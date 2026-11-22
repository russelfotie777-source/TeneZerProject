<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';
require_once '../includes/functions.php';

$adminId = $_SESSION['admin_id'] ?? null;

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    tenezer_delete_property($pdo, $id);
    tenezer_log($pdo, 'DELETE', 'property', $id, $adminId, 'Logement meublé');
    header('Location: manage_meubles.php?deleted=1');
    exit();
}

// Publier / retirer
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE properties SET status = IF(status='active','inactive','active') WHERE id = ? AND category = 'meuble'")->execute([$id]);
    tenezer_log($pdo, 'UPDATE', 'property', $id, $adminId, 'Statut basculé (meublé)');
    header('Location: manage_meubles.php');
    exit();
}

$meubles = [];
$schemaError = !tenezer_meubles_ready($pdo);
if (!$schemaError) {
    $meubles = $pdo->query("SELECT p.*,
                                   (SELECT COUNT(*) FROM media m WHERE m.entity_type = 'property' AND m.entity_id = p.id AND m.type = 'photo') AS nb_photos,
                                   (SELECT COUNT(*) FROM media m WHERE m.entity_type = 'property' AND m.entity_id = p.id AND m.type = 'video') AS nb_videos
                            FROM properties p
                            WHERE p.category = 'meuble'
                            ORDER BY p.created_at DESC")->fetchAll();
}

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';
?>

<header class="h-16 glass-effect border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 sticky top-0">
    <h2 class="text-xl font-semibold text-slate-800">Logements meublés</h2>
    <a href="meuble-form.php" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-brand-gold hover:text-slate-900 transition-colors flex items-center gap-2">
        <i class="fas fa-plus"></i> Publier un logement
    </a>
</header>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <?php if ($schemaError): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
            La base de données n'est pas encore à jour pour les logements meublés. Exécutez le fichier <code>migrations/2026-11-22_logements_meubles.sql</code> dans phpMyAdmin, puis rechargez cette page.
        </div>
    <?php endif; ?>
    <?php if (($_GET['saved'] ?? '') === 'created'): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">Logement publié. Il est visible sur la page Logements meublés.</div>
    <?php elseif (($_GET['saved'] ?? '') === 'updated'): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">Modifications enregistrées.</div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">Logement supprimé.</div>
    <?php endif; ?>
    <?php if (!empty($_GET['skipped'])): $skipped = (int)$_GET['skipped']; ?>
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm">
            <?php echo $skipped; ?> fichier<?php echo $skipped > 1 ? 's n\'ont' : ' n\'a'; ?> pas été ajouté<?php echo $skipped > 1 ? 's' : ''; ?> : format ou taille refusé. Photos en JPG, PNG ou WEBP (8 Mo au plus), vidéos en MP4, MOV, WEBM ou AVI (50 Mo au plus). Modifiez le logement pour les ajouter à nouveau.
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-card border border-slate-100 overflow-hidden overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-xs border-b border-slate-100">
                    <th class="px-6 py-4 font-semibold">Logement</th>
                    <th class="px-6 py-4 font-semibold text-right">Prix</th>
                    <th class="px-6 py-4 font-semibold">Sécurité et accès</th>
                    <th class="px-6 py-4 font-semibold">Médias</th>
                    <th class="px-6 py-4 font-semibold">Statut</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($meubles as $p): $photo = tenezer_main_photo($pdo, $p['id']); ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <?php if ($photo): ?>
                                    <img src="../<?php echo htmlspecialchars($photo); ?>" alt="" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-image text-slate-300"></i>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-900 truncate"><?php echo htmlspecialchars($p['title']); ?></div>
                                <div class="text-xs text-slate-500"><?php echo htmlspecialchars(tenezer_type_label($p['type'])); ?> · <?php echo htmlspecialchars(($p['quartier'] ? $p['quartier'] . ', ' : '') . $p['city']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-900 text-right whitespace-nowrap tabular-nums"><?php echo htmlspecialchars(tenezer_price_label($p)); ?></td>
                    <td class="px-6 py-4 text-xs text-slate-600">
                        <div class="flex flex-wrap gap-x-3 gap-y-1">
                            <span class="<?php echo $p['has_guard'] ? 'text-slate-700' : 'text-slate-300 line-through'; ?>">Gardien</span>
                            <span class="<?php echo $p['has_secure_gate'] ? 'text-slate-700' : 'text-slate-300 line-through'; ?>">Barrière</span>
                            <span class="<?php echo $p['open_24h'] ? 'text-slate-700' : 'text-slate-300 line-through'; ?>">24h/24</span>
                        </div>
                        <?php if ($p['distance_route']): ?>
                            <div class="text-slate-400 mt-1"><?php echo htmlspecialchars($p['distance_route']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap tabular-nums">
                        <?php echo (int)$p['nb_photos']; ?> photo<?php echo $p['nb_photos'] > 1 ? 's' : ''; ?>
                        · <?php echo (int)$p['nb_videos']; ?> vidéo<?php echo $p['nb_videos'] > 1 ? 's' : ''; ?>
                    </td>
                    <td class="px-6 py-4">
                        <a href="?toggle=<?php echo (int)$p['id']; ?>" title="<?php echo $p['status'] === 'active' ? 'Retirer de la page publique' : 'Publier sur la page publique'; ?>" class="flex items-center gap-1.5 text-sm <?php echo $p['status'] === 'active' ? 'text-emerald-600' : 'text-slate-500'; ?>">
                            <span class="w-2 h-2 rounded-full <?php echo $p['status'] === 'active' ? 'bg-emerald-500' : 'bg-slate-300'; ?>"></span>
                            <?php echo $p['status'] === 'active' ? 'Publié' : 'Retiré'; ?>
                        </a>
                    </td>
                    <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                        <a href="../detail.php?id=<?php echo (int)$p['id']; ?>" target="_blank" title="Voir l'annonce" class="text-slate-400 hover:text-slate-700 transition-colors"><i class="fas fa-eye"></i></a>
                        <a href="meuble-form.php?id=<?php echo (int)$p['id']; ?>" title="Modifier" class="text-slate-400 hover:text-slate-700 transition-colors"><i class="fas fa-edit"></i></a>
                        <button type="button" onclick="confirmDelete(<?php echo (int)$p['id']; ?>)" title="Supprimer" class="text-slate-400 hover:text-red-500 transition-colors"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($meubles) && !$schemaError): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="font-semibold text-slate-700">Aucun logement publié</p>
                        <p class="text-sm text-slate-500 mt-1">Publiez un logement pour qu'il apparaisse sur la page Logements meublés.</p>
                        <a href="meuble-form.php" class="inline-block mt-4 bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-brand-gold hover:text-slate-900 transition-colors">Publier un logement</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Supprimer ce logement ?',
        text: "L'annonce, ses photos et ses vidéos seront définitivement supprimées.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Supprimer le logement',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = 'manage_meubles.php?delete=' + id;
    });
}
</script>

<?php require_once 'includes_admin/footer_admin.php'; ?>
