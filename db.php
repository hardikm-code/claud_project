<?php
// ============================================================
// db.php - Database Connection (PDO Singleton)
// ============================================================

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die('<div style="font-family:sans-serif;padding:40px;background:#fff0f0;border:2px solid #e00;margin:20px;border-radius:8px;">
                    <h2 style="color:#c00;">Database Connection Error</h2>
                    <p>Could not connect to the database. Please ensure MySQL is running and the credentials in config.php are correct.</p>
                    <p style="color:#999;font-size:12px;">Error: ' . htmlspecialchars($e->getMessage()) . '</p>
                </div>');
            }
        }
        return self::$instance;
    }
}

// Helper function for quick access
function db(): PDO {
    return Database::getInstance();
}
