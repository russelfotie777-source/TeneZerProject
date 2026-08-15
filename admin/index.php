<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';

$moduleLabels = [
    'location'        => 'Location',
    'achat_vente'      => 'Achat / Vente',
    'meuble'           => 'Meublé',
    'service_menager'  => 'Service ménager',
    'objet_perdu'      => 'Objet perdu',
];

try {
    $total_properties = (int)$pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'active'")->fetchColumn();
    $pending_owners = (int)$pdo->query("SELECT COUNT(*) FROM owners WHERE status = 'pending'")->fetchColumn();
    $pending_requests = (int)$pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'pending'")->fetchColumn();
    $week_requests = (int)$pdo->query("SELECT COUNT(*) FROM requests WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

    $recent_requests = $pdo->query("
        SELECT r.*, p.title AS property_title
        FROM requests r
        LEFT JOIN properties p ON r.property_id = p.id
        ORDER BY r.id DESC
        LIMIT 6
    ")->fetchAll();

} catch (Exception $e) {
    $total_properties = 0; $pending_owners = 0; $pending_requests = 0; $week_requests = 0; $recent_requests = [];
    $db_error = $e->getMessage();
}

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';
?>

<header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 flex-shrink-0">
    <div class="flex items-center gap-4">
        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">Tableau de bord principal</h2>
    </div>
    <div class="flex items-center gap-3">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Tenezer</span>
    </div>
</header>

<main class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8">

    <?php if (isset($db_error)): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs font-mono">
        <i class="fas fa-triangle-exclamation mr-2"></i> Erreur de flux interceptée (le nouveau schéma a-t-il été importé ?) : <?php echo htmlspecialchars($db_error); ?>
    </div>
    <?php endif; ?>

    <div class="mb-10">
        <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Vue d'ensemble de l'activité</h1>
        <p class="text-sm text-slate-500 mt-1">Supervisez en temps réel les indicateurs de Tenezer Immobilier.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Biens publiés</p>
                <h3 class="text-2xl font-black text-slate-900 font-mono"><?php echo $total_properties; ?></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-building"></i>
            </div>
        </div>

        <a href="manage_owners.php?status=pending" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:border-amber-300 transition-all">
            <div>
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Propriétaires en attente</p>
                <h3 class="text-2xl font-black text-slate-900 font-mono"><?php echo $pending_owners; ?></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-user-clock"></i>
            </div>
        </a>

        <a href="manage_requests.php" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:border-blue-300 transition-all">
            <div>
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Demandes en attente</p>
                <h3 class="text-2xl font-black text-slate-900 font-mono"><?php echo $pending_requests; ?></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-inbox"></i>
            </div>
        </a>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Demandes (7 jours)</p>
                <h3 class="text-2xl font-black text-slate-900 font-mono"><?php echo $week_requests; ?></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Dernières demandes reçues</h3>
            <a href="manage_requests.php" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-brand-gold">Tout voir &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[10px] uppercase tracking-wider font-bold">
                        <th class="px-6 py-4">Réf.</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Module</th>
                        <th class="px-6 py-4">Bien concerné</th>
                        <th class="px-6 py-4 text-right">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($recent_requests)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-xs text-slate-400 font-medium">Aucune demande reçue pour le moment.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recent_requests as $r): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-xs text-slate-400">#TNZ-<?php echo str_pad((string)$r['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900"><?php echo htmlspecialchars(trim($r['first_name'] . ' ' . ($r['last_name'] ?? ''))); ?></div>
                            <div class="text-xs text-slate-400"><?php echo htmlspecialchars($r['phone']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 text-[9px] font-black rounded bg-slate-100 text-slate-600 uppercase tracking-wider"><?php echo htmlspecialchars($moduleLabels[$r['module']] ?? $r['module']); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-700 max-w-[200px] truncate"><?php echo htmlspecialchars($r['property_title'] ?? '—'); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider
                                <?php echo $r['status'] === 'closed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($r['status'] === 'processing' ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-amber-50 text-amber-600 border border-amber-200'); ?>">
                                <?php echo ['pending' => 'En attente', 'processing' => 'En cours', 'closed' => 'Clôturée'][$r['status']] ?? $r['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once 'includes_admin/footer_admin.php'; ?>
