</div><!-- /.container -->

<footer class="mt-auto border-top py-3" style="background:var(--ai-dark)">
  <div class="container d-flex flex-column flex-md-row
              align-items-center justify-content-between gap-3
              text-white-50 small">

    <span>© <?= date('Y') ?> Movie AI demo</span>

    <nav class="d-flex gap-3">
      <a class="link-light text-decoration-none" href="https://www.omdbapi.com/" target="_blank">OMDb API</a>
      <a class="link-light text-decoration-none" href="https://ai.google.dev/"  target="_blank">Gemini API</a>
      <a class="link-light text-decoration-none" href="https://github.com/rahithahsan/project-movie-ai<?= getenv('REPL_SLUG')?>" target="_blank">Source</a>
    </nav>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/suggest.js"></script>
</body></html>
