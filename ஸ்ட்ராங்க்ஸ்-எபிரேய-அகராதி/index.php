<?php
require 'partials.php';
include 'counter.php';
$version = '2026.03';

$dataDir = __DIR__ . '/data';
$dictionary = json_decode(file_get_contents($dataDir . '/Dictionary.json'), true);
$dictionaryTitle = $dictionary['title'] ?? 'Bible Dictionary';
$dictionaryAuthor = $dictionary['author'] ?? 'Unknown Author';
$allWords = $dictionary['words'] ?? [];

$word = trim($_GET['word'] ?? '');

if ($word !== '') {
    renderWordDetail($word, $dataDir, $dictionaryTitle, $dictionaryAuthor, count($allWords), $visitors2, $version);
} else {
    renderWordList($allWords, $dictionaryTitle, $dictionaryAuthor, count($allWords), $visitors2, $version);
}

/* ========================================================================
 * Word listing (search + pagination)
 * ==================================================================== */
function renderWordList($allWords, $dictionaryTitle, $dictionaryAuthor, $totalWords, $visitors2, $version) {
    $query = trim($_GET['q'] ?? '');
    $perPage = 50;
    $page = max(1, intval($_GET['page'] ?? 1));

    if ($query !== '') {
        $filtered = array_values(array_filter($allWords, function ($item) use ($query) {
            return stripos(displayText($item), $query) !== false;
        }));
    } else {
        $filtered = $allWords;
    }

    $total = count($filtered);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $totalPages);
    $pageItems = array_slice($filtered, ($page - 1) * $perPage, $perPage);

    renderHeader($dictionaryTitle, $dictionaryTitle, 'Search and browse ' . $dictionaryTitle . ' online, free.', $version);

    // Full word list (already capitalized), embedded for instant client-side
    // filter-as-you-type -- avoids a server round trip on every keystroke.
    $clientWords = array_map(function ($item) {
        return ['w' => capitalizeFirst(displayText($item)), 's' => $item['slug']];
    }, $allWords);
    ?>

<main>
    <form class="search-box" method="get" action="index.php" id="searchForm">
        <input type="text" name="q" id="searchInput" value="<?php echo htmlspecialchars($query); ?>" placeholder="Type Here to Search the Dictionary" autocomplete="off">
        <a class="clear-search" href="index.php" id="clearSearch" style="<?php echo $query !== '' ? '' : 'display:none;'; ?>">Clear</a>
    </form>

    <div id="wordListContainer" data-per-page="<?php echo $perPage; ?>" data-initial-page="<?php echo $page; ?>">
        <?php if ($query !== ''): ?>
            <p class="result-summary"><?php echo $total; ?> result<?php echo $total === 1 ? '' : 's'; ?> for "<?php echo htmlspecialchars($query); ?>"</p>
        <?php endif; ?>

        <?php if (empty($pageItems)): ?>
            <p class="no-results">No words found.</p>
        <?php else: ?>
            <ul class="word-list">
                <?php foreach ($pageItems as $item): ?>
                    <li>
                        <a href="index.php?word=<?php echo urlencode($item['slug']); ?>">
                            <?php echo htmlspecialchars(capitalizeFirst(displayText($item))); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php renderPagination($page, $totalPages, $query); ?>
        <?php endif; ?>
    </div>
</main>

<script type="application/json" id="allWordsData"><?php echo json_encode($clientWords, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?></script>

    <?php
    renderFooter($dictionaryTitle, $dictionaryAuthor, $totalWords, $visitors2, $version);
}

function renderPagination($page, $totalPages, $query) {
    if ($totalPages <= 1) {
        return;
    }

    $baseParams = $query !== '' ? ['q' => $query] : [];

    function pageUrl($pageNo, $baseParams) {
        return 'index.php?' . http_build_query(array_merge($baseParams, ['page' => $pageNo]));
    }
    ?>
    <div class="pagination">
        <?php if ($page <= 1): ?>
            <span class="disabled">&laquo; Previous</span>
        <?php else: ?>
            <a data-page="<?php echo $page - 1; ?>" href="<?php echo htmlspecialchars(pageUrl($page - 1, $baseParams)); ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        if ($start > 1): ?>
            <a data-page="1" href="<?php echo htmlspecialchars(pageUrl(1, $baseParams)); ?>">1</a>
            <?php if ($start > 2): ?><span>&hellip;</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($p = $start; $p <= $end; $p++): ?>
            <?php if ($p === $page): ?>
                <span class="current"><?php echo $p; ?></span>
            <?php else: ?>
                <a data-page="<?php echo $p; ?>" href="<?php echo htmlspecialchars(pageUrl($p, $baseParams)); ?>"><?php echo $p; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?><span>&hellip;</span><?php endif; ?>
            <a data-page="<?php echo $totalPages; ?>" href="<?php echo htmlspecialchars(pageUrl($totalPages, $baseParams)); ?>"><?php echo $totalPages; ?></a>
        <?php endif; ?>

        <?php if ($page >= $totalPages): ?>
            <span class="disabled">Next &raquo;</span>
        <?php else: ?>
            <a data-page="<?php echo $page + 1; ?>" href="<?php echo htmlspecialchars(pageUrl($page + 1, $baseParams)); ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
    <?php
}

/* ========================================================================
 * Word detail page
 * ==================================================================== */
function renderWordDetail($slug, $dataDir, $dictionaryTitle, $dictionaryAuthor, $totalWords, $visitors2, $version) {
    $safeSlug = basename($slug);
    $wordFile = $dataDir . '/' . $safeSlug . '.json';

    if ($safeSlug === '' || $safeSlug === 'Dictionary' || !file_exists($wordFile)) {
        renderHeader($dictionaryTitle, 'Word Not Found - ' . $dictionaryTitle, '', $version);
        echo '<main><p class="no-results">Sorry, this word could not be found.</p>';
        echo '<p><a class="back-link" href="index.php">&laquo; Back to word list</a></p></main>';
        renderFooter($dictionaryTitle, $dictionaryAuthor, $totalWords, $visitors2, $version);
        return;
    }

    $data = json_decode(file_get_contents($wordFile), true);
    $displayWord = capitalizeFirst(displayText($data));
    $paragraphs = array_map(function ($section) {
        return $section['paragraph'] ?? '';
    }, $data['sections'] ?? []);
    $metaDescription = excerptDefinition($paragraphs);

    renderHeader($dictionaryTitle, $displayWord . ' - ' . $dictionaryTitle, $metaDescription, $version);
    ?>

<main>
    <a class="back-link" href="index.php">&laquo; Back to word list</a>

    <div class="toolbar">
        <button id="zoomIn" title="Zoom in">A+</button>
        <button id="zoomOut" title="Zoom out">A-</button>
        <button id="zoomReset" title="Reset zoom">&#8635;</button>
        <button id="copyTextBtn">📋 Copy Text</button>
        <button id="copyLinkBtn">🔗 Copy Link</button>
        <span id="copyFeedback" class="copy-feedback"></span>
    </div>

    <div id="wordContent" class="word-detail">
        <h1 id="wordTitle" class="word-title"><?php echo htmlspecialchars($displayWord); ?></h1>
        <div id="wordDefinition" class="definition">
            <?php foreach ($paragraphs as $paragraph): ?>
                <?php if (trim($paragraph) !== ''): ?>
                    <p><?php echo renderDefinitionParagraph($paragraph); ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</main>

    <?php
    renderFooter($dictionaryTitle, $dictionaryAuthor, $totalWords, $visitors2, $version);
}
