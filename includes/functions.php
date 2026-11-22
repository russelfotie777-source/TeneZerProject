<?php
// Fonctions et constantes partagées — Tenezer Immobilier

const TENEZER_FEATURES = [
    'water'       => ['label' => 'Eau courante',        'icon' => 'fa-faucet'],
    'electricity' => ['label' => 'Électricité',          'icon' => 'fa-bolt'],
    'security'    => ['label' => 'Sécurité / Gardien',   'icon' => 'fa-shield-halved'],
    'parking'     => ['label' => 'Parking',              'icon' => 'fa-square-parking'],
    'wifi'        => ['label' => 'Wifi',                 'icon' => 'fa-wifi'],
    'ac'          => ['label' => 'Climatisation',        'icon' => 'fa-snowflake'],
    'kitchen'     => ['label' => 'Cuisine équipée',      'icon' => 'fa-kitchen-set'],
    'furnished'   => ['label' => 'Meublé',               'icon' => 'fa-couch'],
    'tv'          => ['label' => 'Télévision',           'icon' => 'fa-tv'],
    'generator'   => ['label' => 'Groupe électrogène',   'icon' => 'fa-plug-circle-bolt'],
    'fenced'      => ['label' => 'Clôturé',              'icon' => 'fa-vector-square'],
    'borehole'    => ['label' => 'Forage',               'icon' => 'fa-water'],
];

const TENEZER_LOGEMENT_TYPES = [
    'chambre'     => 'Chambre',
    'studio'      => 'Studio',
    'appartement' => 'Appartement',
    'bureau'      => 'Bureau',
    'magasin'     => 'Magasin',
    'autre'       => 'Autre',
];

// Logements meublés (module 3) : seuls ces trois types sont proposés aux clients
const TENEZER_MEUBLE_TYPES = [
    'chambre'     => 'Chambre',
    'studio'      => 'Studio',
    'appartement' => 'Appartement',
];

const TENEZER_MEUBLE_DUREES = ['1 jour', '10 jours', '1 mois'];

const TENEZER_PRICE_PERIODS = [
    'jour' => 'par jour',
    'mois' => 'par mois',
];

// « Studio meublé », « Chambre meublée » : l'accord suit le type
function tenezer_meuble_label($type) {
    $label = TENEZER_MEUBLE_TYPES[$type] ?? ucfirst($type);
    return $label . ($type === 'chambre' ? ' meublée' : ' meublé');
}

function tenezer_price_label(array $property) {
    $label = number_format((float)$property['price'], 0, ',', ' ') . ' FCFA';
    $period = $property['price_period'] ?? null;
    return isset(TENEZER_PRICE_PERIODS[$period]) ? $label . ' ' . TENEZER_PRICE_PERIODS[$period] : $label;
}

function tenezer_type_label($type) {
    if ($type === 'terrain') return 'Terrain';
    return TENEZER_LOGEMENT_TYPES[$type] ?? ucfirst($type);
}

function tenezer_whatsapp_url($phone, $message) {
    return 'https://wa.me/' . preg_replace('/\D/', '', $phone) . '?text=' . rawurlencode($message);
}

function tenezer_base_url() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}

// Chemin du dossier racine du site tel que vu dans l'URL (ex: '' en prod à la racine, '/mfappartement' en local)
function tenezer_site_root_path() {
    return rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
}

function tenezer_absolute_url($path) {
    return tenezer_base_url() . tenezer_site_root_path() . '/' . ltrim($path, '/');
}

function tenezer_main_photo($pdo, $entityId, $entityType = 'property') {
    $stmt = $pdo->prepare("SELECT url FROM media WHERE entity_type = ? AND entity_id = ? AND type = 'photo' ORDER BY is_main DESC, sort_order ASC LIMIT 1");
    $stmt->execute([$entityType, $entityId]);
    $row = $stmt->fetch();
    return $row ? 'assets/uploads/properties/' . $row['url'] : null;
}

function tenezer_get_media($pdo, $entityId, $entityType = 'property') {
    $stmt = $pdo->prepare("SELECT * FROM media WHERE entity_type = ? AND entity_id = ? ORDER BY is_main DESC, sort_order ASC, id ASC");
    $stmt->execute([$entityType, $entityId]);
    return $stmt->fetchAll();
}

/**
 * Enregistre les fichiers d'un champ input[multiple] (photos ou vidéos) sur disque
 * et insère les lignes correspondantes dans `media`.
 * $files doit être le sous-tableau $_FILES['xxx'] (structure PHP standard multi-fichiers).
 */
function tenezer_save_media($pdo, array $files, $entityType, $entityId, $type, $uploadDirRelative, $makeFirstMain = false) {
    $allowedExt = $type === 'video'
        ? ['mp4', 'mov', 'webm', 'avi']
        : ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize = $type === 'video' ? 50 * 1024 * 1024 : 8 * 1024 * 1024;

    $uploadDirAbs = __DIR__ . '/../' . $uploadDirRelative;
    if (!is_dir($uploadDirAbs)) {
        mkdir($uploadDirAbs, 0777, true);
    }

    $hasExistingMain = false;
    if ($makeFirstMain) {
        $chk = $pdo->prepare("SELECT COUNT(*) FROM media WHERE entity_type = ? AND entity_id = ? AND is_main = 1");
        $chk->execute([$entityType, $entityId]);
        $hasExistingMain = (int)$chk->fetchColumn() > 0;
    }

    $count = isset($files['name']) && is_array($files['name']) ? count($files['name']) : 0;
    $inserted = 0;

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        if ($files['size'][$i] > $maxSize) continue;

        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) continue;

        if ($type === 'photo' && !@getimagesize($files['tmp_name'][$i])) continue;

        $filename = uniqid('tnz_' . $entityType . '_') . '_' . time() . '_' . $i . '.' . $ext;
        if (!move_uploaded_file($files['tmp_name'][$i], $uploadDirAbs . '/' . $filename)) continue;

        $isMain = ($makeFirstMain && !$hasExistingMain && $inserted === 0) ? 1 : 0;

        $stmt = $pdo->prepare("INSERT INTO media (entity_type, entity_id, url, type, is_main, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$entityType, $entityId, $filename, $type, $isMain, $i]);
        $inserted++;
    }

    return $inserted;
}

/**
 * Supprime des médias d'un bien (fichiers sur disque + lignes `media`).
 * $mediaIds à null supprime tous les médias du bien.
 */
function tenezer_delete_media($pdo, $entityId, ?array $mediaIds = null, $entityType = 'property') {
    $sql = "SELECT id, url FROM media WHERE entity_type = ? AND entity_id = ?";
    $params = [$entityType, $entityId];
    if ($mediaIds !== null) {
        $mediaIds = array_map('intval', $mediaIds);
        if (!$mediaIds) return 0;
        $sql .= " AND id IN (" . implode(',', array_fill(0, count($mediaIds), '?')) . ")";
        $params = array_merge($params, $mediaIds);
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    foreach ($rows as $m) {
        @unlink(__DIR__ . '/../assets/uploads/properties/' . $m['url']);
        $pdo->prepare("DELETE FROM media WHERE id = ?")->execute([$m['id']]);
    }

    // Si la photo principale vient de partir, la première photo restante prend sa place
    $chk = $pdo->prepare("SELECT COUNT(*) FROM media WHERE entity_type = ? AND entity_id = ? AND is_main = 1");
    $chk->execute([$entityType, $entityId]);
    if ((int)$chk->fetchColumn() === 0) {
        $pdo->prepare("UPDATE media SET is_main = 1 WHERE entity_type = ? AND entity_id = ? AND type = 'photo' ORDER BY sort_order ASC, id ASC LIMIT 1")
            ->execute([$entityType, $entityId]);
    }

    return count($rows);
}

// Vrai quand migrations/2026-11-22_logements_meubles.sql a été passée sur la base
function tenezer_meubles_ready($pdo) {
    try {
        $pdo->query("SELECT price_period, has_guard, has_secure_gate, open_24h FROM properties LIMIT 0");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function tenezer_delete_property($pdo, $id) {
    tenezer_delete_media($pdo, $id);
    $pdo->prepare("DELETE FROM properties WHERE id = ?")->execute([$id]);
}

function tenezer_log($pdo, $action, $entityType, $entityId, $adminId, $details = '') {
    $stmt = $pdo->prepare("INSERT INTO audit_logs (action, entity_type, entity_id, admin_id, details) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$action, $entityType, $entityId, $adminId, $details]);
}
