<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';
require_once '../includes/functions.php';

$adminId = $_SESSION['admin_id'] ?? null;
$adminName = $_SESSION['admin_username'] ?? 'Administrateur';

// Mise à jour de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['status'])) {
    $reqId = (int)$_POST['request_id'];
    $status = in_array($_POST['status'], ['pending', 'processing', 'closed'], true) ? $_POST['status'] : 'pending';
    $notes = trim($_POST['notes'] ?? '');

    $stmt = $pdo->prepare("UPDATE requests SET status = ?, notes = ?, handled_by = ? WHERE id = ?");
    $stmt->execute([$status, $notes, $adminId, $reqId]);

    tenezer_log($pdo, 'UPDATE', 'request', $reqId, $adminId, "Statut → $status par $adminName. Note : $notes");

    header('Location: manage_requests.php?module=' . urlencode($_POST['current_module'] ?? 'all') . '&updated=1');
    exit();
}

$moduleLabels = [
    'location'        => 'Location',
    'achat_vente'      => 'Achat / Vente',
    'meuble'           => 'Meublé',
    'service_menager'  => 'Service ménager',
    'objet_perdu'      => 'Objet perdu',
];

$module = $_GET['module'] ?? 'all';
$sql = "SELECT r.*, p.title AS property_title, a.username AS handled_by_name
        FROM requests r
        LEFT JOIN properties p ON r.property_id = p.id
        LEFT JOIN admins a ON r.handled_by = a.id";
$params = [];
if (array_key_exists($module, $moduleLabels)) {
    $sql .= " WHERE r.module = ?";
    $params[] = $module;
}
$sql .= " ORDER BY r.created_at DESC LIMIT 300";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';
?>

<header class="h-16 glass-effect border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 sticky top-0">
    <h2 class="text-xl font-semibold text-slate-800">Demandes</h2>
</header>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <?php if (isset($_GET['updated'])): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">Demande mise à jour.</div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2 mb-8">
        <a href="?module=all" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest <?php echo $module === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-500 border border-slate-200'; ?>">Toutes</a>
        <?php foreach ($moduleLabels as $key => $label): ?>
            <a href="?module=<?php echo $key; ?>" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest <?php echo $module === $key ? 'bg-slate-900 text-white' : 'bg-white text-slate-500 border border-slate-200'; ?>"><?php echo $label; ?></a>
        <?php endforeach; ?>
    </div>

    <div class="space-y-4">
        <?php foreach ($requests as $r): $payload = json_decode($r['payload'] ?? '[]', true) ?: []; ?>
        <div class="bg-white rounded-xl shadow-card border border-slate-100 p-6">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                <div>
                    <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 uppercase tracking-widest"><?php echo htmlspecialchars($moduleLabels[$r['module']] ?? $r['module']); ?></span>
                    <?php if ($r['sub_type']): ?>
                        <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-slate-50 text-slate-400 uppercase tracking-widest"><?php echo htmlspecialchars($r['sub_type']); ?></span>
                    <?php endif; ?>
                    <h3 class="font-bold text-slate-900 mt-2"><?php echo htmlspecialchars(trim($r['first_name'] . ' ' . ($r['last_name'] ?? ''))); ?></h3>
                    <p class="text-xs text-slate-500">
                        <i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($r['phone']); ?>
                        <?php if ($r['city']): ?> · <?php echo htmlspecialchars($r['city']); ?><?php endif; ?>
                        <?php if ($r['property_title']): ?> · Bien : <?php echo htmlspecialchars($r['property_title']); ?><?php endif; ?>
                    </p>
                    <p class="text-[10px] text-slate-400 mt-1">#<?php echo (int)$r['id']; ?> · <?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?></p>
                </div>
                <span class="px-3 py-1 text-[10px] font-bold rounded-full
                    <?php echo $r['status'] === 'closed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($r['status'] === 'processing' ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-amber-50 text-amber-600 border border-amber-200'); ?>">
                    <?php echo ['pending' => 'En attente', 'processing' => 'En cours', 'closed' => 'Clôturée'][$r['status']] ?? $r['status']; ?>
                </span>
            </div>

            <?php if (!empty($payload)): ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 p-4 bg-slate-50 rounded-xl text-xs">
                <?php foreach ($payload as $k => $v): if ($v === '' || $v === null) continue; ?>
                    <div><span class="block text-slate-400 uppercase font-bold text-[9px] mb-0.5"><?php echo htmlspecialchars(str_replace('_', ' ', $k)); ?></span><span class="text-slate-700 font-medium"><?php echo htmlspecialchars(is_array($v) ? implode(', ', $v) : $v); ?></span></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($r['notes']): ?>
                <p class="text-xs text-slate-500 mb-4"><i class="fas fa-note-sticky mr-1"></i><?php echo htmlspecialchars($r['notes']); ?><?php if ($r['handled_by_name']): ?> — <span class="italic">traité par <?php echo htmlspecialchars($r['handled_by_name']); ?></span><?php endif; ?></p>
            <?php endif; ?>

            <form method="POST" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="request_id" value="<?php echo (int)$r['id']; ?>">
                <input type="hidden" name="current_module" value="<?php echo htmlspecialchars($module); ?>">
                <select name="status" class="border border-slate-200 rounded-lg px-3 py-2 text-xs">
                    <option value="pending" <?php echo $r['status'] === 'pending' ? 'selected' : ''; ?>>En attente</option>
                    <option value="processing" <?php echo $r['status'] === 'processing' ? 'selected' : ''; ?>>En cours</option>
                    <option value="closed" <?php echo $r['status'] === 'closed' ? 'selected' : ''; ?>>Clôturée</option>
                </select>
                <input type="text" name="notes" placeholder="Note interne" value="<?php echo htmlspecialchars($r['notes'] ?? ''); ?>" class="flex-1 min-w-[180px] border border-slate-200 rounded-lg px-3 py-2 text-xs">
                <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-brand-gold hover:text-slate-900 transition-all">Enregistrer</button>
                <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $r['whatsapp'] ?: $r['phone']); ?>" target="_blank" class="text-emerald-500 hover:text-emerald-600"><i class="fab fa-whatsapp text-lg"></i></a>
            </form>
        </div>
        <?php endforeach; ?>
        <?php if (empty($requests)): ?>
            <div class="bg-white rounded-xl shadow-card border border-slate-100 p-12 text-center text-slate-400">Aucune demande dans cette catégorie.</div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes_admin/footer_admin.php'; ?>
