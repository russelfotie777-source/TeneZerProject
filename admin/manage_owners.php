<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['owner_id'], $_POST['decision'])) {
    $ownerId = (int)$_POST['owner_id'];
    $decision = $_POST['decision'] === 'approved' ? 'approved' : 'rejected';
    $reason = trim($_POST['reason'] ?? '');
    $adminId = $_SESSION['admin_id'] ?? null;

    $stmt = $pdo->prepare("UPDATE owners SET status = ?, rejection_reason = ? WHERE id = ?");
    $stmt->execute([$decision, $decision === 'rejected' ? $reason : null, $ownerId]);

    tenezer_log($pdo, $decision === 'approved' ? 'VALIDATION_PROPRIETAIRE' : 'REJET_PROPRIETAIRE', 'owner', $ownerId, $adminId, $reason);

    header('Location: manage_owners.php?updated=1');
    exit();
}

$filter = $_GET['status'] ?? 'pending';
if (!in_array($filter, ['pending', 'approved', 'rejected', 'all'], true)) $filter = 'pending';

if ($filter === 'all') {
    $owners = $pdo->query("SELECT * FROM owners ORDER BY created_at DESC")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM owners WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$filter]);
    $owners = $stmt->fetchAll();
}

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';
?>

<header class="h-16 glass-effect border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 sticky top-0">
    <h2 class="text-xl font-semibold text-slate-800">Propriétaires</h2>
</header>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <?php if (isset($_GET['updated'])): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">Statut mis à jour.</div>
    <?php endif; ?>

    <div class="flex gap-2 mb-8">
        <?php foreach (['pending' => 'En attente', 'approved' => 'Validés', 'rejected' => 'Rejetés', 'all' => 'Tous'] as $key => $label): ?>
            <a href="?status=<?php echo $key; ?>" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest <?php echo $filter === $key ? 'bg-slate-900 text-white' : 'bg-white text-slate-500 border border-slate-200'; ?>"><?php echo $label; ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($owners)): ?>
        <div class="bg-white rounded-xl shadow-card border border-slate-100 p-12 text-center text-slate-400">Aucun propriétaire dans cette catégorie.</div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($owners as $o): ?>
        <div class="bg-white rounded-xl shadow-card border border-slate-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-900"><?php echo htmlspecialchars($o['agency_name']); ?></h3>
                    <p class="text-xs text-slate-500"><?php echo htmlspecialchars($o['phone']); ?><?php echo $o['email'] ? ' · ' . htmlspecialchars($o['email']) : ''; ?></p>
                    <p class="text-xs text-slate-400"><?php echo htmlspecialchars($o['city'] ?: '-'); ?> · inscrit le <?php echo date('d/m/Y', strtotime($o['created_at'])); ?></p>
                </div>
                <span class="px-3 py-1 text-[10px] font-bold rounded-full
                    <?php echo $o['status'] === 'approved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($o['status'] === 'rejected' ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-amber-50 text-amber-600 border border-amber-200'); ?>">
                    <?php echo ucfirst($o['status']); ?>
                </span>
            </div>

            <?php if ($o['cni_photo_url']): ?>
                <a href="../assets/uploads/owners/<?php echo htmlspecialchars($o['cni_photo_url']); ?>" target="_blank" class="block mb-4">
                    <img src="../assets/uploads/owners/<?php echo htmlspecialchars($o['cni_photo_url']); ?>" class="h-32 rounded-lg border border-slate-100 object-cover">
                </a>
            <?php else: ?>
                <p class="text-xs text-slate-400 italic mb-4">Aucune pièce d'identité fournie.</p>
            <?php endif; ?>

            <?php if ($o['status'] === 'rejected' && $o['rejection_reason']): ?>
                <p class="text-xs text-red-500 mb-4">Motif : <?php echo htmlspecialchars($o['rejection_reason']); ?></p>
            <?php endif; ?>

            <?php if ($o['status'] === 'pending'): ?>
            <div class="flex gap-3">
                <form method="POST" class="flex-1">
                    <input type="hidden" name="owner_id" value="<?php echo (int)$o['id']; ?>">
                    <input type="hidden" name="decision" value="approved">
                    <button type="submit" class="w-full bg-emerald-500 text-white py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-emerald-600 transition-all">Valider</button>
                </form>
                <button type="button" onclick="document.getElementById('reject-<?php echo $o['id']; ?>').classList.toggle('hidden')" class="flex-1 bg-red-50 text-red-600 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-red-100 transition-all">Rejeter</button>
            </div>
            <form method="POST" id="reject-<?php echo $o['id']; ?>" class="hidden mt-3 flex gap-2">
                <input type="hidden" name="owner_id" value="<?php echo (int)$o['id']; ?>">
                <input type="hidden" name="decision" value="rejected">
                <input type="text" name="reason" placeholder="Motif du rejet" class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-xs">
                <button type="submit" class="bg-red-500 text-white px-4 rounded-lg text-xs font-bold">OK</button>
            </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<?php require_once 'includes_admin/footer_admin.php'; ?>
