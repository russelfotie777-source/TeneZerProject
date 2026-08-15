<?php
require_once '../mfbase/database.php';
session_start();

if (isset($_SESSION['owner_logged_in']) && $_SESSION['owner_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM owners WHERE phone = ?");
    $stmt->execute([$phone]);
    $owner = $stmt->fetch();

    if ($owner && password_verify($password, $owner['password'])) {
        $_SESSION['owner_logged_in'] = true;
        $_SESSION['owner_id'] = $owner['id'];
        $_SESSION['owner_name'] = $owner['agency_name'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Numéro de téléphone ou mot de passe incorrect.';
    }
}

$page_title = 'Connexion';
require_once 'includes_owner/header_owner.php';
?>

<main class="max-w-md mx-auto px-6 py-20">
    <h1 class="font-serif text-3xl font-black text-tenezer-dark mb-2 text-center">Espace Propriétaire</h1>
    <p class="text-sm text-gray-500 text-center mb-10">Connectez-vous pour gérer vos biens.</p>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['logged_out'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">Vous avez été déconnecté.</div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 space-y-4">
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Téléphone</label>
            <input type="tel" name="phone" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Mot de passe</label>
            <input type="password" name="password" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
        </div>
        <button type="submit" class="w-full bg-tenezer-dark text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">Se connecter</button>
        <p class="text-center text-xs text-gray-400">Pas encore de compte ? <a href="inscription.php" class="text-tenezer-gold font-bold">S'inscrire</a></p>
    </form>
</main>

<?php require_once 'includes_owner/footer_owner.php'; ?>
