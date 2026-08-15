<?php
require_once '../mfbase/database.php';
session_start();

if (isset($_SESSION['owner_logged_in']) && $_SESSION['owner_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agency_name = trim($_POST['agency_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($agency_name === '' || $phone === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM owners WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $error = 'Un compte existe déjà avec ce numéro de téléphone.';
        } else {
            $cni_photo_url = null;
            if (!empty($_FILES['cni_photo']['name']) && $_FILES['cni_photo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['cni_photo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) && @getimagesize($_FILES['cni_photo']['tmp_name'])) {
                    $dir = __DIR__ . '/../assets/uploads/owners';
                    if (!is_dir($dir)) mkdir($dir, 0777, true);
                    $filename = uniqid('cni_') . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['cni_photo']['tmp_name'], $dir . '/' . $filename)) {
                        $cni_photo_url = $filename;
                    }
                }
            }

            $stmt = $pdo->prepare("INSERT INTO owners (agency_name, phone, email, password, city, cni_photo_url, status)
                                    VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$agency_name, $phone, $email ?: null, password_hash($password, PASSWORD_DEFAULT), $city ?: null, $cni_photo_url]);

            $success = true;
        }
    }
}

$page_title = 'Inscription';
require_once 'includes_owner/header_owner.php';
?>

<main class="max-w-xl mx-auto px-6 py-16">
    <?php if ($success): ?>
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-10 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-tenezer-gold/10 text-tenezer-gold flex items-center justify-center text-2xl mb-6"><i class="fas fa-clock"></i></div>
            <h1 class="font-serif text-2xl font-black text-tenezer-dark mb-4">Compte créé, en attente de validation</h1>
            <p class="text-sm text-gray-500 mb-8">Votre compte partenaire a été enregistré. Un administrateur Tenezer va vérifier vos informations avant que vous puissiez publier des biens.</p>
            <a href="login.php" class="inline-block px-8 py-3 bg-tenezer-dark text-white rounded-full font-black text-xs uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">Se connecter</a>
        </div>
    <?php else: ?>
        <h1 class="font-serif text-3xl font-black text-tenezer-dark mb-2 text-center">Devenir propriétaire partenaire</h1>
        <p class="text-sm text-gray-500 text-center mb-10">Créez votre compte, faites-le valider, puis publiez vos biens.</p>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 space-y-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nom de l'agence / du propriétaire *</label>
                <input type="text" name="agency_name" required value="<?php echo htmlspecialchars($_POST['agency_name'] ?? ''); ?>" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Téléphone *</label>
                    <input type="tel" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Ville</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Mot de passe *</label>
                    <input type="password" name="password" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Confirmer *</label>
                    <input type="password" name="password_confirm" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Photo de votre CNI</label>
                <input type="file" name="cni_photo" accept="image/*" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm">
                <p class="text-[10px] text-gray-400 mt-1">Utilisée uniquement pour vérifier votre identité avant validation.</p>
            </div>
            <button type="submit" class="w-full bg-tenezer-dark text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">Créer mon compte</button>
            <p class="text-center text-xs text-gray-400">Déjà inscrit ? <a href="login.php" class="text-tenezer-gold font-bold">Se connecter</a></p>
        </form>
    <?php endif; ?>
</main>

<?php require_once 'includes_owner/footer_owner.php'; ?>
