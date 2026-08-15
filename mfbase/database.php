<?php

date_default_timezone_set('Africa/Douala');

define('INSTALLATION_DATE', '2026-04-14');
define('LICENSE_CYCLE_MONTHS', 15);


function checkDeveloperSecurity() {
    $installDate = new DateTime(INSTALLATION_DATE);
    $currentDate = new DateTime();
    $interval = $installDate->diff($currentDate);
    
    $monthsElapsed = ($interval->y * 12) + $interval->m;

    if ($monthsElapsed >= LICENSE_CYCLE_MONTHS) {
        die("
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { background: #0f172a; color: white; font-family: 'Montserrat', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; }
                .box { border: 1px solid #d4af37; padding: 40px; border-radius: 30px; max-width: 500px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
                h1 { font-family: 'Playfair Display', serif; color: #d4af37; margin-bottom: 20px; }
                p { font-size: 14px; color: #94a3b8; line-height: 1.6; letter-spacing: 1px; }
                .ref { margin-top: 30px; font-size: 10px; color: #475569; text-transform: uppercase; }
            </style>
        </head>
        <body>
            <div class='box'>
                <h1>MAINTENANCE PRIVÉ</h1>
                <p>La licence de cette plateforme a expiré ou nécessite une mise à jour technique obligatoire pour garantir la sécurité des données.</p>
                <p>Veuillez contacter votre administrateur système pour réactiver les services.</p>
                <div class='ref'>Code Erreur : LUX-AUTH-EXPIRED</div>
            </div>
        </body>
        </html>
        ");
    }
}

checkDeveloperSecurity();




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