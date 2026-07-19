if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/bibledictionary/eastons-bible-dictionary/sw.js')
        .then(() => console.log('Service Worker Registered'))
        .catch(err => console.log('Service Worker Failed:', err));
}

document.addEventListener('DOMContentLoaded', () => {
    setupInstallButton();
    setupNavToggle();
    setupZoom();
    setupCopyButtons();
    setupLiveSearch();
});

/* ---------------------------------------------------------------------- */
/* Mobile hamburger menu                                                   */
/* ---------------------------------------------------------------------- */
function setupNavToggle() {
    const toggleBtn = document.getElementById('navToggle');
    const nav = document.getElementById('siteNav');
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('open');
        toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
}

/* ---------------------------------------------------------------------- */
/* PWA install prompt                                                      */
/* ---------------------------------------------------------------------- */
function setupInstallButton() {
    const installBtn = document.getElementById('installAppBtn');
    if (!installBtn) return;
    let deferredPrompt;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        installBtn.style.display = 'inline-block';
    });

    installBtn.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const choice = await deferredPrompt.userChoice;
            console.log('User choice:', choice.outcome);
            deferredPrompt = null;
            installBtn.style.display = 'none';
        }
    });
}

/* ---------------------------------------------------------------------- */
/* Zoom controls (word detail page)                                       */
/* ---------------------------------------------------------------------- */
function setupZoom() {
    const content = document.getElementById('wordContent');
    if (!content) return;

    let fontScale = 1;

    function applyZoom() {
        content.style.fontSize = fontScale + 'em';
    }

    document.getElementById('zoomIn')?.addEventListener('click', () => {
        fontScale = Math.min(fontScale + 0.1, 3);
        applyZoom();
    });
    document.getElementById('zoomOut')?.addEventListener('click', () => {
        fontScale = Math.max(fontScale - 0.1, 0.5);
        applyZoom();
    });
    document.getElementById('zoomReset')?.addEventListener('click', () => {
        fontScale = 1;
        applyZoom();
    });
}

/* ---------------------------------------------------------------------- */
/* Copy text / copy link (word detail page)                               */
/* ---------------------------------------------------------------------- */
function setupCopyButtons() {
    const copyTextBtn = document.getElementById('copyTextBtn');
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    const feedback = document.getElementById('copyFeedback');

    function showFeedback(message) {
        if (!feedback) return;
        feedback.textContent = message;
        feedback.style.display = 'inline';
        clearTimeout(showFeedback._t);
        showFeedback._t = setTimeout(() => {
            feedback.style.display = 'none';
        }, 2000);
    }

    // window.location.href is always percent-encoded (e.g. Tamil folder/word
    // names become %E0%AE...); decodeURI() gives back the readable form
    // while still leaving ?/=/&/# untouched, so the query string stays intact.
    function readableLink() {
        try {
            return decodeURI(window.location.href);
        } catch (e) {
            return window.location.href;
        }
    }

    if (copyTextBtn) {
        copyTextBtn.addEventListener('click', async () => {
            const word = document.getElementById('wordTitle')?.textContent.trim() || '';
            const definition = document.getElementById('wordDefinition')?.innerText.trim() || '';
            const link = readableLink();
            const text = word + '\n\n' + definition + '\n\n' + link;
            try {
                await navigator.clipboard.writeText(text);
                showFeedback('Copied text!');
            } catch (e) {
                showFeedback('Could not copy');
            }
        });
    }

    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', async () => {
            const link = readableLink();
            try {
                await navigator.clipboard.writeText(link);
                showFeedback('Copied link!');
            } catch (e) {
                showFeedback('Could not copy');
            }
        });
    }
}

/* ---------------------------------------------------------------------- */
/* Instant filter-as-you-type + client-side pagination (word list page)   */
/* ---------------------------------------------------------------------- */
function setupLiveSearch() {
    const dataScript = document.getElementById('allWordsData');
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearch');
    const container = document.getElementById('wordListContainer');

    if (!dataScript || !searchInput || !container) return;

    let allWords;
    try {
        allWords = JSON.parse(dataScript.textContent);
    } catch (e) {
        return;
    }

    const perPage = parseInt(container.dataset.perPage, 10) || 50;
    let currentPage = parseInt(container.dataset.initialPage, 10) || 1;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function buildPaginationHtml(page, totalPages) {
        if (totalPages <= 1) return '';

        const parts = ['<div class="pagination">'];

        parts.push(page <= 1
            ? '<span class="disabled">&laquo; Previous</span>'
            : `<a href="#" data-page="${page - 1}">&laquo; Previous</a>`);

        const start = Math.max(1, page - 2);
        const end = Math.min(totalPages, page + 2);

        if (start > 1) {
            parts.push(`<a href="#" data-page="1">1</a>`);
            if (start > 2) parts.push('<span>&hellip;</span>');
        }

        for (let p = start; p <= end; p++) {
            parts.push(p === page
                ? `<span class="current">${p}</span>`
                : `<a href="#" data-page="${p}">${p}</a>`);
        }

        if (end < totalPages) {
            if (end < totalPages - 1) parts.push('<span>&hellip;</span>');
            parts.push(`<a href="#" data-page="${totalPages}">${totalPages}</a>`);
        }

        parts.push(page >= totalPages
            ? '<span class="disabled">Next &raquo;</span>'
            : `<a href="#" data-page="${page + 1}">Next &raquo;</a>`);

        parts.push('</div>');
        return parts.join('');
    }

    function render(query, page) {
        const filtered = query
            ? allWords.filter(item => item.w.toLowerCase().includes(query.toLowerCase()))
            : allWords;

        const total = filtered.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        page = Math.min(Math.max(1, page), totalPages);
        currentPage = page;

        const pageItems = filtered.slice((page - 1) * perPage, page * perPage);

        let html = '';
        if (query) {
            html += `<p class="result-summary">${total} result${total === 1 ? '' : 's'} for "${escapeHtml(query)}"</p>`;
        }

        if (pageItems.length === 0) {
            html += '<p class="no-results">No words found.</p>';
        } else {
            html += '<ul class="word-list">';
            for (const item of pageItems) {
                html += `<li><a href="index.php?word=${encodeURIComponent(item.s)}">${escapeHtml(item.w)}</a></li>`;
            }
            html += '</ul>';
            html += buildPaginationHtml(page, totalPages);
        }

        container.innerHTML = html;
        clearBtn.style.display = query ? 'inline-block' : 'none';
    }

    searchInput.addEventListener('input', () => {
        render(searchInput.value.trim(), 1);
    });

    clearBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        searchInput.value = '';
        searchInput.focus();
        render('', 1);
    });

    container.addEventListener('click', (e) => {
        const link = e.target.closest('a[data-page]');
        if (!link) return;
        e.preventDefault();
        render(searchInput.value.trim(), parseInt(link.dataset.page, 10));
    });

    // Prevent a full page reload on Enter -- live filtering already applies
    document.getElementById('searchForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        render(searchInput.value.trim(), 1);
    });
}
