<?php
class Rating
{
    private PDO $db;
    public function __construct() { $this->db = db(); }

    public function insert(?int $uid, string $title, int $rating): void
    {
        $sql = "INSERT INTO ratings (user_id, movie_title, rating) VALUES (?,?,?)";
        $this->db->prepare($sql)->execute([$uid, $title, $rating]);
    }

    public function avgFor(string $title): ?float
    {
        $stmt = $this->db->prepare(
            "SELECT AVG(rating) AS avg FROM ratings WHERE movie_title = ?"
        );
        $stmt->execute([$title]);
        return $stmt->fetchColumn() ?: null;
    }
}
