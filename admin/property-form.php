<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';
require_once '../includes/functions.php';

$adminId = $_SESSION['admin_id'] ?? null;
$propertyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$property = null;

if ($propertyId) {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$propertyId]);
    $property = $stmt->fetch();
    if (!$property) { header('Location: manage_properties.php'); exit(); }
    if ($property['category'] === 'meuble') { header('Location: meuble-form.php?id=' . $propertyId); exit(); }
}

$owners = $pdo->query("SELECT id, agency_name FROM owners WHERE status = 'approved' ORDER BY agency_name")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] === 'terrain' ? 'terrain' : 'logement';
    $type = $category === 'terrain' ? 'terrain' : trim($_POST['type'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $quartier = trim($_POST['quartier'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $ownerId = !empty($_POST['owner_id']) ? (int)$_POST['owner_id'] : null;
    $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';

    if ($title === '' || $description === '' || $city === '' || $price <= 0 || ($category === 'logement' && $type === '')) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        $isNew = $_POST['is_new'] !== '' ? (int)$_POST['is_new'] : null;
        $distanceRoute = trim($_POST['distance_route'] ?? '') ?: null;
        $hasParking = isset($_POST['has_parking']) ? 1 : 0;
        $bedrooms = $category === 'logement' && $_POST['bedrooms'] !== '' ? (int)$_POST['bedrooms'] : null;
        $bathrooms = $category === 'logement' && $_POST['bathrooms'] !== '' ? (int)$_POST['bathrooms'] : null;
        $surfaceChambre = $category === 'logement' && $_POST['surface_chambre'] !== '' ? (int)$_POST['surface_chambre'] : null;
        $surfaceSalon = $category === 'logement' && $_POST['surface_salon'] !== '' ? (int)$_POST['surface_salon'] : null;
        $surfaceCuisine = $category === 'logement' && $_POST['surface_cuisine'] !== '' ? (int)$_POST['surface_cuisine'] : null;
        $superficieTerrain = $category === 'terrain' && $_POST['superficie_terrain'] !== '' ? (int)$_POST['superficie_terrain'] : null;
        $features = json_encode($_POST['features'] ?? [], JSON_UNESCAPED_UNICODE);

        if ($property) {
            $stmt = $pdo->prepare("UPDATE properties SET owner_id=?, category=?, type=?, title=?, description=?, city=?, quartier=?, price=?, is_new=?, distance_route=?, bedrooms=?, bathrooms=?, surface_chambre=?, surface_salon=?, surface_cuisine=?, superficie_terrain=?, has_parking=?, features=?, status=?
                                    WHERE id = ?");
            $stmt->execute([$ownerId, $category, $type, $title, $description, $city, $quartier ?: null, $price, $isNew, $distanceRoute, $bedrooms, $bathrooms, $surfaceChambre, $surfaceSalon, $surfaceCuisine, $superficieTerrain, $hasParking, $features, $status, $property['id']]);
            $propertyId = $property['id'];
            tenezer_log($pdo, 'UPDATE', 'property', $propertyId, $adminId, $title);
        } else {
            $stmt = $pdo->prepare("INSERT INTO properties (owner_id, created_by_admin_id, category, type, title, description, city, quartier, price, is_new, distance_route, bedrooms, bathrooms, surface_chambre, surface_salon, surface_cuisine, superficie_terrain, has_parking, features, status)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$ownerId, $adminId, $category, $type, $title, $description, $city, $quartier ?: null, $price, $isNew, $distanceRoute, $bedrooms, $bathrooms, $surfaceChambre, $surfaceSalon, $surfaceCuisine, $superficieTerrain, $hasParking, $features, $status]);
            $propertyId = (int)$pdo->lastInsertId();
            tenezer_log($pdo, 'CREATE', 'property', $propertyId, $adminId, $title);
        }

        if (!empty($_FILES['photos']['name'][0])) {
            tenezer_save_media($pdo, $_FILES['photos'], 'property', $propertyId, 'photo', 'assets/uploads/properties', true);
        }
        if (!empty($_FILES['videos']['name'][0])) {
            tenezer_save_media($pdo, $_FILES['videos'], 'property', $propertyId, 'video', 'assets/uploads/properties');
        }

        header('Location: manage_properties.php?saved=1');
        exit();
    }
}

$existingMedia = $property ? tenezer_get_media($pdo, $property['id']) : [];
$existingFeatures = $property ? (json_decode($property['features'] ?? '[]', true) ?: []) : [];

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';
?>

<header class="h-16 glass-effect border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 sticky top-0">
    <h2 class="text-xl font-semibold text-slate-800"><?php echo $property ? 'Modifier le bien' : 'Ajouter un bien'; ?></h2>
    <a href="manage_properties.php" class="text-slate-400 hover:text-slate-700 text-sm"><i class="fas fa-arrow-left mr-1"></i> Retour</a>
</header>

<main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-card border border-slate-100 p-8 space-y-6 max-w-3xl">

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Propriétaire</label>
            <select name="owner_id" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
                <option value="">— Publié par Tenezer (admin) —</option>
                <?php foreach ($owners as $o): ?>
                    <option value="<?php echo (int)$o['id']; ?>" <?php echo ($property && $property['owner_id'] == $o['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($o['agency_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Catégorie *</label>
            <div class="flex gap-3">
                <label class="flex-1">
                    <input type="radio" name="category" value="logement" class="peer hidden" onchange="toggleCategory()" <?php echo (!$property || $property['category'] === 'logement') ? 'checked' : ''; ?>>
                    <div class="cursor-pointer text-center px-4 py-3 rounded-xl border-2 border-slate-200 peer-checked:border-brand-gold peer-checked:bg-amber-50 font-bold text-sm">Logement</div>
                </label>
                <label class="flex-1">
                    <input type="radio" name="category" value="terrain" class="peer hidden" onchange="toggleCategory()" <?php echo ($property && $property['category'] === 'terrain') ? 'checked' : ''; ?>>
                    <div class="cursor-pointer text-center px-4 py-3 rounded-xl border-2 border-slate-200 peer-checked:border-brand-gold peer-checked:bg-amber-50 font-bold text-sm">Terrain</div>
                </label>
            </div>
        </div>

        <div id="type-field">
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Type de logement *</label>
            <select name="type" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
                <?php foreach (TENEZER_LOGEMENT_TYPES as $slug => $label): ?>
                    <option value="<?php echo $slug; ?>" <?php echo ($property && $property['type'] === $slug) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Titre *</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($property['title'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Description *</label>
            <textarea name="description" rows="4" required class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm"><?php echo htmlspecialchars($property['description'] ?? ''); ?></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Ville *</label>
                <input type="text" name="city" required value="<?php echo htmlspecialchars($property['city'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Quartier</label>
                <input type="text" name="quartier" value="<?php echo htmlspecialchars($property['quartier'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Prix (FCFA) *</label>
                <input type="number" name="price" required min="1" value="<?php echo htmlspecialchars($property['price'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Statut</label>
                <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
                    <option value="active" <?php echo (!$property || $property['status'] === 'active') ? 'selected' : ''; ?>>Publié</option>
                    <option value="inactive" <?php echo ($property && $property['status'] === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Distance de la route</label>
            <input type="text" name="distance_route" value="<?php echo htmlspecialchars($property['distance_route'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
        </div>

        <div id="logement-fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Chambres</label>
                <input type="number" name="bedrooms" min="0" value="<?php echo htmlspecialchars($property['bedrooms'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Douches</label>
                <input type="number" name="bathrooms" min="0" value="<?php echo htmlspecialchars($property['bathrooms'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Salon (m²)</label>
                <input type="number" name="surface_salon" min="0" value="<?php echo htmlspecialchars($property['surface_salon'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Cuisine (m²)</label>
                <input type="number" name="surface_cuisine" min="0" value="<?php echo htmlspecialchars($property['surface_cuisine'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
            </div>
            <div class="col-span-2">
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Chambre (m²)</label>
                <input type="number" name="surface_chambre" min="0" value="<?php echo htmlspecialchars($property['surface_chambre'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        <div id="terrain-fields" class="hidden">
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Superficie du terrain (m²)</label>
            <input type="number" name="superficie_terrain" min="0" value="<?php echo htmlspecialchars($property['superficie_terrain'] ?? ''); ?>" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
        </div>

        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 text-sm font-medium">
                <input type="checkbox" name="has_parking" value="1" <?php echo !empty($property['has_parking']) ? 'checked' : ''; ?>> Parking
            </label>
            <label class="flex items-center gap-2 text-sm font-medium">
                <input type="radio" name="is_new" value="1" <?php echo (isset($property['is_new']) && $property['is_new'] == 1) ? 'checked' : ''; ?>> Neuf
            </label>
            <label class="flex items-center gap-2 text-sm font-medium">
                <input type="radio" name="is_new" value="0" <?php echo (isset($property['is_new']) && $property['is_new'] == 0) ? 'checked' : ''; ?>> Ancien
            </label>
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Équipements</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <?php foreach (TENEZER_FEATURES as $slug => $meta): ?>
                <label class="flex items-center gap-2 text-xs font-medium">
                    <input type="checkbox" name="features[]" value="<?php echo $slug; ?>" <?php echo in_array($slug, $existingFeatures, true) ? 'checked' : ''; ?>>
                    <?php echo $meta['label']; ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($existingMedia)): ?>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Médias actuels</label>
            <div class="flex flex-wrap gap-3">
                <?php foreach ($existingMedia as $m): ?>
                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-50 border <?php echo $m['is_main'] ? 'border-brand-gold' : 'border-slate-100'; ?>">
                        <?php if ($m['type'] === 'photo'): ?>
                            <img src="../assets/uploads/properties/<?php echo htmlspecialchars($m['url']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-video"></i></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Ajouter des photos</label>
            <input type="file" name="photos[]" accept="image/*" multiple class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Ajouter des vidéos</label>
            <input type="file" name="videos[]" accept="video/*" multiple class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm">
        </div>

        <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-brand-gold hover:text-slate-900 transition-all">
            <?php echo $property ? 'Enregistrer' : 'Publier le bien'; ?>
        </button>
    </form>
</main>

<script>
function toggleCategory() {
    const isTerrain = document.querySelector('input[name="category"]:checked').value === 'terrain';
    document.getElementById('type-field').classList.toggle('hidden', isTerrain);
    document.getElementById('logement-fields').classList.toggle('hidden', isTerrain);
    document.getElementById('terrain-fields').classList.toggle('hidden', !isTerrain);
}
document.addEventListener('DOMContentLoaded', toggleCategory);
</script>

<?php require_once 'includes_admin/footer_admin.php'; ?>
