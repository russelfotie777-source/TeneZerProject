<?php
require_once '../mfbase/database.php';
require_once '../includes/functions.php';
require_once 'includes_owner/auth_check.php';

$stmt = $pdo->prepare("SELECT * FROM owners WHERE id = ?");
$stmt->execute([$_SESSION['owner_id']]);
$owner = $stmt->fetch();

if (!$owner) {
    header('Location: logout.php');
    exit();
}

// Suppression d'un bien (propriétaire uniquement, sur ses propres biens)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_property_id'])) {
    $propId = (int)$_POST['delete_property_id'];
    $check = $pdo->prepare("SELECT id FROM properties WHERE id = ? AND owner_id = ?");
    $check->execute([$propId, $owner['id']]);
    if ($check->fetch()) {
        $mediaRows = $pdo->prepare("SELECT url FROM media WHERE entity_type = 'property' AND entity_id = ?");
        $mediaRows->execute([$propId]);
        foreach ($mediaRows->fetchAll() as $m) {
            @unlink(__DIR__ . '/../assets/uploads/properties/' . $m['url']);
        }
        $pdo->prepare("DELETE FROM media WHERE entity_type = 'property' AND entity_id = ?")->execute([$propId]);
        $pdo->prepare("DELETE FROM properties WHERE id = ?")->execute([$propId]);
    }
    header('Location: dashboard.php?deleted=1');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM properties WHERE owner_id = ? ORDER BY created_at DESC");
$stmt->execute([$owner['id']]);
$properties = $stmt->fetchAll();

$page_title = 'Mon tableau de bord';
require_once 'includes_owner/header_owner.php';
?>

<main class="max-w-5xl mx-auto px-6 py-12">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="font-serif text-2xl md:text-3xl font-black text-tenezer-dark">Bonjour, <?php echo htmlspecialchars($owner['agency_name']); ?></h1>
            <p class="text-sm text-gray-500">Gérez vos biens publiés sur Tenezer.</p>
        </div>
        <?php if ($owner['status'] === 'approved'): ?>
            <a href="bien-form.php" class="px-6 py-3 bg-tenezer-gold text-tenezer-dark rounded-full font-black text-xs uppercase tracking-widest hover:brightness-110 transition-all whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i>Publier un bien
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">Bien supprimé.</div>
    <?php endif; ?>
    <?php if (isset($_GET['saved'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">Bien enregistré avec succès.</div>
    <?php endif; ?>

    <?php if ($owner['status'] === 'pending'): ?>
        <div class="mb-10 p-6 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-4">
            <i class="fas fa-clock text-amber-500 text-xl mt-0.5"></i>
            <div>
                <h3 class="font-black text-amber-800 text-sm uppercase tracking-wide mb-1">Compte en attente de validation</h3>
                <p class="text-amber-700 text-sm">Un administrateur Tenezer doit valider votre compte avant que vous puissiez publier des biens.</p>
            </div>
        </div>
    <?php elseif ($owner['status'] === 'rejected'): ?>
        <div class="mb-10 p-6 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-4">
            <i class="fas fa-circle-xmark text-red-500 text-xl mt-0.5"></i>
            <div>
                <h3 class="font-black text-red-800 text-sm uppercase tracking-wide mb-1">Compte non validé</h3>
                <p class="text-red-700 text-sm"><?php echo htmlspecialchars($owner['rejection_reason'] ?: "Votre compte n'a pas été validé. Contactez Tenezer pour plus d'informations."); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($properties)): ?>
        <div class="text-center py-20 bg-white rounded-3xl border border-gray-100">
            <i class="fas fa-house text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-sm">Vous n'avez publié aucun bien pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($properties as $p): $photo = tenezer_main_photo($pdo, $p['id']); ?>
                <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-50 flex items-center justify-center flex-shrink-0">
                        <?php if ($photo): ?>
                            <img src="../<?php echo htmlspecialchars($photo); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-image text-gray-300"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-tenezer-dark truncate"><?php echo htmlspecialchars($p['title']); ?></h3>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars(tenezer_type_label($p['type'])); ?> · <?php echo htmlspecialchars($p['city']); ?></p>
                        <span class="inline-block mt-1 text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full <?php echo $p['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; ?>">
                            <?php echo $p['status'] === 'active' ? 'Publié' : 'Inactif'; ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="bien-form.php?id=<?php echo (int)$p['id']; ?>" class="w-9 h-9 rounded-full bg-slate-50 flex items-center justify-center text-tenezer-dark hover:bg-tenezer-gold transition-all"><i class="fas fa-pen text-xs"></i></a>
                        <form method="POST" onsubmit="return confirm('Supprimer ce bien ?');">
                            <input type="hidden" name="delete_property_id" value="<?php echo (int)$p['id']; ?>">
                            <button type="submit" class="w-9 h-9 rounded-full bg-slate-50 flex items-center justify-center text-red-500 hover:bg-red-100 transition-all"><i class="fas fa-trash text-xs"></i></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'includes_owner/footer_owner.php'; ?>
