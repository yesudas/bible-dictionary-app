# bible-dictionary-app
Simple &amp; elegant web application for bible dictionaries

# Demo
https://wordofgod.in/bibledictionary

# Features
1. Mobile-friendly PHP web apps — a parent hub linking to 18 dictionaries, each a self-contained app
2. Installable as a PWA (manifest + service worker) on both the parent and every dictionary
3. Instant filter-as-you-type search (client-side, no page reload) with server-side pagination fallback
4. Every word has its own bookmarkable, SEO-friendly URL (`index.php?word=...`)
5. Word detail pages: Copy Text, Copy Link (unencoded, readable Tamil/etc. URLs), and zoom in/out/reset
6. Cross-dictionary word index + API — look up which dictionaries contain a given word, even from an inflected Tamil form
7. Per-dictionary + master sitemap.xml generation
8. Visitor counter (bot-filtered) on every page, plus a hidden honeypot link to catch scraping bots
9. About Us page on every dictionary

# Dictionaries
| Folder | Language |
|---|---|
| `bdag3-greek-dictionary` | English (Greek lexicon — BDAG3) |
| `bdb-t-bible-dictionary` | English (Greek lexicon — BDB-T) |
| `danker-greek-dictionary` | English (Greek lexicon — Danker) |
| `eastons-bible-dictionary` | English |
| `gesenius-hebrew-dictionary` | English (Hebrew lexicon — Gesenius) |
| `gr-en-ls-greek-dictionary` | English (Greek lexicon — Liddell-Scott) |
| `he-en-b-hebrew-dictionary` | Hebrew + English (bilingual) |
| `lxx-green-dictionary` | English (Greek lexicon — LXX/Green's) |
| `mlsj-greek-dictionary` | English (Greek lexicon — MLSJ) |
| `naves-bible-dictionary` | English (Nave's Topical Bible Dictionary) |
| `smiths-bible-dictionary` | English |
| `strongs-bible-dictionary` | English (Hebrew & Greek — Strong's) |
| `tamil-bible-dictionary-by-truth` | Tamil |
| `thompson-chain-reference` | English (Thompson Chain Reference) |
| `சத்திய-வேதாகமப்-பெயர்-அகராதி` | Tamil (Bible names) |
| `பரிபூரண-பெயர்ப்-பொக்கிஷம்` | Tamil (Bible names — எம். ஜோசப் மோசஸ்) |
| `ஸ்ட்ராங்க்ஸ்-எபிரேய-அகராதி` | Tamil + Hebrew (Strong's) |
| `ஸ்ட்ராங்க்ஸ்-கிரேக்க-அகராதி` | Tamil + Greek (Strong's) |

> `greek-nt-dictionary` was also added but is missing from disk as of this
> writing (likely removed by a sync tool) — add it back to this table once
> it's confirmed present again.

Each dictionary folder is an independent PHP app with the same structure,
built from a shared template (see **Child app anatomy** below) — copy
`eastons-bible-dictionary`'s files into a new folder and adjust `data/` to
add another dictionary.

---

# Parent app (this directory)

- `index.php` — header ("Bible Dictionaries"), a card linking to each
  dictionary, and a footer with the WordOfGod.in resource links, the
  "Freely you have received; Freely give" verse (Matt 10:8), and the
  visitor count.
- `assets/{css,js,images}/` — styles, the install-button/service-worker
  script, and PWA icons.
- `manifest.json` + `sw.js` — PWA support.
- `counter.php` — bot-filtered visitor counter; also consolidates each
  dictionary's own `counter.txt` into a combined weekly total (see
  `$consolidationIntervalSeconds` at the top of the file). The dictionaries
  it consolidates (`$additionalFolders`) are auto-discovered — any immediate
  subfolder with its own `counter.php` — so a newly added dictionary needs
  no edits here.
- `robots.txt` — crawler rules; not touched by any of the scripts here.
- `v1/` — archived copy of the old static homepage, kept for reference
  only; not served.

## Cross-Dictionary Word Index & API

Lets the app answer "which dictionaries have an entry for this word?" —
including when the visitor types an inflected form (e.g. `ஆரோனாலும்`)
rather than the dictionary's exact base word (`ஆரோன்`).

### Data it depends on

- `index/words/{word}.json` — one file per dictionary word, listing every
  dictionary that has an entry for it, e.g.:
  ```json
  {
    "word": "அகபு",
    "supportedDictionaries": [
      "tamil-bible-dictionary-by-truth",
      "சத்திய-வேதாகமப்-பெயர்-அகராதி"
    ]
  }
  ```
- `index/letters/{letter}.txt` — `inflected_form=dictionary_word` mapping
  files, one per starting letter (e.g. `ஆ.txt`), generated separately by
  `generate_mapping_from_bible.py` in the
  [private-apps](../private-apps/python/bible-dictionary) repo by mining
  real Bible text. Tamil-only — English and Strong's words always resolve
  via the exact-match fallback below instead.

### `i.php` — builds `index/words/`

Run whenever a dictionary's `data/` folder changes:

```bash
php i.php
```

Scans the `data/` folder of every dictionary listed in its `$dictionaries`
array (unlike `sitemap.php`/`counter.php`, this list is manually
maintained — edit it to add/remove a dictionary), skipping
`Dictionary.json`, and writes `index/words/{word}.json` for every word file
found (e.g. `data/அகசியா.json` → word `அகசியா`), listing which of those
dictionaries contain it. Can also be opened in a browser — it renders a
styled HTML progress page instead of the plain/ANSI CLI output.

### `api.php` — `getDictionaries` endpoint

```
GET /bibledictionary/api.php?action=getDictionaries&word=ஆரோனாலும்
→ {
    "dictionaries": [
      {"slug": "ஆரோன்", "dictionary": "tamil-bible-dictionary-by-truth"},
      {"slug": "ஆரோன்", "dictionary": "சத்திய-வேதாகமப்-பெயர்-அகராதி"}
    ]
  }
```

Each entry's `slug` is the filename to fetch from *that entry's own*
`dictionary`, e.g. `tamil-bible-dictionary-by-truth/data/ஆரோன்.json`. Pairing them
per-entry (rather than one shared slug for the whole list) keeps this
correct even in the rare case where a single inflected form resolves to
more than one distinct dictionary word, each potentially slugged
differently in different dictionaries.

1. Looks up the word's starting letter in `index/letters/{letter}.txt` to
   resolve an inflected form to its actual dictionary word(s).
2. Falls back to treating `word` as an exact dictionary word directly
   against `index/words/{word}.json` if no inflected-form mapping was
   found (base dictionary words never appear as keys in the letters files).
   Tries the word as-is, lowercased, and uppercased, verifying the exact
   on-disk filename casing via a directory scan (not `file_exists()`,
   which can't be trusted to reject a mismatched case on a
   case-insensitive filesystem) — so this returns the correct slug
   casing regardless of input casing (`Aaron`/`aaron`, `H1`/`h1`) and
   regardless of the server's filesystem.
3. Emits one `{slug, dictionary}` pair per dictionary that supports each
   resolved word.

Returns `{"dictionaries": []}` for a word not found either way, and
`{"success": false, "error": "..."}` for a malformed request (missing
`word`, unknown `action`).

## Master sitemap

```bash
php sitemap.php
```

Regenerates every dictionary's own `sitemap.xml` (each dictionary's
`sitemap.php` runs as its own subprocess, so one dictionary's failure
can't take down the others), then writes a `sitemap.xml` here as a
`<sitemapindex>` pointing at all of them — this is the one URL to submit
to search engines. Dictionaries are auto-discovered (any immediate
subfolder with its own `sitemap.php`), so a newly added dictionary needs
no edits here. Can also be opened in a browser — it renders a styled HTML
progress page instead of the plain/ANSI CLI output.

---

# Child app anatomy (every dictionary folder)

- `index.php` — one file handling both views:
  - **Word list** (`index.php`): search box with instant client-side
    filter-as-you-type (the full word list is embedded once as JSON on
    load) plus server-side `?q=`/`?page=` pagination as a no-JS fallback,
    50 words per page.
  - **Word detail** (`index.php?word=slug`): the word/definition, a
    toolbar (Copy Text, Copy Link, zoom in/out/reset), and SEO meta tags.
    Renders plain-text definitions escaped, but definitions that already
    contain HTML (Strong's `<strong>`/`<span>` markup, சத்திய's `<h2>`/`<h3>`
    era headings) are rendered as trusted HTML instead of double-escaped,
    and legacy `#/G427.../427`-style cross-reference links from the old
    AngularJS app are rewritten to `index.php?word=G427`.
- `about.php` — About Us page (Word of God team info, links, contact
  details); shares the same header/footer.
- `partials.php` — shared header/footer + helpers (`displayText()`,
  `capitalizeFirst()`, the canonical `siteResourceLinks()` list used by
  both the header nav and footer, HTML-vs-plain-text definition
  rendering).
- `assets/{css,js,images}/` — styles (incl. the mobile hamburger nav
  collapse), search/pagination/copy/zoom JS, PWA icons.
- `manifest.json` + `sw.js` — PWA support, scoped to that dictionary's URL.
- `build-index.php` — **run whenever `data/` changes**: regenerates
  `data/Dictionary.json` (word + slug pairs) directly from the actual
  files in `data/`, since filenames can drift out of sync with an
  older/manually-edited `Dictionary.json`. Strong's-style dictionaries
  (slugs like `H1`, `G23`) are sorted in numeric concordance order rather
  than alphabetically by their long composite display text.
- `sitemap.php` — regenerates this dictionary's own `sitemap.xml` from
  `Dictionary.json`; derives its folder name automatically
  (`basename(__DIR__)`), so it needs no per-dictionary edits.
- `bot.php` — honeypot: a link to it is hidden in the footer (CSS
  visually-hidden, not `display:none`, so simple scrapers that skip
  hidden links don't detect the trap) and logs any hit to `bot.log`.
- `counter.php` — bot-filtered visitor counter (`counter.txt`).
- `contactus.php` / `contactus.db` — legacy contact-form backend from the
  old AngularJS app; not currently wired to a UI form in the new
  `about.php` (which shows contact info as plain text instead). Left in
  place in case a contact form is added back later.

## Maintenance: after adding/renaming/removing words in `data/`

```bash
cd some-dictionary/
php build-index.php   # regenerate Dictionary.json
php sitemap.php       # regenerate this dictionary's sitemap.xml
cd ..
php i.php        # rebuild the cross-dictionary word index
php sitemap.php  # regenerate every sitemap + the master sitemap index
```

---

# How to Install

## Make changes specific to your website
Update the Google Analytics tag (`G-8ZYHRZG9B8`) and the `wordofgod.in`
URLs in each dictionary's `partials.php` (header/footer nav) and
`manifest.json`/`sitemap.php` if deploying to a different domain.

## Upload
Upload the entire contents of this repository to your website.

Thats it!

## Contact Us
Feel free to share your comments/feedbacks to wordofgod@wordofgod.in
