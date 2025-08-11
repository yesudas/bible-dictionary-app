<?php
// sitemap.php

// Path to Dictionary.json
$jsonFile = __DIR__ . '/data/Dictionary.json';
$sitemapFile = __DIR__ . '/sitemap.xml';

// Read and decode JSON
$json = file_get_contents($jsonFile);
if ($json === false) {
    die('Could not read Dictionary.json');
}
$data = json_decode($json, true);
if (!$data || !isset($data['words'])) {
    die('Invalid Dictionary.json');
}

// XML header
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// Base URL
$baseUrl = 'http://wordofgod.in/bibledictionary/tamil-bible-dictionary/#/';

// Today's date in YYYY-MM-DD format
$today = date('Y-m-d');

// Add each word as a URL
foreach ($data['words'] as $word) {
    // URL encode the word for the link
    $wordUrl = rawurlencode($word['word']);
    $loc = $baseUrl . $wordUrl . '/' . $word['id'];
    $xml .= "  <url>\n";
    $xml .= "    <loc>{$loc}</loc>\n";
    $xml .= "    <lastmod>{$today}</lastmod>\n";
    $xml .= "  </url>\n";
}

$xml .= '</urlset>';

// Write to sitemap.xml
file_put_contents($sitemapFile, $xml);

// Output success message
echo json_encode(['success' => true, 'message' => 'sitemap.xml created', 'count' => count($data['words'])]);
?>