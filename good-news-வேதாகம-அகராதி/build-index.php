<?php
/**
 * build-index.php - Regenerates data/Dictionary.json from the actual files
 * in data/ (the old Dictionary.json predates the a.json/aaron.json-style
 * renaming and still points at stale "Word_N.json" filenames).
 *
 * Run whenever words are added/removed/renamed under data/:
 *   php build-index.php
 */

$dataDir = __DIR__ . '/data';
$dictionaryFile = $dataDir . '/Dictionary.json';

$existingTitle = 'Bible Dictionary';
$existingAuthor = 'Unknown Author';
if (file_exists($dictionaryFile)) {
    $existing = json_decode(file_get_contents($dictionaryFile), true);
    $existingTitle = $existing['title'] ?? $existingTitle;
    $existingAuthor = $existing['author'] ?? $existingAuthor;
}

$words = [];
foreach (glob($dataDir . '/*.json') as $file) {
    $filename = basename($file);
    if ($filename === 'Dictionary.json') {
        continue;
    }

    $slug = substr($filename, 0, -strlen('.json'));
    $data = json_decode(file_get_contents($file), true);
    $word = trim($data['word'] ?? $slug);

    $words[] = ['word' => $word, 'slug' => $slug];
}

// Strong's-style dictionaries (slugs like "H1", "G23") should browse in
// numeric concordance order, not alphabetically by their (often long,
// composite) display text.
$isStrongsStyle = count($words) > 0 && array_reduce($words, function ($carry, $w) {
    return $carry && preg_match('/^[A-Za-z]+\d+$/', $w['slug']);
}, true);

if ($isStrongsStyle) {
    usort($words, function ($a, $b) {
        return (int) preg_replace('/^\D+/', '', $a['slug']) <=> (int) preg_replace('/^\D+/', '', $b['slug']);
    });
} else {
    usort($words, function ($a, $b) {
        return strcasecmp($a['word'], $b['word']);
    });
}

$dictionary = [
    'title' => $existingTitle,
    'author' => $existingAuthor,
    'words' => $words,
];

file_put_contents($dictionaryFile, json_encode($dictionary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo 'Wrote ' . count($words) . " words to $dictionaryFile\n";
