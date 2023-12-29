<?php

namespace redstar\Models;

/**
 * Třída pro práci s databází
 * @package redstar\Models
 */
class DatabaseModel
{
    /** @var DatabaseModel $database - Instance databázového modelu. */
    private static $database;

    /** @var \PDO $pdo Instance pracující s databází prostřednictvím PDO. */
    private $pdo;

    /**
     * Konstruktor pro tvorbu instance
     */
    private function __construct() {
        $this->pdo = new \PDO("mysql:host=".DB_SERVER."; dbname=".DB_NAME, DB_USER, DB_PASS);

    }

    /**
     * Tovární metoda pro poskytnutí singletonu databázového modelu.
     * @return DatabaseModel Databázový model
     */
    public static function getDatabaseModel() {
        if (empty(self::$database)){
            self::$database = new DatabaseModel();
        }
        return self::$database;
    }

    public function getUserCredentials(string $username) {
        $stmt = $this->pdo->prepare("SELECT username, password FROM users WHERE username = :username");

        $stmt->bindValue(":username", $username);
        $stmt->execute();

        return $stmt->fetch();
    }
}