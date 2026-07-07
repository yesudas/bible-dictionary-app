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
 * Run: php i.php
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

if (!is_dir($indexDir) && !mkdir($indexDir, 0755, true) && !is_dir($indexDir)) {
    fwrite(STDERR, "ERROR: unable to create index directory: $indexDir\n");
    exit(1);
}

// word => list of dictionary names that contain it, in $dictionaries order
$wordDictionaries = [];

foreach ($dictionaries as $dictionary) {
    $dataDir = $baseDir . '/' . $dictionary . '/data';
    if (!is_dir($dataDir)) {
        echo "WARNING: data folder not found for $dictionary ($dataDir)\n";
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
    echo "Scanned $dictionary: $wordCount words\n";
}

echo 'Total unique words across all dictionaries: ' . count($wordDictionaries) . "\n";

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

echo "Wrote $written word index files to $indexDir\n";
