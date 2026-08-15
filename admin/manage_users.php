<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';

// Sécurité : Seul le Super Admin accède à cette page
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    header('Location: index.php');
    exit();
}

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';

// Récupération des utilisateurs
$users = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC")->fetchAll();

// Traitement de la suppression (Ajax ou simple POST)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ? AND username != ?");
    $stmt->execute([$id, $_SESSION['admin_username']]);
    header('Location: manage_users.php?success=1');
    exit();
}
?>

<header class="h-16 glass-effect border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 sticky top-0">
    <div class="flex items-center">
        <h2 class="text-xl font-semibold text-slate-800">Gestion des Comptes</h2>
    </div>
    <a href="add_user.php" class="bg-brand-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:shadow-lg transition-all flex items-center gap-2">
        <i class="fas fa-user-plus"></i> Nouveau Membre
    </a>
</header>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Membres de l'équipe</h1>
        <p class="text-sm text-slate-500 mt-1">Gérez les accès et les permissions de vos collaborateurs.</p>
    </div>

    <div class="bg-white rounded-xl shadow-card border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100">
                    <th class="px-6 py-4 font-semibold">Membre</th>
                    <th class="px-6 py-4 font-semibold">Rôle</th>
                    <th class="px-6 py-4 font-semibold">Statut</th>
                    <th class="px-6 py-4 font-semibold">Créé le</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-900 text-brand-gold flex items-center justify-center font-bold">
                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                <div class="text-xs text-slate-500">@<?php echo htmlspecialchars($user['username']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($user['role'] == 'super_admin'): ?>
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-600 border border-amber-200">Super Admin</span>
                        <?php else: ?>
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-50 text-blue-600 border border-blue-200"><?php echo ucfirst($user['role']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="flex items-center gap-1.5 text-sm <?php echo $user['is_active'] ? 'text-emerald-600' : 'text-red-600'; ?>">
                            <span class="w-2 h-2 rounded-full <?php echo $user['is_active'] ? 'bg-emerald-500' : 'bg-red-500'; ?>"></span>
                            <?php echo $user['is_active'] ? 'Actif' : 'Suspendu'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="text-slate-400 hover:text-brand-primary transition-colors"><i class="fas fa-edit"></i></a>
                        <?php if($user['username'] !== $_SESSION['admin_username']): ?>
                        <button onclick="confirmDelete(<?php echo $user['id']; ?>)" class="text-slate-400 hover:text-red-500 transition-colors"><i class="fas fa-trash"></i></button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: "Cet utilisateur n'aura plus accès à l'administration !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'manage_users.php?delete=' + id;
        }
    })
}
</script>