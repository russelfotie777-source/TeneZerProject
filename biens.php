<?php
require_once 'mfbase/database.php';
require_once 'includes/functions.php';

$category = isset($_GET['category']) && $_GET['category'] === 'terrain' ? 'terrain' : 'logement';
$city = trim($_GET['city'] ?? '');
$quartier = trim($_GET['quartier'] ?? '');
$type = trim($_GET['type'] ?? '');
$bedrooms = isset($_GET['bedrooms']) && $_GET['bedrooms'] !== '' ? (int)$_GET['bedrooms'] : null;
$budgetMax = isset($_GET['budget_max']) && $_GET['budget_max'] !== '' ? (float)$_GET['budget_max'] : null;
$sort = $_GET['sort'] ?? 'recent';
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$perPage = 9;

$sql = "SELECT * FROM properties WHERE status = 'active' AND category = ?";
$params = [$category];

if ($city !== '') {
    $sql .= " AND city LIKE ?";
    $params[] = '%' . $city . '%';
}
if ($quartier !== '') {
    $sql .= " AND quartier LIKE ?";
    $params[] = '%' . $quartier . '%';
}
if ($type !== '' && $category === 'logement') {
    $sql .= " AND type = ?";
    $params[] = $type;
}
if ($bedrooms !== null && $category === 'logement') {
    $sql .= " AND bedrooms >= ?";
    $params[] = $bedrooms;
}
if ($budgetMax !== null) {
    $sql .= " AND price <= ?";
    $params[] = $budgetMax;
}

switch ($sort) {
    case 'price_asc':  $sql .= " ORDER BY price ASC"; break;
    case 'price_desc': $sql .= " ORDER BY price DESC"; break;
    default:            $sql .= " ORDER BY created_at DESC"; break;
}

$properties = [];
$totalCount = 0;
try {
    $countStmt = $pdo->prepare(str_replace('SELECT *', 'SELECT COUNT(*) as total', $sql));
    $countStmt->execute($params);
    $totalCount = (int)$countStmt->fetchColumn();

    $totalPages = max(1, (int)ceil($totalCount / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;

    $sql .= " LIMIT $perPage OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll();
} catch (PDOException $e) {
    $properties = [];
    $totalPages = 1;
}

// Reconstruit la query string en excluant 'page', pour les liens de pagination
$paginationParams = $_GET;
unset($paginationParams['page']);
$paginationQuery = http_build_query($paginationParams);

$page_title = ($category === 'terrain' ? 'Terrains disponibles' : 'Logements à louer') . ' | Tenezer Immobilier';
$og_description = $category === 'terrain'
    ? 'Découvrez les terrains disponibles au Cameroun avec Tenezer.'
    : 'Découvrez les logements à louer (chambres, studios, appartements, bureaux, magasins) au Cameroun avec Tenezer.';

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="bg-slate-50 min-h-screen pb-20">

    <section class="bg-tenezer-dark py-16">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="font-serif text-3xl md:text-5xl font-black text-white mb-4">
                <?php echo $category === 'terrain' ? 'Terrains disponibles' : 'Logements à louer'; ?>
            </h1>
            <div class="flex justify-center gap-3 mt-6">
                <a href="biens.php?category=logement" class="px-6 py-2 rounded-full text-[11px] font-black uppercase tracking-widest transition-all <?php echo $category === 'logement' ? 'bg-tenezer-gold text-tenezer-dark' : 'border border-white/20 text-white hover:border-tenezer-gold'; ?>">Logements</a>
                <a href="biens.php?category=terrain" class="px-6 py-2 rounded-full text-[11px] font-black uppercase tracking-widest transition-all <?php echo $category === 'terrain' ? 'bg-tenezer-gold text-tenezer-dark' : 'border border-white/20 text-white hover:border-tenezer-gold'; ?>">Terrains</a>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 -mt-8">
        <form method="GET" class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Ville</label>
                <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>" placeholder="Ex: Douala" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Quartier</label>
                <input type="text" name="quartier" value="<?php echo htmlspecialchars($quartier); ?>" placeholder="Ex: Bonapriso" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
            </div>
            <?php if ($category === 'logement'): ?>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Type</label>
                <select name="type" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    <option value="">Tous</option>
                    <?php foreach (TENEZER_LOGEMENT_TYPES as $slug => $label): ?>
                        <option value="<?php echo $slug; ?>" <?php echo $type === $slug ? 'selected' : ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Chambres min.</label>
                <select name="bedrooms" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    <option value="">Indifférent</option>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $bedrooms === $i ? 'selected' : ''; ?>><?php echo $i; ?>+</option>
                    <?php endfor; ?>
                </select>
            </div>
            <?php endif; ?>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Budget max (FCFA)</label>
                <input type="number" name="budget_max" value="<?php echo htmlspecialchars($_GET['budget_max'] ?? ''); ?>" placeholder="Ex: 100000" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Trier par</label>
                <select name="sort" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    <option value="recent" <?php echo $sort === 'recent' ? 'selected' : ''; ?>>Plus récents</option>
                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Prix croissant</option>
                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Prix décroissant</option>
                </select>
            </div>
            <button type="submit" class="w-full sm:w-auto bg-tenezer-dark text-white rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                <i class="fas fa-search mr-2"></i>Filtrer
            </button>
        </form>
    </section>

    <section class="max-w-7xl mx-auto px-6 mt-14">
        <?php if (empty($properties)): ?>
            <div class="text-center py-24">
                <i class="fas fa-house-circle-xmark text-5xl text-gray-300 mb-6"></i>
                <p class="text-gray-500 font-medium">Aucun bien ne correspond à votre recherche pour le moment.</p>
                <a href="https://wa.me/237672163188" target="_blank" class="inline-block mt-6 text-tenezer-gold font-black text-xs uppercase tracking-widest"><i class="fab fa-whatsapp mr-1"></i> Contactez-nous, nous vous aiderons à trouver</a>
            </div>
        <?php else: ?>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-6"><?php echo $totalCount; ?> bien<?php echo $totalCount > 1 ? 's' : ''; ?> trouvé<?php echo $totalCount > 1 ? 's' : ''; ?></p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($properties as $p): $photo = tenezer_main_photo($pdo, $p['id']); ?>
            <a href="detail.php?id=<?php echo (int)$p['id']; ?>" class="group rounded-3xl overflow-hidden bg-white border border-gray-100 shadow-sm hover:shadow-2xl transition-all">
                <div class="h-52 overflow-hidden bg-gray-100 flex items-center justify-center">
                    <?php if ($photo): ?>
                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                        <i class="fas fa-image text-4xl text-gray-300"></i>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <span class="text-[9px] font-black uppercase tracking-widest text-tenezer-gold"><?php echo htmlspecialchars(tenezer_type_label($p['type'])); ?></span>
                    <h3 class="font-bold text-slate-900 mt-2 mb-1 line-clamp-1"><?php echo htmlspecialchars($p['title']); ?></h3>
                    <p class="text-xs text-gray-500 mb-4"><i class="fas fa-location-dot mr-1"></i><?php echo htmlspecialchars($p['quartier'] ? $p['quartier'] . ', ' . $p['city'] : $p['city']); ?></p>
                    <p class="font-black text-tenezer-dark"><?php echo number_format((float)$p['price'], 0, ',', ' '); ?> FCFA</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center items-center gap-2 mt-14">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?php echo $paginationQuery ? $paginationQuery . '&' : ''; ?>page=<?php echo $i; ?>"
                   class="w-10 h-10 flex items-center justify-center rounded-full text-xs font-black <?php echo $i === $page ? 'bg-tenezer-dark text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-tenezer-gold'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>
