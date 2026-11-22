<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';
require_once '../includes/functions.php';

$adminId = $_SESSION['admin_id'] ?? null;
$propertyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$property = null;

if ($propertyId) {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND category = 'meuble'");
    $stmt->execute([$propertyId]);
    $property = $stmt->fetch();
    if (!$property) { header('Location: manage_meubles.php'); exit(); }
}

// Compte les fichiers envoyés qui seront acceptés par tenezer_save_media (mêmes règles)
function meuble_valid_uploads(array $files, array $allowedExt, $maxSize) {
    $valid = 0;
    $count = isset($files['name']) && is_array($files['name']) ? count($files['name']) : 0;
    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK || $files['size'][$i] > $maxSize) continue;
        if (in_array(strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION)), $allowedExt, true)) $valid++;
    }
    return $valid;
}
function meuble_sent_uploads(array $files) {
    return isset($files['error']) && is_array($files['error'])
        ? count(array_filter($files['error'], fn($e) => $e !== UPLOAD_ERR_NO_FILE))
        : 0;
}

$existingMedia = $property ? tenezer_get_media($pdo, $property['id']) : [];
$schemaReady = tenezer_meubles_ready($pdo);
$error = $schemaReady ? '' : "La base de données n'est pas encore à jour pour les logements meublés. Exécutez le fichier migrations/2026-11-22_logements_meubles.sql dans phpMyAdmin, puis rechargez cette page.";
$values = $property ?: ['type' => 'studio', 'price_period' => 'jour', 'status' => 'active', 'has_guard' => 0, 'has_secure_gate' => 0, 'open_24h' => 0];

if ($schemaReady && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        'title'           => trim($_POST['title'] ?? ''),
        'type'            => $_POST['type'] ?? '',
        'city'            => trim($_POST['city'] ?? ''),
        'quartier'        => trim($_POST['quartier'] ?? ''),
        'price'           => $_POST['price'] ?? '',
        'price_period'    => isset(TENEZER_PRICE_PERIODS[$_POST['price_period'] ?? '']) ? $_POST['price_period'] : 'jour',
        'distance_route'  => trim($_POST['distance_route'] ?? ''),
        'has_guard'       => isset($_POST['has_guard']) ? 1 : 0,
        'has_secure_gate' => isset($_POST['has_secure_gate']) ? 1 : 0,
        'open_24h'        => isset($_POST['open_24h']) ? 1 : 0,
        'description'     => trim($_POST['description'] ?? ''),
        'status'          => ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active',
    ];
    $removeIds = array_map('intval', $_POST['remove_media'] ?? []);
    $photosKept = count(array_filter($existingMedia, fn($m) => $m['type'] === 'photo' && !in_array((int)$m['id'], $removeIds, true)));
    $newPhotos = meuble_valid_uploads($_FILES['photos'] ?? [], ['jpg', 'jpeg', 'png', 'webp'], 8 * 1024 * 1024);

    if ($values['title'] === '' || $values['city'] === '' || $values['quartier'] === '' || !isset(TENEZER_MEUBLE_TYPES[$values['type']])) {
        $error = 'Renseignez le nom du logement, son type, la ville et le quartier.';
    } elseif ((float)$values['price'] <= 0) {
        $error = 'Indiquez un prix de location supérieur à 0.';
    } elseif ($photosKept + $newPhotos === 0) {
        $error = 'Ajoutez au moins une photo du logement (JPG, PNG ou WEBP, 8 Mo au plus).';
    } else {
        $fields = [$values['type'], $values['title'], $values['description'], $values['city'], $values['quartier'],
                   (float)$values['price'], $values['price_period'], $values['distance_route'] ?: null,
                   $values['has_guard'], $values['has_secure_gate'], $values['open_24h'], $values['status']];

        if ($property) {
            $stmt = $pdo->prepare("UPDATE properties SET type=?, title=?, description=?, city=?, quartier=?, price=?, price_period=?, distance_route=?, has_guard=?, has_secure_gate=?, open_24h=?, status=?
                                    WHERE id = ? AND category = 'meuble'");
            $stmt->execute(array_merge($fields, [$property['id']]));
            $propertyId = (int)$property['id'];
            tenezer_log($pdo, 'UPDATE', 'property', $propertyId, $adminId, 'Meublé : ' . $values['title']);
            if ($removeIds) tenezer_delete_media($pdo, $propertyId, $removeIds);
        } else {
            $stmt = $pdo->prepare("INSERT INTO properties (created_by_admin_id, category, type, title, description, city, quartier, price, price_period, distance_route, has_guard, has_secure_gate, open_24h, status)
                                    VALUES (?, 'meuble', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array_merge([$adminId], $fields));
            $propertyId = (int)$pdo->lastInsertId();
            tenezer_log($pdo, 'CREATE', 'property', $propertyId, $adminId, 'Meublé : ' . $values['title']);
        }

        $sent = meuble_sent_uploads($_FILES['photos'] ?? []) + meuble_sent_uploads($_FILES['videos'] ?? []);
        $saved = 0;
        if (!empty($_FILES['photos']['name'][0])) {
            $saved += tenezer_save_media($pdo, $_FILES['photos'], 'property', $propertyId, 'photo', 'assets/uploads/properties', true);
        }
        if (!empty($_FILES['videos']['name'][0])) {
            $saved += tenezer_save_media($pdo, $_FILES['videos'], 'property', $propertyId, 'video', 'assets/uploads/properties');
        }

        $query = ['saved' => $property ? 'updated' : 'created'];
        if ($sent > $saved) $query['skipped'] = $sent - $saved;
        header('Location: manage_meubles.php?' . http_build_query($query));
        exit();
    }
}

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';

$labelClass = 'block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2';
$inputClass = 'w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-gold';
?>

<header class="h-16 glass-effect border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 sticky top-0">
    <h2 class="text-xl font-semibold text-slate-800"><?php echo $property ? 'Modifier le logement meublé' : 'Publier un logement meublé'; ?></h2>
    <a href="manage_meubles.php" class="text-slate-400 hover:text-slate-700 text-sm"><i class="fas fa-arrow-left mr-1"></i> Retour</a>
</header>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm max-w-3xl" role="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-card border border-slate-100 p-6 sm:p-8 space-y-8 max-w-3xl">

        <fieldset class="space-y-4">
            <legend class="text-base font-semibold text-slate-800 mb-4">Le logement</legend>

            <div>
                <label for="title" class="<?php echo $labelClass; ?>">Nom du logement *</label>
                <input type="text" id="title" name="title" required maxlength="255" placeholder="Ex : Studio meublé Bonapriso" value="<?php echo htmlspecialchars($values['title'] ?? ''); ?>" class="<?php echo $inputClass; ?>">
            </div>

            <div>
                <span class="<?php echo $labelClass; ?>">Type de logement *</span>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <?php foreach (TENEZER_MEUBLE_TYPES as $slug => $label): ?>
                    <label>
                        <input type="radio" name="type" value="<?php echo $slug; ?>" class="peer sr-only" <?php echo ($values['type'] ?? '') === $slug ? 'checked' : ''; ?> required>
                        <span class="block cursor-pointer text-center px-3 py-3 rounded-xl border-2 border-slate-200 font-bold text-sm peer-checked:border-brand-gold peer-checked:bg-amber-50 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-gold"><?php echo $label; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="city" class="<?php echo $labelClass; ?>">Ville *</label>
                    <input type="text" id="city" name="city" required placeholder="Ex : Douala" value="<?php echo htmlspecialchars($values['city'] ?? ''); ?>" class="<?php echo $inputClass; ?>">
                </div>
                <div>
                    <label for="quartier" class="<?php echo $labelClass; ?>">Quartier *</label>
                    <input type="text" id="quartier" name="quartier" required placeholder="Ex : Bonapriso" value="<?php echo htmlspecialchars($values['quartier'] ?? ''); ?>" class="<?php echo $inputClass; ?>">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="price" class="<?php echo $labelClass; ?>">Prix de location (FCFA) *</label>
                    <input type="number" id="price" name="price" required min="1" step="1" inputmode="numeric" value="<?php echo htmlspecialchars(isset($values['price']) && $values['price'] !== '' ? (string)(int)$values['price'] : ''); ?>" class="<?php echo $inputClass; ?>">
                </div>
                <div>
                    <label for="price_period" class="<?php echo $labelClass; ?>">Le prix s'entend</label>
                    <select id="price_period" name="price_period" class="<?php echo $inputClass; ?>">
                        <?php foreach (TENEZER_PRICE_PERIODS as $slug => $label): ?>
                            <option value="<?php echo $slug; ?>" <?php echo ($values['price_period'] ?? 'jour') === $slug ? 'selected' : ''; ?>><?php echo ucfirst($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="description" class="<?php echo $labelClass; ?>">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Équipements, étage, règles de la maison…" class="<?php echo $inputClass; ?>"><?php echo htmlspecialchars($values['description'] ?? ''); ?></textarea>
            </div>
        </fieldset>

        <div class="border-t border-slate-100 pt-8">
            <fieldset class="space-y-4">
                <legend class="text-base font-semibold text-slate-800 mb-4">Sécurité et accès</legend>

                <div>
                    <label for="distance_route" class="<?php echo $labelClass; ?>">Distance par rapport à la route</label>
                    <input type="text" id="distance_route" name="distance_route" placeholder="Ex : 50 m du goudron" value="<?php echo htmlspecialchars($values['distance_route'] ?? ''); ?>" class="<?php echo $inputClass; ?>">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <?php foreach (['has_guard' => 'Gardien sur place', 'has_secure_gate' => 'Barrière sécurisée', 'open_24h' => 'Disponible 24h/24'] as $field => $label): ?>
                    <label class="flex items-center gap-3 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 cursor-pointer has-[:checked]:border-brand-gold has-[:checked]:bg-amber-50">
                        <input type="checkbox" name="<?php echo $field; ?>" value="1" class="w-4 h-4 accent-[#F1A80A]" <?php echo !empty($values[$field]) ? 'checked' : ''; ?>>
                        <?php echo $label; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        </div>

        <div class="border-t border-slate-100 pt-8">
            <fieldset class="space-y-4">
                <legend class="text-base font-semibold text-slate-800 mb-4">Photos et vidéos</legend>

                <?php if (!empty($existingMedia)): ?>
                <div>
                    <span class="<?php echo $labelClass; ?>">Médias en ligne</span>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                        <?php foreach ($existingMedia as $m): ?>
                        <label class="block cursor-pointer group">
                            <div class="aspect-square rounded-xl overflow-hidden bg-slate-100 border-2 <?php echo $m['is_main'] ? 'border-brand-gold' : 'border-transparent'; ?> group-has-[:checked]:opacity-40">
                                <?php if ($m['type'] === 'photo'): ?>
                                    <img src="../assets/uploads/properties/<?php echo htmlspecialchars($m['url']); ?>" alt="" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-video"></i></div>
                                <?php endif; ?>
                            </div>
                            <span class="flex items-center gap-2 mt-2 text-xs text-slate-500">
                                <input type="checkbox" name="remove_media[]" value="<?php echo (int)$m['id']; ?>" class="accent-red-500"> Retirer
                            </span>
                            <?php if ($m['is_main']): ?><span class="block text-xs text-slate-400">Photo principale</span><?php endif; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div>
                    <label for="photos" class="<?php echo $labelClass; ?>">Ajouter des photos<?php echo $property ? '' : ' *'; ?></label>
                    <input type="file" id="photos" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple <?php echo empty($existingMedia) ? 'required' : ''; ?> class="<?php echo $inputClass; ?>">
                    <p class="text-xs text-slate-400 mt-2">JPG, PNG ou WEBP, 8 Mo au plus par photo. La première photo devient la photo principale.</p>
                </div>
                <div>
                    <label for="videos" class="<?php echo $labelClass; ?>">Ajouter des vidéos</label>
                    <input type="file" id="videos" name="videos[]" accept="video/mp4,video/quicktime,video/webm,video/x-msvideo" multiple class="<?php echo $inputClass; ?>">
                    <p class="text-xs text-slate-400 mt-2">MP4, MOV, WEBM ou AVI, 50 Mo au plus par vidéo.</p>
                </div>
            </fieldset>
        </div>

        <div class="border-t border-slate-100 pt-8 space-y-4">
            <div>
                <label for="status" class="<?php echo $labelClass; ?>">Visibilité</label>
                <select id="status" name="status" class="<?php echo $inputClass; ?>">
                    <option value="active" <?php echo ($values['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Publié sur la page Logements meublés</option>
                    <option value="inactive" <?php echo ($values['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Retiré (non visible par les clients)</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-brand-gold hover:text-slate-900 transition-colors">
                <?php echo $property ? 'Enregistrer les modifications' : 'Publier le logement'; ?>
            </button>
        </div>
    </form>
</main>

<?php require_once 'includes_admin/footer_admin.php'; ?>
