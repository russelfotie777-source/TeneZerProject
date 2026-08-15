<?php
// track_whatsapp.php — tracking générique des clics WhatsApp (tous modules)
require_once 'mfbase/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity_type = isset($_POST['entity_type']) ? substr(trim($_POST['entity_type']), 0, 50) : 'property';
    $entity_id = isset($_POST['entity_id']) ? (int)$_POST['entity_id'] : 0;

    if ($entity_id > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO whatsapp_clicks (entity_type, entity_id) VALUES (?, ?)");
            $stmt->execute([$entity_type, $entity_id]);
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID invalide']);
    }
}
