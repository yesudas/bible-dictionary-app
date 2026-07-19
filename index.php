<?php
include 'counter.php';
$version = '2026.03';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bible Dictionaries</title>
    <meta name="description" content="Free online Bible dictionaries in English and Tamil, from www.WordOfGod.in">

    <link rel="manifest" href="/bibledictionary/manifest.json?v=<?php echo $version; ?>">
    <meta name="theme-color" content="#173f36">
    <link rel="icon" href="/bibledictionary/assets/images/icon-192.png">
    <link rel="apple-touch-icon" href="/bibledictionary/assets/images/icon-192.png">

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
    <div class="header-inner">
        <div>
            <h1>Bible Dictionaries</h1>
            <p class="header-kicker">WordOfGod.in</p>
        </div>
        <button id="installAppBtn">📲 Install App</button>
    </div>
</header>


<main>
    <div class="container">
        <a class="card" href="சத்திய-வேதாகமப்-பெயர்-அகராதி/">
            <h2>சத்திய வேதாகமப் பெயர் அகராதி</h2>
        </a>
        <a class="card" href="பரிபூரண-பெயர்ப்-பொக்கிஷம்/">
            <h2>பரிபூரண பரிசுத்த வேதாகமப் பெயர்ப் பொக்கிஷம்</h2>
        </a>
        <a class="card" href="ஸ்ட்ராங்க்ஸ்-எபிரேய-அகராதி/">
            <h2>ஸ்ட்ராங்க்ஸ் எபிரேய அகராதி</h2>
        </a>
        <a class="card" href="ஸ்ட்ராங்க்ஸ்-கிரேக்க-அகராதி/">
            <h2>ஸ்ட்ராங்க்ஸ் கிரேக்க அகராதி</h2>
        </a>
        <a class="card" href="bdag3-greek-dictionary/">
            <h2>BDAG3 Greek Dictionary</h2>
        </a>
        <a class="card" href="bdb-t-bible-dictionary/">
            <h2>BDB-T Bible Dictionary</h2>
        </a>
        <a class="card" href="danker-greek-dictionary/">
            <h2>Danker Greek Dictionary</h2>
        </a>
        <a class="card" href="eastons-bible-dictionary/">
            <h2>Easton's Bible Dictionary</h2>
        </a>
        <a class="card" href="gesenius-hebrew-dictionary/">
            <h2>Gesenius Hebrew Dictionary</h2>
        </a>
        <a class="card" href="gr-en-ls-greek-dictionary/">
            <h2>Gr-En-LS Greek Dictionary</h2>
        </a>
        <a class="card" href="he-en-b-hebrew-dictionary/">
            <h2>He-En-B Hebrew Dictionary</h2>
        </a>
        <a class="card" href="lxx-green-dictionary/">
            <h2>LXX Green Dictionary</h2>
        </a>
        <a class="card" href="mlsj-greek-dictionary/">
            <h2>MLSJ Greek Dictionary</h2>
        </a>
        <a class="card" href="naves-bible-dictionary/">
            <h2>Nave's Bible Dictionary</h2>
        </a>
        <a class="card" href="smiths-bible-dictionary/">
            <h2>Smith's Bible Dictionary</h2>
        </a>
        <a class="card" href="strongs-bible-dictionary/">
            <h2>Strong's Bible Dictionary</h2>
        </a>
        <a class="card" href="tamil-bible-dictionary-by-truth/">
            <h2>Tamil Bible Dictionary by Truth</h2>
        </a>
        <a class="card" href="thompson-chain-reference/">
            <h2>Thompson Chain Reference</h2>
        </a>
    </div>
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
