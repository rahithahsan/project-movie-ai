<?php
class Movies extends Controller
{
    /** --------------------------------------------------------------
     *  GET  /movies/search   – search the OMDb API
     *  POST /movies/search   – persist a 1‑5 rating
     *  --------------------------------------------------------------*/
    public function search(): void
    {
        $ratingM   = $this->model('Rating');

        $query     = $_GET['q']      ?? '';
        $movie     = null;           // OMDb payload
        $avgRating = null;           // float|null
        $review    = null;           // AI review text

        /* ── 1. Handle an incoming rating ───────────────────────── */
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['rate'], $_POST['title'])) {

            $ratingM->insert(null, $_POST['title'], (int) $_POST['rate']);
            // PRG pattern → avoids resubmission on refresh
            header('Location: /movies/search?q=' .
                    urlencode($_POST['title']));
            exit;
        }

        /* ── 2. Fetch OMDb data when a query is present ─────────── */
        if ($query !== '') {
            $raw = file_get_contents(
                'https://www.omdbapi.com/?apikey=' .
                $_ENV['OMDB_API_KEY'] . '&t=' . urlencode($query)
            );

            $payload = json_decode($raw, true);
            if ($payload['Response'] === 'True') {
                $movie     = $payload;
                $avgRating = $ratingM->avgFor($movie['Title']);
            }
        }

        /* ── 3. Optional AI‑generated review (Gemini) ───────────── */
        if ($movie && isset($_GET['review'])) {
            $review = $this->aiReview($movie['Title'], $movie['Plot']);
        }

        /* ── 4. Render view ─────────────────────────────────────── */
        $this->view('movies/search',
            compact('movie', 'query', 'avgRating', 'review'));
    }

    /* ==============================================================
       Private helper – call Gemini‑Pro and return an 80‑word review
       ==============================================================*/
    private function aiReview(string $title, string $plot): string
    {
        $url  = 'https://generativelanguage.googleapis.com/v1beta/models/'
              . 'gemini-2.0-flash:generateContent?key='
              . $_ENV['GEMINI_API_KEY'];          // 👈 endpoint + key

        $body = json_encode([
            'contents' => [[
                'parts' => [[
                    'text' => "Write an impartial 80‑word movie review for "
                            . "\"$title\".\nPlot synopsis: $plot"
                ]]
            ]]
        ]);

        /* curl POST */
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 12,
        ]);

        $raw  = curl_exec($ch);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err) return "Review unavailable. ($err)";

        $json = json_decode($raw, true);

        return $json['candidates'][0]['content']['parts'][0]['text']
               ?? 'Review unavailable.';
    }
}
