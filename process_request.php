<?php
require_once 'mfbase/database.php';

header('Content-Type: application/json; charset=utf-8');

$allowedModules = ['location', 'achat_vente', 'meuble', 'service_menager', 'objet_perdu'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    exit;
}

$module = $_POST['module'] ?? '';
$firstName = trim($_POST['first_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (!in_array($module, $allowedModules, true) || $firstName === '' || $phone === '') {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => "Champs obligatoires manquants (nom, téléphone)."]);
    exit;
}

$lastName = trim($_POST['last_name'] ?? '') ?: null;
$whatsapp = trim($_POST['whatsapp'] ?? '') ?: $phone;
$email = trim($_POST['email'] ?? '') ?: null;
$city = trim($_POST['city'] ?? '') ?: null;
$subType = trim($_POST['sub_type'] ?? '') ?: null;
$propertyId = !empty($_POST['property_id']) ? (int)$_POST['property_id'] : null;
$extra = is_array($_POST['extra'] ?? null) ? $_POST['extra'] : [];
$payload = json_encode($extra, JSON_UNESCAPED_UNICODE);

try {
    $stmt = $pdo->prepare("INSERT INTO requests (module, sub_type, property_id, first_name, last_name, phone, whatsapp, email, city, payload)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$module, $subType, $propertyId, $firstName, $lastName, $phone, $whatsapp, $email, $city, $payload]);
    $requestId = (int)$pdo->lastInsertId();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => "Erreur d'enregistrement."]);
    exit;
}

// Notification email à Tenezer — best effort, une erreur d'envoi ne doit pas faire perdre la demande déjà enregistrée
try {
    require_once __DIR__ . '/admin/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/admin/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/admin/PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'globallogistique33@gmail.com';
    $mail->Password = 'gejlyzzxmlyieend';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('globallogistique33@gmail.com', 'Tenezer Immobilier');
    $mail->addAddress('ztegnidemanou@gmail.com', 'Tenezer');
    $mail->isHTML(true);
    $mail->Subject = 'Nouvelle demande Tenezer #' . $requestId . ' (' . $module . ')';

    $extraLines = '';
    foreach ($extra as $k => $v) {
        $extraLines .= '<p style="margin:4px 0;"><strong>' . htmlspecialchars($k) . ':</strong> ' . htmlspecialchars(is_array($v) ? implode(', ', $v) : (string)$v) . '</p>';
    }

    $mail->Body = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden;'>
        <div style='background:#0B0B0B;padding:24px;color:#F1A80A;'>
            <h2 style='margin:0;'>Nouvelle demande — " . htmlspecialchars($module) . "</h2>
        </div>
        <div style='padding:24px;color:#333;'>
            <p style='margin:4px 0;'><strong>Nom :</strong> " . htmlspecialchars(trim($firstName . ' ' . ($lastName ?? ''))) . "</p>
            <p style='margin:4px 0;'><strong>Téléphone :</strong> " . htmlspecialchars($phone) . "</p>
            <p style='margin:4px 0;'><strong>WhatsApp :</strong> " . htmlspecialchars($whatsapp) . "</p>
            <p style='margin:4px 0;'><strong>Ville :</strong> " . htmlspecialchars($city ?? '-') . "</p>
            $extraLines
        </div>
    </div>";
    $mail->send();
} catch (\Throwable $e) {
    // Envoi email en échec : ignoré silencieusement, la demande reste enregistrée en base
}

echo json_encode(['status' => 'success', 'id' => $requestId]);
