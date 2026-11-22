<?php

date_default_timezone_set('Africa/Douala');

define('DB_HOST', 'localhost');
define('DB_NAME', 'tenezerc_base');
define('DB_USER', 'tenezerc_baseUser');
define('DB_PASS', 'F4t5sef8@');
define('DB_CHARSET', 'utf8mb4');
define('ENVIRONMENT', 'production');

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
];

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $pdo->exec("SET time_zone = '+01:00'"); 
    
} catch (PDOException $e) {
    if (ENVIRONMENT === 'development') {
        die("Erreur de connexion : " . $e->getMessage());
    } else {
        error_log("DB Error: " . $e->getMessage());
        die("Une erreur technique est survenue. Veuillez réessayer ultérieurement.");
    }
}


function executeQuery($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Erreur SQL : " . $e->getMessage());
        throw $e;
    }
}

function beginTransaction() { global $pdo; return $pdo->beginTransaction(); }
function commitTransaction() { global $pdo; return $pdo->commit(); }
function rollbackTransaction() { global $pdo; return $pdo->rollBack(); }