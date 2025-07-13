# Movie AI – OMDb ✕ Gemini Demo - Rahith Ahsan

Search • Rate • Read an 80‑word AI review

[Live Replit → https://replit.com](https://replit.com/@rahsan2/project-movie-ai#README.md]([https://replit.com](https://replit.com/@rahsan2/project-movie-ai#README.md))
[GitHub      [→ paste repo here](https://github.com/rahithahsan/project-movie-ai)]([https://github.com](https://github.com/rahithahsan/project-movie-ai))

## Default route

MVC layout

`/public/index.php?url=movies/search`

`app/controllers/Movies.php` → `models/Rating.php` → `views`

## 1. What this project does ✨

*   Assignment requirement
*   Implementation

### Connect to OMDb

Server‑side calls to `https://www.omdbapi.com/?apikey=…` (key stored in Replit Secrets). Two modes:

*   `&s=TITLE` → search list
*   `&i=IMDB_ID&plot=full` → single movie details

### Search & show movie info

Responsive Bootstrap card with poster, plot, year, genre, IMDb rating.

### Give a rating

Inline `<select 1‑5>` + “Rate” button. POST inserts row in ratings table.

### Persist in DB (PDO)

`models/Rating.php` uses prepared statements against MariaDB. Schema: `id · user_id? · movie_title · rating · created_at`

### Display averages

`SELECT ROUND(AVG(rating),1)` → badge “★ 4.3/5”.

### AI‑generated review

Button calls `/movies/search?id=…&review=1` → controller hits Google Gemini 1.0 Flash REST end-point and renders an impartial ~80‑word summary.

### Extra polish

*   Type‑ahead suggestions (AJAX to `/movies/suggest`)
*   Debounced fetch, max 5 hits
*   Custom hero copy when page is empty
*   Dark navbar + footer
*   Dedicated brand palette in `public/css/style.css`

## 2. Folder map
app/
  controllers/   Movies.php
  models/        Rating.php
  core/          App.php · Controller.php · database.php
  views/
     layout/     header.php · footer.php
     movies/     search.php
public/
  css/           style.css
  js/            suggest.js
  index.php      (front‑controller)
router.php       (pretty URL helper for Replit)


## 3. RUBRIC CHECKLIST:
MVC - Clean 3‑layer separation; single controller handles both list & detail; autoloader in core/App.php.

DB / table - ratings table meets spec, created via migration SQL in README. All CRUD via PDO.

Code structure - Composer‑style PSR‑0 autoload, router for clean URLs, secrets in env.

Look / feel - Custom palette, gradient hero, Bootstrap 5 cards, icons, fully responsive; empty‑state marketing section.

Extra
• Type‑ahead search• Debounce & accessibility (keyboard ↑↓ support)
• Gemini Flash review (faster & cheaper)
• Docker‑free deploy on Replit with router.php• 404 guard & tidy error handling