<?php
class Movies extends Controller
{
    /** GET /movies/search  &  POST /movies/rate */
    public function search(): void
    {
        $ratingM   = $this->model('Rating');
        $query     = $_GET['q']   ?? '';
        $movie     = null;
        $avgRating = null;
        $review    = null;

        /* 1️⃣ Handle a submitted rating --------------------------- */
        if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['rate'],$_POST['title'])) {
            $ratingM->insert(null, $_POST['title'], (int)$_POST['rate']);
            header('Location: /movies/search?q='.urlencode($_POST['title'])); exit;
        }

        /* 2️⃣ Pull OMDb data if there’s a query ------------------- */
        if ($query) {
            $resp  = json_decode(
                file_get_contents(
                    'https://www.omdbapi.com/?apikey='
                    . $_ENV['OMDB_API_KEY'].'&t='.urlencode($query)
                ),
                true
            );
            if ($resp['Response']==='True') {
                $movie     = $resp;
                $avgRating = $ratingM->avgFor($movie['Title']);
            }
        }

        /* 3️⃣ Optional AI review ---------------------------------- */
        if ($movie && isset($_GET['review'])) {
            $review = $this->aiReview($movie['Title'], $movie['Plot']);
        }

        $this->view('movies/search', compact('movie','query','avgRating','review'));
    }

    /* ——— private helper ——— */
    private function aiReview(string $title,string $plot): string
    {
        $url  = 'https://generativelanguage.googleapis.com/v1beta/models/'
              . 'gemini-1.0-pro:generateContent?key='.$_ENV['GEMINI_API_KEY'];

        $body = json_encode([
            'contents'=>[['parts'=>[['text'=>
                "Write an impartial 80‑word movie review for \"$title\". "
              . "Plot summary: $plot"]]]]
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch,[
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true
        ]);
        $raw = curl_exec($ch); curl_close($ch);

        $json = json_decode($raw, true);
        return $json['candidates'][0]['content']['parts'][0]['text'] ?? 'Review unavailable.';
    }
}
