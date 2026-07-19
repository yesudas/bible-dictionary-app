<?php
require 'partials.php';
include 'counter.php';
$version = '2026.03';

$dataDir = __DIR__ . '/data';
$dictionary = json_decode(file_get_contents($dataDir . '/Dictionary.json'), true);
$dictionaryTitle = $dictionary['title'] ?? 'Bible Dictionary';
$dictionaryAuthor = $dictionary['author'] ?? 'Unknown Author';
$totalWords = count($dictionary['words'] ?? []);

renderHeader(
    $dictionaryTitle,
    'About Us - ' . $dictionaryTitle,
    'About the Word of God team behind ' . $dictionaryTitle . ', and our other free Christian resources.',
    $version
);
?>

<main>
    <a class="back-link" href="index.php">&laquo; Back to word list</a>

    <div class="word-detail about-page">
        <h1 class="word-title">About Us</h1>

        <p>We are from Word of God Team, www.WordOfGod.in</p>
        <p>We are the <strong>First Media Ministry in India</strong> started in 2003.</p>
        <p>We are more than 140+ team members across India.</p>
        <p>It is an self funded, non-profit, inter-denominational team.</p>
        <p>We do not collect offerings and donations.</p>
        <p>We do not show ads in any of our websites, apps, YouTube videos</p>
        <p>We do not reserve copyright, all our contents are free of cost as per Matt 10:8, always!</p>

        <p>We started our media journey by creating Mobile Bible Apps for Java, Symbian and China made mobiles.</p>
        <p>Our contributions continued to counsel, teach, train, equip the church, youth, Bible Translators, Tribal field workers, Missionary organizations in various kinds of media ministries.</p>
        <p>We have supported multiple languages in india, including tribal languages, continuing the same.</p>

        <p>Now, we are the top and largest resource generator in public domain from Christianity in India.</p>
        <p>100s of GBs of data is getting downloaded every week from us.</p>

        <h3>Our Vision:</h3>
        <p>All Things are From Jesus, By Jesus and For Jesus - Rom 11:36<br>Freely you have Received; Freely Give - Matt 10:8</p>

        <h3>Our Free Christian Apps</h3>
        <p><a href="https://play.google.com/store/apps/developer?id=Yesudas+Solomon" target="_blank" rel="noopener">Click Here to See Android Apps</a></p>

        <h3>Our Websites:</h3>
        <p>
            <?php foreach (siteResourceLinks() as $link): ?>
                <?php echo renderLink($link); ?><br>
            <?php endforeach; ?>
        </p>

        <h3>Contact Us</h3>
        <p>
            <strong>WhatsApp: </strong>
            <span>+91 7676 50 5599</span>
        </p>
        <p>
            <strong>Email: </strong>
            <span>wordofgod@wordofgod.in</span>
        </p>
        <p>
            <strong>Website: </strong>
            <span><a href="https://www.wordofgod.in/" target="_blank" rel="noopener">www.WordOfGod.in</a></span>
        </p>
        <p>
            <strong>YouTube: </strong>
            <span><a href="https://www.youtube.com/c/BibleMinutes" target="_blank" rel="noopener">Bible Minutes</a></span>
        </p>

        <h3>Telegram Channels</h3>
        <p><a href="https://t.me/TamilChristianPDFs" target="_blank" rel="noopener">Tamil Christian PDFs</a></p>
        <p><a href="https://t.me/EnglishChristianPDFs" target="_blank" rel="noopener">English Christian PDFs</a></p>
        <p><a href="https://t.me/KannadaChristianPDFs" target="_blank" rel="noopener">Kannada Christian PDFs</a></p>

        <h3>Our Books</h3>
        <p>We have published more than 190 books as of Sep 2025. All of them are available in Public Domain, free of cost.</p>
        <p>You can download them from our site <a href="https://www.wordofgod.in/" target="_blank" rel="noopener">www.WordOfGod.in</a> or our Telegram Channels listed above.</p>
        <p>Some of them are available as printed books based on the request from users at <a href="https://notionpress.com/author/345982" target="_blank" rel="noopener">Bible Minutes at NotionPress</a></p>
        <p>All our books are royalty free, priced at production cost. We do not expect/rely on the profit of online books.<br>Majority of the cost goes to NotionPress as they are the publishers for the printed books. Try using coupon codes as BIBLEMINUTES, BIBLEMINUTES1, BIBLEMINUTES2, etc whichever works.</p>
    </div>
</main>

<?php
renderFooter($dictionaryTitle, $dictionaryAuthor, $totalWords, $visitors2, $version);
