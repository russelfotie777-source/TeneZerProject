<?php
require_once 'mfbase/database.php';
require_once 'includes/functions.php';

$city = trim($_GET['city'] ?? '');
$type = isset(TENEZER_MEUBLE_TYPES[$_GET['type'] ?? '']) ? $_GET['type'] : '';
$budgetMax = isset($_GET['budget_max']) && $_GET['budget_max'] !== '' ? (float)$_GET['budget_max'] : null;
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$perPage = 9;
$isFiltered = $city !== '' || $type !== '' || $budgetMax !== null;

$where = "WHERE p.status = 'active' AND p.category = 'meuble'";
$params = [];
if ($city !== '') {
    $where .= " AND p.city LIKE ?";
    $params[] = '%' . $city . '%';
}
if ($type !== '') {
    $where .= " AND p.type = ?";
    $params[] = $type;
}
if ($budgetMax !== null) {
    $where .= " AND p.price <= ?";
    $params[] = $budgetMax;
}

// Tolère l'absence des colonnes meublé tant que la migration n'est pas passée : la page affiche alors l'état vide
$meubles = [];
$totalCount = 0;
$totalPages = 1;
try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM properties p $where");
    $countStmt->execute($params);
    $totalCount = (int)$countStmt->fetchColumn();

    $totalPages = max(1, (int)ceil($totalCount / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;

    $stmt = $pdo->prepare("SELECT p.*,
                                  (SELECT COUNT(*) FROM media m WHERE m.entity_type = 'property' AND m.entity_id = p.id AND m.type = 'video') AS nb_videos
                           FROM properties p $where
                           ORDER BY p.created_at DESC
                           LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $meubles = $stmt->fetchAll();
} catch (PDOException $e) {
    $meubles = [];
    $totalCount = 0;
}

$paginationParams = $_GET;
unset($paginationParams['page']);
$paginationQuery = http_build_query($paginationParams);

$page_title = 'Logements meublés | Tenezer Immobilier';
$og_description = 'Chambres, studios et appartements meublés au Cameroun : photos, vidéos, prix et sécurité de chaque logement, réservation en ligne avec Tenezer.';

require_once 'includes/header.php';
require_once 'includes/navbar.php';

$labelClass = 'block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2';
$inputClass = 'w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold';
$pillClass = 'block cursor-pointer text-center px-3 py-3 rounded-xl border-2 border-gray-200 text-sm font-bold text-tenezer-dark transition-colors peer-checked:border-tenezer-gold peer-checked:bg-tenezer-gold/10 peer-focus-visible:ring-2 peer-focus-visible:ring-tenezer-gold';
?>

<main class="bg-slate-50 min-h-screen pb-20">

    <section class="bg-tenezer-dark py-16">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <span class="text-tenezer-gold text-[10px] font-black uppercase tracking-[0.4em]">Module 3</span>
            <h1 class="font-serif text-3xl md:text-5xl font-black text-white mt-3">Logements Meublés</h1>
            <p class="text-gray-400 text-sm mt-4 max-w-xl mx-auto">Chambres, studios et appartements meublés publiés par Tenezer. Choisissez un logement, ou décrivez votre besoin et nous vous trouvons une adresse.</p>
            <a href="#demande" class="inline-block mt-8 px-8 py-3 bg-tenezer-gold text-tenezer-dark rounded-full font-black text-xs uppercase tracking-widest hover:brightness-110 transition-all">Faire une demande</a>
        </div>
    </section>

    <!-- RECHERCHE -->
    <section class="max-w-5xl mx-auto px-6 -mt-8">
        <form method="GET" class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label for="f-city" class="<?php echo $labelClass; ?>">Ville</label>
                <input type="text" id="f-city" name="city" value="<?php echo htmlspecialchars($city); ?>" placeholder="Ex : Douala" class="<?php echo $inputClass; ?>">
            </div>
            <div>
                <label for="f-type" class="<?php echo $labelClass; ?>">Type</label>
                <select id="f-type" name="type" class="<?php echo $inputClass; ?>">
                    <option value="">Tous</option>
                    <?php foreach (TENEZER_MEUBLE_TYPES as $slug => $label): ?>
                        <option value="<?php echo $slug; ?>" <?php echo $type === $slug ? 'selected' : ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="f-budget" class="<?php echo $labelClass; ?>">Prix max (FCFA)</label>
                <input type="number" id="f-budget" name="budget_max" min="0" inputmode="numeric" value="<?php echo $budgetMax !== null ? (int)$budgetMax : ''; ?>" placeholder="Ex : 30000" class="<?php echo $inputClass; ?>">
            </div>
            <button type="submit" class="w-full bg-tenezer-dark text-white rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                <i class="fas fa-search mr-2"></i>Filtrer
            </button>
        </form>
    </section>

    <!-- ANNONCES -->
    <section class="max-w-7xl mx-auto px-6 mt-14" aria-labelledby="annonces-titre">
        <h2 id="annonces-titre" class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-6">
            <?php echo $totalCount > 0 ? $totalCount . ' logement' . ($totalCount > 1 ? 's' : '') . ' disponible' . ($totalCount > 1 ? 's' : '') : 'Logements disponibles'; ?>
        </h2>

        <?php if (empty($meubles)): ?>
            <div class="text-center py-16 bg-white rounded-3xl border border-gray-100">
                <i class="fas fa-couch text-4xl text-gray-300 mb-4"></i>
                <?php if ($isFiltered): ?>
                    <p class="font-bold text-tenezer-dark">Aucun résultat</p>
                    <p class="text-sm text-gray-500 mt-2">Modifiez votre recherche et réessayez, ou décrivez votre besoin ci-dessous.</p>
                    <a href="meubles.php" class="inline-block mt-6 text-tenezer-gold font-black text-xs uppercase tracking-widest">Voir tous les logements</a>
                <?php else: ?>
                    <p class="font-bold text-tenezer-dark">Aucun logement publié pour le moment</p>
                    <p class="text-sm text-gray-500 mt-2">Décrivez votre besoin ci-dessous : un conseiller Tenezer vous propose un logement.</p>
                    <a href="#demande" class="inline-block mt-6 text-tenezer-gold font-black text-xs uppercase tracking-widest">Faire une demande</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($meubles as $m): $photo = tenezer_main_photo($pdo, $m['id']); ?>
            <a href="detail.php?id=<?php echo (int)$m['id']; ?>" class="group rounded-3xl overflow-hidden bg-white border border-gray-100 shadow-sm hover:shadow-2xl transition-all flex flex-col">
                <div class="relative h-52 overflow-hidden bg-gray-100 flex items-center justify-center">
                    <?php if ($photo): ?>
                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($m['title']); ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                        <i class="fas fa-image text-4xl text-gray-300"></i>
                    <?php endif; ?>
                    <?php if ((int)$m['nb_videos'] > 0): ?>
                        <span class="absolute left-4 bottom-4 bg-tenezer-dark/80 text-white text-xs font-bold rounded-full px-3 py-1"><i class="fas fa-video mr-1 text-tenezer-gold"></i>Vidéo</span>
                    <?php endif; ?>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <span class="text-[9px] font-black uppercase tracking-widest text-tenezer-gold"><?php echo htmlspecialchars(tenezer_meuble_label($m['type'])); ?></span>
                    <h3 class="font-bold text-slate-900 mt-2 mb-1 line-clamp-1"><?php echo htmlspecialchars($m['title']); ?></h3>
                    <p class="text-xs text-gray-500 mb-4"><i class="fas fa-location-dot mr-1"></i><?php echo htmlspecialchars(($m['quartier'] ? $m['quartier'] . ', ' : '') . $m['city']); ?></p>
                    <ul class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-gray-600 mb-5">
                        <?php if ($m['has_guard']): ?><li><i class="fas fa-user-shield mr-1 text-tenezer-gold"></i>Gardien</li><?php endif; ?>
                        <?php if ($m['has_secure_gate']): ?><li><i class="fas fa-shield-halved mr-1 text-tenezer-gold"></i>Barrière sécurisée</li><?php endif; ?>
                        <?php if ($m['open_24h']): ?><li><i class="fas fa-clock mr-1 text-tenezer-gold"></i>24h/24</li><?php endif; ?>
                        <?php if ($m['distance_route']): ?><li><i class="fas fa-road mr-1 text-tenezer-gold"></i><?php echo htmlspecialchars($m['distance_route']); ?></li><?php endif; ?>
                    </ul>
                    <p class="font-black text-tenezer-dark mt-auto"><?php echo htmlspecialchars(tenezer_price_label($m)); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav class="flex justify-center items-center gap-2 mt-14" aria-label="Pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?php echo $paginationQuery ? $paginationQuery . '&' : ''; ?>page=<?php echo $i; ?>" <?php echo $i === $page ? 'aria-current="page"' : ''; ?>
                   class="w-10 h-10 flex items-center justify-center rounded-full text-xs font-black <?php echo $i === $page ? 'bg-tenezer-dark text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-tenezer-gold'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </section>

    <!-- DEMANDE DE RÉSERVATION -->
    <section id="demande" class="max-w-3xl mx-auto px-6 mt-20 scroll-mt-24" aria-labelledby="demande-titre">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <h2 id="demande-titre" class="font-serif text-2xl md:text-3xl font-black text-tenezer-dark">Demande de réservation</h2>
            <p class="text-sm text-gray-500 mt-2 mb-6">Indiquez ce que vous cherchez : un conseiller Tenezer vous rappelle avec les logements disponibles.</p>

            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                <i class="fas fa-circle-info mr-1"></i> Inscription : <strong>2 000 FCFA</strong> · Commission propriétaire : <strong>10 %</strong> après satisfaction.
            </div>

            <form class="tenezer-request-form space-y-5">
                <input type="hidden" name="module" value="meuble">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="d-nom" class="<?php echo $labelClass; ?>">Nom et prénom *</label>
                        <input type="text" id="d-nom" name="first_name" required autocomplete="name" class="<?php echo $inputClass; ?>">
                    </div>
                    <div>
                        <label for="d-tel" class="<?php echo $labelClass; ?>">Téléphone *</label>
                        <input type="tel" id="d-tel" name="phone" required autocomplete="tel" placeholder="Ex : 6 77 12 34 56" class="<?php echo $inputClass; ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="d-ville" class="<?php echo $labelClass; ?>">Ville souhaitée *</label>
                        <input type="text" id="d-ville" name="city" required placeholder="Ex : Douala" class="<?php echo $inputClass; ?>">
                    </div>
                    <div>
                        <label for="d-budget" class="<?php echo $labelClass; ?>">Budget (FCFA) *</label>
                        <input type="number" id="d-budget" name="extra[budget]" required min="1" inputmode="numeric" placeholder="Ex : 25000" class="<?php echo $inputClass; ?>">
                    </div>
                </div>

                <fieldset>
                    <legend class="<?php echo $labelClass; ?>">Type de bien recherché *</legend>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <?php foreach (TENEZER_MEUBLE_TYPES as $label): ?>
                        <label>
                            <input type="radio" name="extra[type_logement]" value="<?php echo $label; ?>" required class="peer sr-only">
                            <span class="<?php echo $pillClass; ?>"><?php echo $label; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="<?php echo $labelClass; ?>">Durée du séjour *</legend>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <?php foreach (TENEZER_MEUBLE_DUREES as $duree): ?>
                        <label>
                            <input type="radio" name="extra[duree_sejour]" value="<?php echo $duree; ?>" required class="peer sr-only">
                            <span class="<?php echo $pillClass; ?>"><?php echo $duree; ?></span>
                        </label>
                        <?php endforeach; ?>
                        <label>
                            <input type="radio" name="extra[duree_sejour]" value="Autre durée" required data-reveal="d-duree-autre" class="peer sr-only">
                            <span class="<?php echo $pillClass; ?>">Autre durée</span>
                        </label>
                    </div>
                    <div id="d-duree-autre" class="mt-3" hidden>
                        <label for="d-duree-precisee" class="sr-only">Précisez la durée du séjour</label>
                        <input type="text" id="d-duree-precisee" name="extra[duree_precisee]" placeholder="Précisez la durée, ex : 2 semaines" class="<?php echo $inputClass; ?>">
                    </div>
                </fieldset>

                <div>
                    <label for="d-com" class="<?php echo $labelClass; ?>">Commentaire</label>
                    <textarea id="d-com" name="extra[commentaire]" rows="3" placeholder="Quartier préféré, date d'arrivée…" class="<?php echo $inputClass; ?>"></textarea>
                </div>

                <p class="form-msg hidden text-xs text-red-600" role="alert"></p>

                <button type="submit" class="w-full bg-tenezer-dark text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                    Envoyer ma demande
                </button>
            </form>
        </div>
    </section>

</main>

<script src="assets/js/request-form.js"></script>
<?php require_once 'includes/footer.php'; ?>
