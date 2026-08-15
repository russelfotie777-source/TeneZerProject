<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';
require_once '../includes/functions.php';

$adminId = $_SESSION['admin_id'] ?? null;

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $mediaRows = $pdo->prepare("SELECT url FROM media WHERE entity_type = 'property' AND entity_id = ?");
    $mediaRows->execute([$id]);
    foreach ($mediaRows->fetchAll() as $m) {
        @unlink(__DIR__ . '/../assets/uploads/properties/' . $m['url']);
    }
    $pdo->prepare("DELETE FROM media WHERE entity_type = 'property' AND entity_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM properties WHERE id = ?")->execute([$id]);
    tenezer_log($pdo, 'DELETE', 'property', $id, $adminId, '');
    header('Location: manage_properties.php?deleted=1');
    exit();
}

// Activer / désactiver
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE properties SET status = IF(status='active','inactive','active') WHERE id = ?")->execute([$id]);
    tenezer_log($pdo, 'UPDATE', 'property', $id, $adminId, 'Statut basculé');
    header('Location: manage_properties.php');
    exit();
}

$category = $_GET['category'] ?? 'all';
$sql = "SELECT p.*, o.agency_name FROM properties p LEFT JOIN owners o ON p.owner_id = o.id";
$params = [];
if (in_array($category, ['logement', 'terrain'], true)) {
    $sql .= " WHERE p.category = ?";
    $params[] = $category;
}
$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';
?>

<header class="h-16 glass-effect border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 sticky top-0">
    <h2 class="text-xl font-semibold text-slate-800">Logements &amp; Terrains</h2>
    <a href="property-form.php" class="bg-brand-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:shadow-lg transition-all flex items-center gap-2">
        <i class="fas fa-plus"></i> Ajouter un bien
    </a>
</header>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <?php if (isset($_GET['deleted'])): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">Bien supprimé.</div>
    <?php endif; ?>

    <div class="flex gap-2 mb-8">
        <?php foreach (['all' => 'Tous', 'logement' => 'Logements', 'terrain' => 'Terrains'] as $key => $label): ?>
            <a href="?category=<?php echo $key; ?>" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest <?php echo $category === $key ? 'bg-slate-900 text-white' : 'bg-white text-slate-500 border border-slate-200'; ?>"><?php echo $label; ?></a>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-xl shadow-card border border-slate-100 overflow-hidden overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100">
                    <th class="px-6 py-4 font-semibold">Bien</th>
                    <th class="px-6 py-4 font-semibold">Propriétaire</th>
                    <th class="px-6 py-4 font-semibold">Prix</th>
                    <th class="px-6 py-4 font-semibold">Statut</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($properties as $p): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($p['title']); ?></div>
                        <div class="text-xs text-slate-500"><?php echo htmlspecialchars(tenezer_type_label($p['type'])); ?> · <?php echo htmlspecialchars($p['city']); ?></div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600"><?php echo htmlspecialchars($p['agency_name'] ?? 'Tenezer (admin)'); ?></td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-900"><?php echo number_format((float)$p['price'], 0, ',', ' '); ?> FCFA</td>
                    <td class="px-6 py-4">
                        <a href="?toggle=<?php echo (int)$p['id']; ?>" class="flex items-center gap-1.5 text-sm <?php echo $p['status'] === 'active' ? 'text-emerald-600' : 'text-red-600'; ?>">
                            <span class="w-2 h-2 rounded-full <?php echo $p['status'] === 'active' ? 'bg-emerald-500' : 'bg-red-500'; ?>"></span>
                            <?php echo $p['status'] === 'active' ? 'Publié' : 'Inactif'; ?>
                        </a>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="property-form.php?id=<?php echo (int)$p['id']; ?>" class="text-slate-400 hover:text-brand-primary transition-colors"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete(<?php echo (int)$p['id']; ?>)" class="text-slate-400 hover:text-red-500 transition-colors"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($properties)): ?>
                <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">Aucun bien pour l'instant.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: "Ce bien et ses photos seront définitivement supprimés.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = 'manage_properties.php?delete=' + id;
    });
}
</script>

<?php require_once 'includes_admin/footer_admin.php'; ?>
