<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getDictionaries':
        getDictionaries();
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}

/**
 * GET api.php?action=getDictionaries&word=<tamil word>
 *
 * Example: api.php?action=getDictionaries&word=ஆரோனாலும்
 * Returns:
 *   {
 *     "dictionaries": [
 *       {"slug": "ஆரோன்", "dictionary": "tamil-bible-dictionary"},
 *       {"slug": "ஆரோன்", "dictionary": "சத்திய-வேதாகமப்-பெயர்-அகராதி"}
 *     ]
 *   }
 * Each entry's "slug" is the filename to fetch from that entry's own
 * "dictionary", e.g. {dictionary}/data/{slug}.json -- pairing them
 * per-entry (rather than one shared slug for a whole dictionary list)
 * keeps this correct even in the rare case where a single inflected form
 * resolves to more than one distinct dictionary word, each potentially
 * spelled/slugged differently in different dictionaries.
 *
 * 1. Finds the given word's starting "letter" (base character, plus the
 *    following vowel sign/virama if there is one) and looks it up in
 *    index/letters/{letter}.txt -- an "inflected_form=dictionary_word"
 *    mapping file -- to resolve the actual dictionary word, e.g.
 *    ஆரோனாலும் -> ஆரோன். ஆகாகியனாகிய -> ஆகாகியன், ஆகாக்
 * 2. If the word isn't found there (e.g. it's already the exact dictionary
 *    word itself, which never appears as a key in the letters files), it
 *    falls back to trying it as-is against index/words/{word}.json.
 * 3. For every dictionary word resolved this way, reads
 *    index/words/{dictionary_word}.json and emits one {slug, dictionary}
 *    pair per dictionary that supports it.
 */
function getDictionaries() {
    $baseDir = __DIR__;
    $word = trim($_GET['word'] ?? '');

    if ($word === '') {
        echo json_encode(['success' => false, 'error' => 'word parameter required']);
        return;
    }

    $dictionaryWords = resolveDictionaryWords($baseDir, $word);

    $dictionaries = [];
    foreach ($dictionaryWords as $dictionaryWord) {
        foreach (lookupSupportedDictionaries($baseDir, $dictionaryWord) as $dictionary) {
            $pair = ['slug' => $dictionaryWord, 'dictionary' => $dictionary];
            if (!in_array($pair, $dictionaries, true)) {
                $dictionaries[] = $pair;
            }
        }
    }

    echo json_encode(['dictionaries' => $dictionaries], JSON_UNESCAPED_UNICODE);
}

/**
 * Resolves a possibly-inflected word to one or more actual dictionary words
 * via index/letters/{letter}.txt, falling back to treating $word itself as
 * the dictionary word if no mapping is found.
 */
function resolveDictionaryWords($baseDir, $word) {
    $letter = startingLetter($word);
    $letterFile = $baseDir . '/index/letters/' . basename($letter) . '.txt';

    $dictionaryWords = [];
    if ($letter !== '' && file_exists($letterFile)) {
        $lines = file($letterFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }
            $key = substr($line, 0, $pos);
            if ($key !== $word) {
                continue;
            }
            $value = substr($line, $pos + 1);
            if (!in_array($value, $dictionaryWords, true)) {
                $dictionaryWords[] = $value;
            }
        }
    }

    // Fall back to treating the input itself as an exact dictionary word.
    // index/words/*.json filenames follow each dictionary's own casing
    // convention (lowercase for English -- e.g. aaron.json -- but
    // unchanged for Strong's IDs -- H1.json -- and Tamil, which has no
    // case at all), so try a few case variants rather than assuming the
    // caller's casing happens to match the file on disk. The real on-disk
    // filename (not just the candidate string) is what gets returned as
    // the slug, verified via wordsIndexFilenames() rather than
    // file_exists() -- file_exists() would silently accept a
    // mismatched-case candidate on a case-insensitive filesystem (macOS's
    // default), returning the wrong-cased slug even though it happens to
    // "find" the file locally; a case-sensitive production server would
    // simply fail to find it.
    if (empty($dictionaryWords)) {
        $candidates = array_unique([$word, mb_strtolower($word), mb_strtoupper($word)]);
        $filenames = wordsIndexFilenames($baseDir);
        foreach ($candidates as $candidate) {
            $filename = basename($candidate) . '.json';
            if (in_array($filename, $filenames, true)) {
                $dictionaryWords[] = $candidate;
                break;
            }
        }
    }

    return $dictionaryWords;
}

/**
 * The real, case-exact list of filenames in index/words/ (cached for the
 * lifetime of the request). Used instead of file_exists() when a
 * candidate's exact casing matters, since file_exists() itself can't be
 * trusted to reject a mismatched case on a case-insensitive filesystem.
 */
function wordsIndexFilenames($baseDir) {
    static $filenames = null;
    if ($filenames === null) {
        $filenames = scandir($baseDir . '/index/words') ?: [];
    }
    return $filenames;
}

/**
 * Reads index/words/{dictionaryWord}.json and returns its
 * "supportedDictionaries" list (or an empty array if the file doesn't
 * exist / has no such field).
 */
function lookupSupportedDictionaries($baseDir, $dictionaryWord) {
    $wordFile = $baseDir . '/index/words/' . basename($dictionaryWord) . '.json';
    if (!file_exists($wordFile)) {
        return [];
    }

    $data = json_decode(file_get_contents($wordFile), true);
    return $data['supportedDictionaries'] ?? [];
}

/**
 * The word's first Tamil "letter": a base character, plus the following
 * vowel sign/virama if there is one. Mirrors starting_letter() in
 * generate_mapping_from_bible.py (private-apps/python/bible-dictionary),
 * which is what built the index/letters/*.txt files, e.g.
 * க -> க, கோ -> கோ, ஸ்நானம் -> ஸ்.
 */
function startingLetter($word) {
    static $vowelSigns = ['்', 'ா', 'ி', 'ீ', 'ு', 'ூ', 'ெ', 'ே', 'ை', 'ொ', 'ோ', 'ௌ'];

    $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
    if (!$chars) {
        return '';
    }
    if (count($chars) > 1 && in_array($chars[1], $vowelSigns, true)) {
        return $chars[0] . $chars[1];
    }
    return $chars[0];
}
