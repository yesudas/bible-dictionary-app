<?php
include 'counter.php';
include 'version.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Bible Dictionaries</title>
    <meta name="description" content="About the Word of God team behind these free Bible dictionaries, Bible apps, and Christian resources at WordOfGod.in.">

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
            <h1><a href="index.php">Bible Dictionaries</a></h1>
            <p class="header-kicker">WordOfGod.in</p>
        </div>
        <button id="installAppBtn">📲 Install App</button>
    </div>
</header>

<main>
    <a class="back-link" href="index.php">&laquo; Back to Dictionaries</a>

    <section class="about-hero">
        <h1>About Us</h1>
        <p class="about-lede">We are the <strong>Word of God Team</strong>, www.WordOfGod.in &mdash; India's first media ministry, started in 2003.</p>
    </section>

    <div class="about-stats">
        <div class="stat"><strong>2003</strong><span>Founded</span></div>
        <div class="stat"><strong>140+</strong><span>Team Members</span></div>
        <div class="stat"><strong>190+</strong><span>Books Published</span></div>
        <div class="stat"><strong>0</strong><span>Ads or Donations</span></div>
    </div>

    <div class="about-grid">
        <section class="about-card">
            <h2>Who We Are</h2>
            <p>We are more than 140+ team members across India &mdash; a self-funded, non-profit, inter-denominational team.</p>
            <p>We do not collect offerings or donations, and we do not show ads in any of our websites, apps, or YouTube videos.</p>
            <p>We do not reserve copyright &mdash; all our content is free of cost, as per Matt 10:8, always!</p>
        </section>

        <section class="about-card">
            <h2>Our Vision</h2>
            <blockquote>
                All Things are From Jesus, By Jesus and For Jesus &mdash; Rom 11:36<br>
                Freely you have Received; Freely Give &mdash; Matt 10:8
            </blockquote>
        </section>

        <section class="about-card">
            <h2>Our Journey</h2>
            <p>We started our media journey by creating mobile Bible apps for Java, Symbian, and China-made mobiles.</p>
            <p>Our contributions continued to counsel, teach, train, and equip the church, youth, Bible translators, tribal field workers, and missionary organizations in various kinds of media ministries.</p>
            <p>We have supported multiple languages in India, including tribal languages, and continue to do so.</p>
            <p>Today, we are the top and largest resource generator in the public domain from Christianity in India &mdash; 100s of GBs of data downloaded every week.</p>
        </section>

        <section class="about-card">
            <h2>Our Books</h2>
            <p>We have published more than 190 books as of Sep 2025, all available in the public domain, free of cost.</p>
            <p>Download them from <a href="https://www.wordofgod.in/" target="_blank" rel="noopener">www.WordOfGod.in</a> or our Telegram channels below.</p>
            <p>Some are available as printed books on request, via <a href="https://notionpress.com/author/345982" target="_blank" rel="noopener">Bible Minutes at NotionPress</a>. All our books are royalty-free, priced at production cost &mdash; you can contact us for discount coupon codes.</p>
        </section>

        <section class="about-card">
            <h2>Contact Us</h2>
            <ul class="contact-list">
                <li><span class="contact-label">WhatsApp</span><span><a href="https://wa.me/917676505599" target="_blank" rel="noopener">+91 7676 50 5599</a></span></li>
                <li><span class="contact-label">Email</span><span><a href="mailto:wordofgod@wordofgod.in">wordofgod@wordofgod.in</a></span></li>
                <li><span class="contact-label">Website</span><span><a href="https://www.wordofgod.in/" target="_blank" rel="noopener">www.WordOfGod.in</a></span></li>
                <li><span class="contact-label">YouTube</span><span><a href="https://www.youtube.com/c/BibleMinutes" target="_blank" rel="noopener">Bible Minutes</a></span></li>
            </ul>
        </section>

        <section class="about-card">
            <h2>Telegram Channels</h2>
            <ul class="chip-list">
                <li><a href="https://t.me/TamilChristianPDFs" target="_blank" rel="noopener">Tamil Christian PDFs</a></li>
                <li><a href="https://t.me/EnglishChristianPDFs" target="_blank" rel="noopener">English Christian PDFs</a></li>
                <li><a href="https://t.me/KannadaChristianPDFs" target="_blank" rel="noopener">Kannada Christian PDFs</a></li>
            </ul>
        </section>

        <section class="about-card about-card-wide">
            <h2>Our Free Christian Apps &amp; Websites</h2>
            <p><a href="https://play.google.com/store/apps/developer?id=Yesudas+Solomon" target="_blank" rel="noopener">Click Here to See Android Apps</a></p>
            <ul class="chip-list">
                <li><a href="index.php">Bible Dictionaries</a></li>
                <li><a href="https://wordofgod.in/bible-concordance/" target="_blank" rel="noopener">Bible Concordance</a></li>
                <li><a href="https://wordofgod.in/bibles/" target="_blank" rel="noopener">Online Bibles</a></li>
                <li><a href="https://wordofgod.in/good-news-collections/" target="_blank" rel="noopener">Good News Collections</a></li>
                <li><a href="https://wordofgod.in/bible-wallpapers/" target="_blank" rel="noopener">Bible Wallpapers</a></li>
                <li><a href="https://wordofgod.in/bible-devotions/" target="_blank" rel="noopener">Bible Devotions</a></li>
                <li><a href="https://wordofgod.in/bible-app-modules/" target="_blank" rel="noopener">Bible App Modules</a></li>
                <li><a href="https://wordofgod.in/wog/word-of-god-வெளியீடுகள்-download-all-our-published-materials-free-of-cost/" target="_blank" rel="noopener">All Our Resources</a></li>
                <li><a href="https://wordofgod.in/" target="_blank" rel="noopener">Free Christian Resources</a></li>
            </ul>
        </section>
    </div>
</main>

<footer class="site-footer">
    <p class="verse">No Copyright, &ldquo;Freely you have received; Freely give&rdquo; &mdash; Matt 10:8</p>
    <p class="visitors">Visitors: <?php echo htmlspecialchars($visitors2); ?></p>
</footer>

<script type="text/javascript" src="assets/js/app.js?v=<?php echo $version; ?>"></script>
</body>
</html>
