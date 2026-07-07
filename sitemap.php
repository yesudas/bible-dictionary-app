<?php
/**
 * sitemap.php (parent) - Regenerates every dictionary's own sitemap.xml
 * (by running each dictionary's sitemap.php as its own process, so one
 * dictionary's failure can't take down the others), then writes a master
 * sitemap.xml here as a <sitemapindex> pointing at all of them.
 *
 * Run: php sitemap.php
 */

$dictionaries = [
    'eastons-bible-dictionary',
    'smiths-bible-dictionary',
    'tamil-bible-dictionary',
    'சத்திய-வேதாகமப்-பெயர்-அகராதி',
    'ஸ்ட்ராங்க்ஸ்-எபிரேய-அகராதி',
    'ஸ்ட்ராங்க்ஸ்-கிரேக்க-அகராதி',
];

$baseDir = __DIR__;
$phpBinary = PHP_BINARY ?: 'php';
$results = [];

foreach ($dictionaries as $dictionary) {
    $script = $baseDir . '/' . $dictionary . '/sitemap.php';
    if (!file_exists($script)) {
        $results[$dictionary] = ['success' => false, 'message' => 'sitemap.php not found'];
        echo "SKIP: $dictionary (sitemap.php not found)\n";
        continue;
    }

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

    if ($decoded && !empty($decoded['success'])) {
        echo "OK: $dictionary ({$decoded['count']} words)\n";
    } else {
        echo "FAILED: $dictionary - " . ($results[$dictionary]['message'] ?? 'unknown error') . "\n";
    }
}

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

echo "\nWrote master sitemap.xml to $baseDir/sitemap.xml\n";
echo json_encode(['success' => true, 'message' => 'Master sitemap.xml created', 'dictionaries' => $results], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
