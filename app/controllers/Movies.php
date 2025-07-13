<?php
class Movies extends Controller
{
    /** GET /movies/search  (or default) */
    public function search(): void
    {
        $query = $_GET['q'] ?? '';
        $movie = null;

        if ($query) {
            $resp  = file_get_contents(
                "https://www.omdbapi.com/?apikey={$_ENV['OMDB_API_KEY']}&t=" . urlencode($query)
            );
            $movie = json_decode($resp, true)['Response'] === 'True' ? json_decode($resp, true) : null;
        }

        $this->view('movies/search', ['movie' => $movie, 'query' => $query]);
    }
}
