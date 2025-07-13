<?php require APP_ROOT.'/views/layout/header.php'; ?>

<form class="input-group mb-4" method="get">
  <input type="hidden" name="url" value="movies/search">
  <input type="text" name="q" class="form-control"
         placeholder="Search movie title…" value="<?= htmlspecialchars($query) ?>">
  <button class="btn btn-primary">Search</button>
</form>

<?php if ($movie): ?>
  <div class="card mb-4 shadow-sm">
    <div class="row g-0">
      <div class="col-md-4"><img src="<?= $movie['Poster'] ?>" class="img-fluid rounded-start"></div>
      <div class="col-md-8">
        <div class="card-body">
          <h3 class="card-title"><?= $movie['Title'] ?></h3>
          <p class="card-text small"><?= $movie['Plot'] ?></p>

          <ul class="list-unstyled small mb-3">
            <li><strong>Year:</strong> <?= $movie['Year'] ?></li>
            <li><strong>Genre:</strong> <?= $movie['Genre'] ?></li>
            <li><strong>IMDB:</strong> <?= $movie['imdbRating'] ?>/10</li>
            <?php if ($avgRating): ?>
              <li><strong>User rating:</strong>
                  <span class="badge bg-success"><?= $avgRating ?>/5</span></li>
            <?php endif; ?>
          </ul>

          <!-- rating form -->
          <form class="d-inline" method="post">
            <input type="hidden" name="title" value="<?= htmlspecialchars($movie['Title']) ?>">
            <select name="rate" class="form-select form-select-sm w-auto d-inline">
              <?php for($i=1;$i<=5;$i++): ?>
                <option><?= $i ?></option>
              <?php endfor; ?>
            </select>
            <button class="btn btn-sm btn-success">Rate</button>
          </form>

          <!-- AI review trigger -->
          <a href="?url=movies/search&q=<?= urlencode($movie['Title']) ?>&review=1"
             class="btn btn-sm btn-outline-secondary ms-2">Get AI review</a>

        </div>
      </div>
    </div>
  </div>

  <?php if ($review): ?>
    <div class="alert alert-secondary"><strong>AI Review:</strong><br><?= nl2br($review) ?></div>
  <?php endif; ?>

<?php elseif ($query): ?>
  <div class="alert alert-warning">No result for <strong><?= htmlspecialchars($query) ?></strong></div>
<?php endif; ?>

<?php require APP_ROOT.'/views/layout/footer.php'; ?>
