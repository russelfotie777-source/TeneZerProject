<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';

// Sécurité : Seul le Super Admin modifie les comptes
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: manage_users.php'); exit(); }

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) { header('Location: manage_users.php'); exit(); }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['full_name'];
    $role = $_POST['role'];
    $is_active = $_POST['is_active'];
    $new_password = $_POST['password'];

    try {
        // Mise à jour de base
        $sql = "UPDATE admins SET full_name = ?, role = ?, is_active = ? WHERE id = ?";
        $params = [$fullname, $role, $is_active, $id];
        
        $pdo->prepare($sql)->execute($params);

        // Si un nouveau mot de passe est saisi, on le met à jour aussi
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?")->execute([$hashed_password, $id]);
        }

        header('Location: manage_users.php?updated=1');
        exit();
    } catch (PDOException $e) {
        $message = "Erreur lors de la mise à jour.";
    }
}

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 flex justify-center items-center bg-slate-50">
    <div class="bg-white rounded-2xl shadow-soft border border-slate-100 p-8 w-full max-w-lg">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                <i class="fas fa-user-edit text-brand-primary"></i> Modifier le profil
            </h2>
            <p class="text-sm text-slate-500 mt-1">Édition du compte de <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
        </div>

        <?php if($message): ?>
            <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nom Complet</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-primary outline-none transition-all bg-slate-50/50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nouveau mot de passe (laisser vide si inchangé)</label>
                <input type="password" name="password" placeholder="••••••••"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-primary outline-none transition-all bg-slate-50/50">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Rôle</label>
                    <select name="role" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-primary outline-none transition-all bg-slate-50/50">
                        <option value="moderateur" <?php echo $user['role'] == 'moderateur' ? 'selected' : ''; ?>>Modérateur</option>
                        <option value="editeur" <?php echo $user['role'] == 'editeur' ? 'selected' : ''; ?>>Éditeur</option>
                        <option value="super_admin" <?php echo $user['role'] == 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Statut</label>
                    <select name="is_active" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-primary outline-none transition-all bg-slate-50/50">
                        <option value="1" <?php echo $user['is_active'] == 1 ? 'selected' : ''; ?>>Actif</option>
                        <option value="0" <?php echo $user['is_active'] == 0 ? 'selected' : ''; ?>>Suspendu</option>
                    </select>
                </div>
            </div>
            
            <div class="pt-6 flex gap-3">
                <button type="submit" class="flex-1 bg-slate-900 text-white py-3.5 rounded-xl font-bold hover:bg-slate-800 transition-all shadow-lg">
                    Sauvegarder
                </button>
                <a href="manage_users.php" class="px-6 py-3.5 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-all text-center">
                    Retour
                </a>
            </div>
        </form>
    </div>
</main>