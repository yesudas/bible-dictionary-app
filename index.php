<?php
include 'counter.php';
include 'version.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bible Dictionaries</title>
    <meta name="description" content="Free online Bible dictionaries in English and Tamil, from www.WordOfGod.in">

    <link rel="manifest" href="/bibledictionary/manifest.json?v=<?php echo $version; ?>">
    <meta name="theme-color" content="#0c2e28">
    <link rel="icon" href="/bibledictionary/assets/images/icon-192.png">
    <link rel="apple-touch-icon" href="/bibledictionary/assets/images/icon-192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,650;9..144,700&family=Noto+Sans+Tamil:wght@400;600;700&family=Sora:wght@400;500;600&display=swap" rel="stylesheet">
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
<body class="home">

<header class="home-topbar">
    <div class="home-topbar-inner">
        <a class="topbar-brand" href="index.php">Bible Dictionaries</a>
        <div class="header-actions">
            <a class="about-link" href="about.php">About Us</a>
            <button id="installAppBtn" type="button">Install App</button>
        </div>
    </div>
</header>

<section class="home-hero" aria-label="Introduction">
    <div class="home-hero-inner">
        <p class="home-brand">WordOfGod.in</p>
        <h1>Bible Dictionaries</h1>
        <p class="home-lede">Free English, Tamil, Greek, Hebrew and other language dictionaries for study, preaching, and everyday reading.</p>
        <div class="home-cta">
            <a class="btn-primary" href="#dictionaries">Browse dictionaries</a>
            <a class="btn-ghost" href="about.php">About the ministry</a>
        </div>
    </div>
</section>

<main id="dictionaries">
    <section class="dict-section" aria-labelledby="tamil-heading">
        <div class="dict-section-head">
            <h2 id="tamil-heading">Tamil</h2>
            <p>Names, Strong&rsquo;s, and general Tamil Bible dictionaries</p>
        </div>
        <ul class="dict-list">
            <li><a href="சத்திய-வேதாகமப்-பெயர்-அகராதி/"><span class="dict-name">சத்திய வேதாகமப் பெயர் அகராதி</span><span class="dict-meta">Bible names</span></a></li>
            <li><a href="பரிபூரண-பெயர்ப்-பொக்கிஷம்/"><span class="dict-name">பரிபூரண பரிசுத்த வேதாகமப் பெயர்ப் பொக்கிஷம்</span><span class="dict-meta">Bible names</span></a></li>
            <li><a href="ஸ்ட்ராங்க்ஸ்-எபிரேய-அகராதி/"><span class="dict-name">ஸ்ட்ராங்க்ஸ் எபிரேய அகராதி</span><span class="dict-meta">Hebrew &middot; Strong&rsquo;s</span></a></li>
            <li><a href="ஸ்ட்ராங்க்ஸ்-கிரேக்க-அகராதி/"><span class="dict-name">ஸ்ட்ராங்க்ஸ் கிரேக்க அகராதி</span><span class="dict-meta">Greek &middot; Strong&rsquo;s</span></a></li>
            <li><a href="tamil-bible-dictionary-by-truth/"><span class="dict-name">Tamil Bible Dictionary by Truth</span><span class="dict-meta">General</span></a></li>
        </ul>
    </section>

    <section class="dict-section" aria-labelledby="english-heading">
        <div class="dict-section-head">
            <h2 id="english-heading">English</h2>
            <p>Classic Bible dictionaries and topical references</p>
        </div>
        <ul class="dict-list">
            <li><a href="eastons-bible-dictionary/"><span class="dict-name">Easton&rsquo;s Bible Dictionary</span><span class="dict-meta">General</span></a></li>
            <li><a href="smiths-bible-dictionary/"><span class="dict-name">Smith&rsquo;s Bible Dictionary</span><span class="dict-meta">General</span></a></li>
            <li><a href="naves-bible-dictionary/"><span class="dict-name">Nave&rsquo;s Bible Dictionary</span><span class="dict-meta">Topical</span></a></li>
            <li><a href="thompson-chain-reference/"><span class="dict-name">Thompson Chain Reference</span><span class="dict-meta">Topical</span></a></li>
            <li><a href="strongs-bible-dictionary/"><span class="dict-name">Strong&rsquo;s Bible Dictionary</span><span class="dict-meta">Hebrew &amp; Greek</span></a></li>
        </ul>
    </section>

    <section class="dict-section" aria-labelledby="lexicon-heading">
        <div class="dict-section-head">
            <h2 id="lexicon-heading">Greek &amp; Hebrew Lexicons</h2>
            <p>Scholarly lexicons for original-language study</p>
        </div>
        <ul class="dict-list">
            <li><a href="bdag3-greek-dictionary/"><span class="dict-name">BDAG3 Greek Dictionary</span><span class="dict-meta">Greek</span></a></li>
            <li><a href="danker-greek-dictionary/"><span class="dict-name">Danker Greek Dictionary</span><span class="dict-meta">Greek</span></a></li>
            <li><a href="mlsj-greek-dictionary/"><span class="dict-name">MLSJ Greek Dictionary</span><span class="dict-meta">Greek</span></a></li>
            <li><a href="gr-en-ls-greek-dictionary/"><span class="dict-name">Gr-En-LS Greek Dictionary</span><span class="dict-meta">Liddell-Scott</span></a></li>
            <li><a href="lxx-green-dictionary/"><span class="dict-name">LXX Green Dictionary</span><span class="dict-meta">Septuagint</span></a></li>
            <li><a href="bdb-t-bible-dictionary/"><span class="dict-name">BDB-T Bible Dictionary</span><span class="dict-meta">Hebrew</span></a></li>
            <li><a href="gesenius-hebrew-dictionary/"><span class="dict-name">Gesenius Hebrew Dictionary</span><span class="dict-meta">Hebrew</span></a></li>
            <li><a href="he-en-b-hebrew-dictionary/"><span class="dict-name">He-En-B Hebrew Dictionary</span><span class="dict-meta">Hebrew + English</span></a></li>
        </ul>
    </section>
</main>

<footer class="site-footer">
    <p class="verse">No Copyright, &ldquo;Freely you have received; Freely give&rdquo; &mdash; Matt 10:8</p>
    <p class="footer-links">
        <a href="https://wordofgod.in/bible-concordance/" target="_blank" rel="noopener">Bible Concordance</a> |
        <a href="https://wordofgod.in/bibles/" target="_blank" rel="noopener">Online Bibles</a> |
        <a href="https://wordofgod.in/good-news-collections/" target="_blank" rel="noopener">Good News Collections</a> |
        <a href="https://wordofgod.in/bible-wallpapers/" target="_blank" rel="noopener">Bible Wallpapers</a> |
        <a href="https://wordofgod.in/bible-devotions/" target="_blank" rel="noopener">Bible Devotions</a> |
        <a href="https://wordofgod.in/bible-app-modules/" target="_blank" rel="noopener">Bible App Modules</a> |
        <a href="https://wordofgod.in/wog/word-of-god-வெளியீடுகள்-download-all-our-published-materials-free-of-cost/" target="_blank" rel="noopener">All Our Resources</a> |
        <a href="https://wordofgod.in/" target="_blank" rel="noopener">Free Christian Resources</a>
    </p>
    <p class="visitors">Visitors: <?php echo htmlspecialchars($visitors2); ?></p>
</footer>

<script type="text/javascript" src="assets/js/app.js?v=<?php echo $version; ?>"></script>
</body>
</html>
