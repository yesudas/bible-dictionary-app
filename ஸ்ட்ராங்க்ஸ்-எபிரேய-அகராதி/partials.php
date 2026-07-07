<?php
/**
 * partials.php - Shared header/footer + small helpers used by both
 * index.php (word list / word detail) and about.php.
 */

/**
 * Returns the display text for a dictionary entry: its "text" field if
 * present (e.g. a future Strong's-style entry with "word": "H1", "text":
 * "H1 - אָב - ..."), otherwise its "word" field.
 */
function displayText($item) {
    return $item['text'] ?? ($item['word'] ?? '');
}

/**
 * Capitalizes only the first letter, and only if it's a lowercase ASCII
 * letter -- the dictionary's own JSON "word"/"text" values already carry
 * their correct original casing, this is just a safety net (and a no-op
 * for non-English scripts, which have no case to begin with).
 */
function capitalizeFirst($text) {
    if ($text === '') {
        return $text;
    }
    $first = $text[0];
    if ($first >= 'a' && $first <= 'z') {
        return strtoupper($first) . substr($text, 1);
    }
    return $text;
}

/**
 * Some dictionaries (Strong's Hebrew/Greek, சத்திய-வேதாகமப்-பெயர்-அகராதி)
 * store their "paragraph" text as real HTML (<strong>, <i>, <span>, and
 * cross-reference links), while others (Easton's, Smith's, Tamil Bible
 * Dictionary) store plain text. Rendering is content-adaptive: if a
 * paragraph contains a "<", it's treated as trusted HTML (this is our own
 * curated dictionary data, not user input) and its legacy cross-reference
 * links are repaired; otherwise it's escaped as plain text.
 */
function renderDefinitionParagraph($paragraph) {
    if (strpos($paragraph, '<') === false) {
        return nl2br(htmlspecialchars($paragraph));
    }
    return fixLegacyCrossReferenceLinks($paragraph);
}

/**
 * The old AngularJS app linked Strong's cross-references as
 * <a href="#/H1479 - גּוּף - gûwph - goof/1479">H1479</a> (a client-side
 * hash route). Rewrites these to <a href="index.php?word=H1479">H1479</a>,
 * reusing the link's own visible text (the clean "H1479"/"G260" slug) so no
 * fragile parsing of the old href's trailing numeric id is needed.
 */
function fixLegacyCrossReferenceLinks($html) {
    return preg_replace('/<a\s+href="#\/[^"]*">([^<]+)<\/a>/u', '<a href="index.php?word=$1">$1</a>', $html);
}

/**
 * Plain-text excerpt of a (possibly HTML) definition, for use in
 * <meta name="description">.
 */
function excerptDefinition($paragraphs, $length = 160) {
    $plain = trim(preg_replace('/\s+/', ' ', strip_tags(implode(' ', $paragraphs))));
    return mb_substr($plain, 0, $length);
}

/**
 * The canonical set of WordOfGod.in resource links, shared by the header
 * nav and the footer (and mirrored in about.php's "Our Websites" section).
 * "Other Dictionaries" is a relative link back to the parent app; the rest
 * are external.
 */
function siteResourceLinks() {
    return [
        ['label' => 'Other Dictionaries', 'href' => '../', 'external' => false],
        ['label' => 'Bible Concordance', 'href' => 'https://wordofgod.in/bible-concordance/', 'external' => true],
        ['label' => 'Online Bibles', 'href' => 'https://wordofgod.in/bibles/', 'external' => true],
        ['label' => 'Good News Collections', 'href' => 'https://wordofgod.in/good-news-collections/', 'external' => true],
        ['label' => 'Bible Wallpapers', 'href' => 'https://wordofgod.in/bible-wallpapers/', 'external' => true],
        ['label' => 'Bible Devotions', 'href' => 'https://wordofgod.in/bible-devotions/', 'external' => true],
        ['label' => 'Bible App Modules', 'href' => 'https://wordofgod.in/bible-app-modules/', 'external' => true],
        ['label' => 'All Our Resources', 'href' => 'https://wordofgod.in/wog/word-of-god-வெளியீடுகள்-download-all-our-published-materials-free-of-cost/', 'external' => true],
        ['label' => 'Free Christian Resources', 'href' => 'https://wordofgod.in/', 'external' => true],
    ];
}

function renderLink($link) {
    $attrs = $link['external'] ? ' target="_blank" rel="noopener"' : '';
    return '<a href="' . htmlspecialchars($link['href']) . '"' . $attrs . '>' . htmlspecialchars($link['label']) . '</a>';
}

/* ========================================================================
 * Shared header / footer
 * ==================================================================== */
function renderHeader($dictionaryTitle, $pageTitle, $metaDescription, $version) {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <?php if ($metaDescription !== ''): ?>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <?php endif; ?>

    <link rel="manifest" href="manifest.json?v=<?php echo $version; ?>">
    <meta name="theme-color" content="#173f36">
    <link rel="icon" href="assets/images/icon-192.png">
    <link rel="apple-touch-icon" href="assets/images/icon-192.png">

    <link rel="stylesheet" type="text/css" href="assets/css/style.css?v=<?php echo $version; ?>">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-8ZYHRZG9B8"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-8ZYHRZG9B8');
    </script>
</head>
<body>

<header class="site-header">
    <div class="header-top">
        <h1><a href="index.php"><?php echo htmlspecialchars($dictionaryTitle); ?></a></h1>
        <div class="header-actions">
            <button id="installAppBtn">📲 Install App</button>
            <button id="navToggle" aria-label="Toggle menu" aria-expanded="false">☰</button>
        </div>
    </div>
    <nav class="site-nav" id="siteNav">
        <?php foreach (siteResourceLinks() as $link): ?>
            <?php echo renderLink($link); ?>
        <?php endforeach; ?>
        <a href="about.php">About Us</a>
    </nav>
</header>
    <?php
}

function renderFooter($dictionaryTitle, $dictionaryAuthor, $totalWords, $visitors2, $version) {
    ?>
<footer class="site-footer">
    <p class="dictionary-info">
        <strong><?php echo htmlspecialchars($dictionaryTitle); ?></strong><br>
        by <?php echo htmlspecialchars($dictionaryAuthor); ?><br>
        <?php echo number_format($totalWords); ?> words
    </p>
    <p class="verse">No Copyright, &ldquo;Freely you have received; Freely give&rdquo; &mdash; Matt 10:8</p>
    <p class="footer-links">
        <?php echo implode(' | ', array_map('renderLink', siteResourceLinks())); ?>
    </p>
    <p class="visitors">Visitors: <?php echo htmlspecialchars($visitors2); ?></p>
    <a href="bot.php" class="visually-hidden" aria-hidden="true" tabindex="-1">.</a>
</footer>

<script type="text/javascript" src="assets/js/app.js?v=<?php echo $version; ?>"></script>
</body>
</html>
    <?php
}
