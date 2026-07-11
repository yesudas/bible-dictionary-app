<?php
/**
 * i.php - Builds a word -> dictionaries index.
 *
 * For every word (a filename minus ".json") found under each dictionary's
 * data/ folder, writes one file to index/words/{word}.json listing every
 * dictionary that has an entry for that word, e.g.:
 *
 *   {
 *     "word": "அகசியா",
 *     "supportedDictionaries": [
 *       "tamil-bible-dictionary",
 *       "சத்திய-வேதாகமப்-பெயர்-அகராதி"
 *     ]
 *   }
 *
 * Run: php i.php   (or open in a browser)
 */

$dictionaries = [
    "eastons-bible-dictionary",
    "smiths-bible-dictionary",
    "tamil-bible-dictionary",
    "சத்திய-வேதாகமப்-பெயர்-அகராதி",
    "ஸ்ட்ராங்க்ஸ்-எபிரேய-அகராதி",
    "ஸ்ட்ராங்க்ஸ்-கிரேக்க-அகராதி",
];

$baseDir = __DIR__;
$indexDir = $baseDir . '/index/words';

// CLI gets ANSI colors (only when writing to an interactive terminal, so
// piped/log output like `php i.php > build.log` stays plain and
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
        . "<title>Building Word Index</title>"
        . "<style>body{background:#0f1115;color:#d4d4d8;}"
        . "pre{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:14px;"
        . "line-height:1.5;padding:24px;white-space:pre-wrap;word-break:break-word;}"
        . "</style></head><body><pre>";
}

$startTime = microtime(true);

$rule = str_repeat('─', 52);
out("\nBuilding cross-dictionary word index\n$rule\n\n", 'bold', $isCli, $isTty);

if (!is_dir($indexDir) && !mkdir($indexDir, 0755, true) && !is_dir($indexDir)) {
    $message = "✗ ERROR: unable to create index directory: $indexDir\n";
    if (defined('STDERR')) {
        fwrite(STDERR, $message);
    } else {
        out($message, 'red', $isCli, $isTty);
    }
    if (!$isCli) {
        echo "</pre></body></html>";
    }
    exit(1);
}

// word => list of dictionary names that contain it, in $dictionaries order
$wordDictionaries = [];
$labelWidth = 44;
$scannedCount = 0;

foreach ($dictionaries as $dictionary) {
    $dataDir = $baseDir . '/' . $dictionary . '/data';
    $dots = str_repeat('.', max(1, $labelWidth - mb_strlen($dictionary)));

    if (!is_dir($dataDir)) {
        out("  ⚠ $dictionary $dots MISSING data/ folder\n", 'yellow', $isCli, $isTty);
        continue;
    }

    $files = glob($dataDir . '/*.json');
    $wordCount = 0;
    foreach ($files as $file) {
        $filename = basename($file);
        if ($filename === 'Dictionary.json') {
            continue;
        }

        $word = substr($filename, 0, -strlen('.json'));
        if ($word === '') {
            continue;
        }

        if (!isset($wordDictionaries[$word])) {
            $wordDictionaries[$word] = [];
        }
        if (!in_array($dictionary, $wordDictionaries[$word], true)) {
            $wordDictionaries[$word][] = $dictionary;
        }
        $wordCount++;
    }
    $scannedCount++;

    $countText = number_format($wordCount) . ' words';
    out('  ✓ ', 'green', $isCli, $isTty);
    out("$dictionary $dots ", null, $isCli, $isTty);
    out("$countText\n", 'bold', $isCli, $isTty);
}

out("\n$rule\n", null, $isCli, $isTty);

/**
 * Hand-formats the index entry as 2-space-indented JSON (matches the
 * project's existing index file style) instead of relying on
 * json_encode()'s fixed 4-space JSON_PRETTY_PRINT indent.
 */
function buildWordIndexJson($word, array $supportedDictionaries)
{
    $lines = [];
    $lines[] = '{';
    $lines[] = '  "word": ' . json_encode($word, JSON_UNESCAPED_UNICODE) . ',';
    $lines[] = '  "supportedDictionaries": [';

    $last = count($supportedDictionaries) - 1;
    foreach ($supportedDictionaries as $i => $dictionary) {
        $comma = ($i === $last) ? '' : ',';
        $lines[] = '    ' . json_encode($dictionary, JSON_UNESCAPED_UNICODE) . $comma;
    }

    $lines[] = '  ]';
    $lines[] = '}';

    return implode("\n", $lines) . "\n";
}

$written = 0;
foreach ($wordDictionaries as $word => $supportedDictionaries) {
    $json = buildWordIndexJson($word, $supportedDictionaries);
    file_put_contents($indexDir . '/' . $word . '.json', $json);
    $written++;
}

$elapsed = number_format(microtime(true) - $startTime, 2);

out("Summary\n", 'bold', $isCli, $isTty);
out('  Dictionaries scanned : ', null, $isCli, $isTty);
out("$scannedCount\n", 'bold', $isCli, $isTty);
out('  Unique words indexed : ', null, $isCli, $isTty);
out(number_format(count($wordDictionaries)) . "\n", 'bold', $isCli, $isTty);
out('  Index files written  : ', null, $isCli, $isTty);
out(number_format($written) . "\n", 'bold', $isCli, $isTty);
out("  Elapsed time         : {$elapsed}s\n", null, $isCli, $isTty);
out("$rule\n\n", null, $isCli, $isTty);

if (!$isCli) {
    echo "</pre></body></html>";
}
