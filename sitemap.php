<?php
/**
 * sitemap.php (parent) - Regenerates every dictionary's own sitemap.xml
 * (by running each dictionary's sitemap.php as its own process, so one
 * dictionary's failure can't take down the others), then writes a master
 * sitemap.xml here as a <sitemapindex> pointing at all of them.
 *
 * Dictionaries are auto-discovered: any immediate subfolder of this
 * directory containing a sitemap.php is treated as a dictionary, so a
 * newly added dictionary is picked up without editing this file.
 *
 * Run: php sitemap.php   (or open in a browser)
 */

$baseDir = __DIR__;
$phpBinary = PHP_BINARY ?: 'php';
$results = [];

// CLI gets plain text (ANSI colors only when writing to an interactive
// terminal, so piped/log output like `php sitemap.php > build.log` stays
// grep-friendly). Any other SAPI (php-fpm, apache2handler, the built-in
// `cli-server` dev server, ...) is assumed to be a browser and gets a real
// styled HTML page instead, since terminal escape codes render as garbage
// there.
$isCli = PHP_SAPI === 'cli';
$isTty = $isCli && defined('STDOUT') && function_exists('posix_isatty') && posix_isatty(STDOUT);

const ANSI_CODES = ['bold' => '1', 'green' => '32', 'yellow' => '33', 'red' => '31'];
const CSS_STYLES = [
    'bold' => 'font-weight:700',
    'green' => 'color:#2f9e44;font-weight:600',
    'yellow' => 'color:#f08c00;font-weight:600',
    'red' => 'color:#e5484d;font-weight:700',
];

/**
 * Prints $text, styled with $style ('bold'|'green'|'yellow'|'red'|null).
 * ANSI escapes in a TTY, an inline-styled <span> in a browser, plain text
 * otherwise (piped CLI output).
 */
function out($text, $style, $isCli, $isTty)
{
    if ($isTty && $style) {
        echo "\033[" . ANSI_CODES[$style] . "m{$text}\033[0m";
        return;
    }
    if ($isCli) {
        echo $text;
        return;
    }
    $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    echo $style ? '<span style="' . CSS_STYLES[$style] . '">' . $escaped . '</span>' : $escaped;
}

if (!$isCli) {
    echo "<!DOCTYPE html>\n<html lang=\"en\"><head><meta charset=\"UTF-8\">"
        . "<title>Building Sitemaps</title>"
        . "<style>body{background:#0f1115;color:#d4d4d8;}"
        . "pre{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:14px;"
        . "line-height:1.5;padding:24px;white-space:pre-wrap;word-break:break-word;}"
        . "</style></head><body><pre>";
}

$startTime = microtime(true);

$rule = str_repeat('─', 52);
out("\nBuilding dictionary sitemaps\n$rule\n\n", 'bold', $isCli, $isTty);

$dictionaries = [];
foreach (scandir($baseDir) as $entry) {
    if ($entry === '.' || $entry === '..') {
        continue;
    }
    if (is_dir($baseDir . '/' . $entry) && file_exists($baseDir . '/' . $entry . '/sitemap.php')) {
        $dictionaries[] = $entry;
    }
}
sort($dictionaries, SORT_STRING);

$labelWidth = 44;
$failedCount = 0;

foreach ($dictionaries as $dictionary) {
    $script = $baseDir . '/' . $dictionary . '/sitemap.php';

    // Use array-form proc_open (bypasses the shell entirely) instead of
    // shell_exec()+escapeshellarg(), which corrupts UTF-8 folder names like
    // the Tamil dictionary paths when the server's locale is not UTF-8 aware.
    $descriptorSpec = [
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ];
    $output = '';
    $process = proc_open([$phpBinary, $script], $descriptorSpec, $pipes);
    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
    }
    $decoded = json_decode(trim((string) $output), true);
    $results[$dictionary] = $decoded ?? ['success' => false, 'message' => 'Unexpected output: ' . trim((string) $output)];

    $dots = str_repeat('.', max(1, $labelWidth - mb_strlen($dictionary)));

    if ($decoded && !empty($decoded['success'])) {
        $countText = number_format($decoded['count']) . ' words';
        out('  ✓ ', 'green', $isCli, $isTty);
        out("$dictionary $dots ", null, $isCli, $isTty);
        out("$countText\n", 'bold', $isCli, $isTty);
    } else {
        $failedCount++;
        $message = $results[$dictionary]['message'] ?? 'unknown error';
        out('  ✗ ', 'red', $isCli, $isTty);
        out("$dictionary $dots ", null, $isCli, $isTty);
        out("FAILED: $message\n", 'red', $isCli, $isTty);
    }
}

out("\n$rule\n", null, $isCli, $isTty);

// Build the master sitemap index
$today = date('Y-m-d');
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

foreach ($dictionaries as $dictionary) {
    if (empty($results[$dictionary]['success'])) {
        continue;
    }
    $loc = 'https://wordofgod.in/bibledictionary/' . rawurlencode($dictionary) . '/sitemap.xml';
    $xml .= "  <sitemap>\n";
    $xml .= "    <loc>{$loc}</loc>\n";
    $xml .= "    <lastmod>{$today}</lastmod>\n";
    $xml .= "  </sitemap>\n";
}

$xml .= '</sitemapindex>';

file_put_contents($baseDir . '/sitemap.xml', $xml);

$elapsed = number_format(microtime(true) - $startTime, 2);

out("Summary\n", 'bold', $isCli, $isTty);
out('  Dictionaries scanned : ', null, $isCli, $isTty);
out(count($dictionaries) . "\n", 'bold', $isCli, $isTty);
out('  Succeeded            : ', null, $isCli, $isTty);
out((count($dictionaries) - $failedCount) . "\n", 'bold', $isCli, $isTty);
if ($failedCount > 0) {
    out('  Failed                : ', null, $isCli, $isTty);
    out("$failedCount\n", 'red', $isCli, $isTty);
}
out('  Master sitemap.xml   : ', null, $isCli, $isTty);
out("$baseDir/sitemap.xml\n", 'bold', $isCli, $isTty);
out("  Elapsed time         : {$elapsed}s\n", null, $isCli, $isTty);
out("$rule\n\n", null, $isCli, $isTty);

if ($isCli) {
    echo json_encode(['success' => true, 'message' => 'Master sitemap.xml created', 'dictionaries' => $results], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "</pre></body></html>";
}
