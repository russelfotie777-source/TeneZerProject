<?php
// Lignes de débogage - à retirer après correction
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';

// Sécurité : Vérification du rôle
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') { 
    header('Location: index.php'); 
    exit(); 
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $fullname = $_POST['full_name'] ?? '';
    $pass_raw = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'moderateur';

    if (!empty($username) && !empty($pass_raw)) {
        // PASSWORD_DEFAULT est plus compatible que ARGON2ID sur certains serveurs locaux
        $password = password_hash($pass_raw, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO admins (username, password, full_name, role, is_active) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$username, $password, $fullname, $role]);
            header('Location: manage_users.php?created=1');
            exit();
        } catch (PDOException $e) {
            $message = "Erreur de base de données : " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 flex justify-center items-center bg-slate-50">
    <div class="bg-white rounded-2xl shadow-soft border border-slate-100 p-6 w-full max-w-lg">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                <i class="fas fa-user-plus text-brand-primary"></i> Nouveau Collaborateur
            </h2>
            <p class="text-sm text-slate-500 mt-1">Ajoutez un nouveau membre à l'équipe administrative.</p>
        </div>

        <?php if($message): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg text-sm animate-pulse">
                <i class="fas fa-exclamation-triangle mr-2"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nom Complet</label>
                    <input type="text" name="full_name" required placeholder="ex: Jean Dupont"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-none transition-all bg-slate-50/50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Identifiant (Username)</label>
                    <input type="text" name="username" required placeholder="nom d'utilisateur"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-none transition-all bg-slate-50/50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mot de passe</label>
                    <div class="relative">
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-none transition-all bg-slate-50/50">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Grade / Rôle</label>
                    <select name="role" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-none transition-all bg-slate-50/50 appearance-none cursor-pointer">
                        <option value="moderateur">Modérateur (Réservations)</option>
                        <option value="editeur">Éditeur (Contenu & Photos)</option>
                        <option value="super_admin">Super Administrateur (Total)</option>
                    </select>
                </div>
            </div>
            
            <div class="pt-6 flex flex-col sm:flex-row gap-3">
                <button type="submit" class="flex-1 bg-slate-900 text-white py-3.5 rounded-xl font-bold hover:bg-slate-800 hover:shadow-lg active:scale-95 transition-all">
                    Confirmer la création
                </button>
                <a href="manage_users.php" class="px-6 py-3.5 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-all text-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</main>