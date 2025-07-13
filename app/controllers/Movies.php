<?php
class Movies extends Controller
{
    /** Handles both list and detail. Routes:
     *  GET /movies/search?q=lion+king         → list
     *  GET /movies/search?id=tt6105098        → details for a hit
     *  POST /movies/search   (rate)           → insert rating then redirect to details
     */
    public function search(): void
    {
        $ratingM = $this->model('Rating');

        /* ── Handle rating submission ───────────────────────────── */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rate'], $_POST['title'], $_POST['id'])) {
            $ratingM->insert(null, $_POST['title'], (int)$_POST['rate']);
            header('Location: /movies/search?id=' . urlencode($_POST['id']));
            exit;
        }

        /* ── Decide “list” or “detail” mode ─────────────────────── */
        $id     = $_GET['id'] ?? null;   // imdbID if coming from list click
        $query  = $_GET['q']  ?? '';     // free‑text search term
        $list   = [];
        $movie  = null;
        $avgRating = $review = null;

        $apiKey = $_ENV['OMDB_API_KEY'];

        if ($id) {                                    // -------- detail -------
            $resp  = json_decode(
                file_get_contents("https://www.omdbapi.com/?apikey=$apiKey&i=$id&plot=full"),
                true
            );
            if ($resp['Response'] === 'True') {
                $movie     = $resp;
                $avgRating = $ratingM->avgFor($movie['Title']);

                /* optional AI review */
                if (isset($_GET['review'])) {
                    $review = $this->aiReview($movie['Title'], $movie['Plot']);
                }
            }
        }
        elseif ($query !== '') {                      // -------- list ---------
            $resp = json_decode(
                file_get_contents("https://www.omdbapi.com/?apikey=$apiKey&s=" . urlencode($query) . "&type=movie"),
                true
            );
            if ($resp['Response'] === 'True') $list = $resp['Search'];
        }

        /* pass everything to the view */
        $this->view('movies/search', compact('query', 'list', 'movie', 'avgRating', 'review'));
    }

    /* === helper: Gemini call (unchanged) === */
    private function aiReview(string $title, string $plot): string
    {
        $url  = 'https://generativelanguage.googleapis.com/v1beta/models/'
              . 'gemini-2.0-flash:generateContent?key=' . $_ENV['GEMINI_API_KEY'];

        $body = json_encode([
            'contents' => [[
                'parts' => [[
                    'text' => "Write an impartial 80‑word movie review for \"$title\". Plot: $plot"
                ]]
            ]]
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($raw, true);
        return $json['candidates'][0]['content']['parts'][0]['text']
               ?? 'Review unavailable.';
    }
}
