<?php require APP_ROOT.'/views/layout/header.php'; ?>

<!-- ╔════════════ SEARCH BOX ════════════╗ -->
<div class="position-relative mb-4">
  <form class="input-group" method="get">
    <input type="hidden" name="url" value="movies/search">
    <input id="searchBox" type="text" name="q" class="form-control"
           placeholder="Search movie title…" autocomplete="off"
           value="<?= htmlspecialchars($query ?? '') ?>">
    <button class="btn btn-primary">Search</button>
  </form>

  <!-- live suggestions -->
  <div id="suggestList"
       class="list-group position-absolute w-100 shadow-sm"
       style="z-index:1050;max-height:260px;overflow-y:auto"></div>
</div>
<!-- ╚════════════════════════════════════╝ -->


<?php /* LANDING HERO (only when nothing searched yet) */ ?>
<?php if (empty($query) && empty($list) && empty($movie)): ?>
  <div class="hero p-5 mb-5 text-center">
    <h1 class="display-6 fw-bold mb-3">Find • Rate • Review</h1>
    <p class="lead mb-4">
      Search any film in the <strong>OMDb</strong> catalogue, give it a rating,
      then let <strong>Gemini 1.0 Flash</strong> write a spoiler‑free review – instantly.
    </p>
    <div class="row row-cols-1 row-cols-md-3 g-3">
      <div class="col"><span class="badge bg-light text-dark p-3 w-100">★ Community averages</span></div>
      <div class="col"><span class="badge bg-light text-dark p-3 w-100">⚡ AI 80‑word summaries</span></div>
      <div class="col"><span class="badge bg-light text-dark p-3 w-100">⌨︎ Type‑ahead search</span></div>
    </div>
  </div>
<?php endif; ?>


<?php /* ─── LIST MODE ──────────────────────────────────── */ ?>
<?php if (!empty($list)): ?>
  <div class="row row-cols-2 row-cols-md-4 g-3 mb-5">
    <?php foreach ($list as $hit): ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="<?= $hit['Poster']!=='N/A' ? $hit['Poster'] : 'https://via.placeholder.com/300x445?text=No+Poster' ?>"
               class="card-img-top" alt="">
          <div class="card-body p-2">
            <h6 class="card-title small mb-1"><?= $hit['Title'] ?></h6>
            <p class="card-text small text-muted mb-2"><?= $hit['Year'] ?></p>
            <a href="?url=movies/search&id=<?= $hit['imdbID'] ?>" class="btn btn-sm btn-outline-primary w-100">
              Details
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php /* ─── DETAIL MODE ───────────────────────────────── */ ?>
<?php elseif (!empty($movie)): ?>
  <div class="card mb-4 shadow-sm">
    <div class="row g-0">
      <div class="col-md-4">
        <img src="<?= $movie['Poster']!=='N/A' ? $movie['Poster'] : 'https://via.placeholder.com/300x445?text=No+Poster' ?>"
             class="img-fluid rounded-start">
      </div>
      <div class="col-md-8">
        <div class="card-body">
          <h3 class="card-title"><?= $movie['Title'] ?></h3>
          <p class="card-text small"><?= $movie['Plot'] ?></p>

          <ul class="list-unstyled small mb-3">
            <li><strong>Year:</strong> <?= $movie['Year'] ?></li>
            <li><strong>Genre:</strong> <?= $movie['Genre'] ?></li>
            <li><strong>IMDB:</strong> <?= $movie['imdbRating'] ?>/10</li>
            <?php if ($avgRating): ?>
              <li><strong>User rating:</strong>
                  <span class="badge bg-success"><?= $avgRating ?>/5</span></li>
            <?php endif; ?>
          </ul>

          <!-- rating form -->
          <form class="d-inline" method="post">
            <input type="hidden" name="title" value="<?= htmlspecialchars($movie['Title']) ?>">
            <input type="hidden" name="id"    value="<?= htmlspecialchars($movie['imdbID']) ?>">
            <select name="rate" class="form-select form-select-sm w-auto d-inline me-1">
              <?php for($i=1;$i<=5;$i++): ?><option><?= $i ?></option><?php endfor; ?>
            </select>
            <button class="btn btn-sm btn-success">Rate</button>
          </form>

          <!-- AI review -->
          <a href="?url=movies/search&id=<?= $movie['imdbID'] ?>&review=1"
             class="btn btn-sm btn-outline-secondary ms-2">Get AI review</a>
        </div>
      </div>
    </div>
  </div>

  <?php if ($review): ?>
    <div class="alert alert-secondary">
      <strong>AI Review:</strong><br><?= nl2br($review) ?>
    </div>
  <?php endif; ?>

<?php /* ─── NO‑RESULT MODE ─────────────────────────────── */ ?>
<?php elseif (!empty($query)): ?>
  <div class="alert alert-warning">
    No movies found for <strong><?= htmlspecialchars($query) ?></strong>
  </div>
<?php endif; ?>

<?php require APP_ROOT.'/views/layout/footer.php'; ?>
