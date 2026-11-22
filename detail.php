<?php
require_once 'mfbase/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND status = 'active'");
$stmt->execute([$id]);
$property = $stmt->fetch();

if (!$property) {
    header('Location: biens.php');
    exit;
}

$media = tenezer_get_media($pdo, $property['id']);
$photos = array_values(array_filter($media, fn($m) => $m['type'] === 'photo'));
$videos = array_values(array_filter($media, fn($m) => $m['type'] === 'video'));

$features = json_decode($property['features'] ?? '[]', true) ?: [];
$isMeuble = $property['category'] === 'meuble';

$whatsapp_text = "Bonjour Tenezer,\n\n";
$whatsapp_text .= $isMeuble ? "Je souhaite réserver ce logement meublé :\n" : "Je suis intéressé(e) par ce bien :\n";
$whatsapp_text .= "🏠 " . $property['title'] . "\n";
$whatsapp_text .= "🆔 Réf : #TNZ-" . $property['id'] . "\n";
$whatsapp_text .= "📍 " . ($property['quartier'] ? $property['quartier'] . ', ' : '') . $property['city'] . "\n";
$whatsapp_text .= "💰 " . tenezer_price_label($property);

$page_title = $property['title'] . ' | Tenezer Immobilier';
$og_description = tenezer_type_label($property['type']) . ' à ' . ($property['quartier'] ? $property['quartier'] . ', ' : '') . $property['city'] . ' — ' . tenezer_price_label($property);
$mainPhoto = tenezer_main_photo($pdo, $property['id']);
if ($mainPhoto) {
    $og_image = tenezer_absolute_url($mainPhoto);
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="bg-white min-h-screen pb-24 lg:pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-8">

        <div class="mb-6">
            <h1 class="font-sans text-2xl sm:text-3xl md:text-5xl font-black text-slate-900 uppercase tracking-tight leading-tight break-words">
                <?php echo htmlspecialchars($property['title']); ?>
            </h1>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-3 pb-4 border-b border-gray-100">
                <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                    <span class="bg-tenezer-gold/10 text-[#b8860b] text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
                        <?php echo htmlspecialchars($isMeuble ? tenezer_meuble_label($property['type']) : tenezer_type_label($property['type'])); ?>
                    </span>
                    <span class="text-gray-400 text-[11px] sm:text-xs italic">
                        <i class="fas fa-map-marker-alt mr-1 text-tenezer-gold"></i>
                        <?php echo htmlspecialchars(($property['quartier'] ? $property['quartier'] . ', ' : '') . $property['city']); ?>
                    </span>
                </div>
                <a href="<?php echo $isMeuble ? 'meubles.php' : 'biens.php?category=' . htmlspecialchars($property['category']); ?>" class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] hover:text-tenezer-gold transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12">

            <div class="lg:col-span-8 flex flex-col">

                <div class="relative rounded-2xl sm:rounded-[2.5rem] overflow-hidden shadow-md mb-4 bg-slate-50 border border-slate-100">
                    <?php if (!empty($photos)): ?>
                        <img id="mainView" src="assets/uploads/properties/<?php echo htmlspecialchars($photos[0]['url']); ?>" class="w-full h-64 sm:h-[400px] md:h-[500px] object-cover">
                    <?php else: ?>
                        <div class="w-full h-64 sm:h-[400px] flex items-center justify-center text-gray-300"><i class="fas fa-image text-5xl"></i></div>
                    <?php endif; ?>
                </div>

                <?php if (count($photos) > 1): ?>
                <div class="flex gap-3 overflow-x-auto no-scrollbar mb-8 pb-1">
                    <?php foreach ($photos as $photo): ?>
                        <img src="assets/uploads/properties/<?php echo htmlspecialchars($photo['url']); ?>"
                             loading="lazy"
                             class="w-20 h-16 sm:w-28 sm:h-20 object-cover rounded-xl shadow-sm border-2 border-transparent hover:border-tenezer-gold transition-all cursor-pointer flex-shrink-0"
                             onclick="document.getElementById('mainView').src = this.src">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($videos)): ?>
                <div class="mb-8">
                    <h3 class="font-sans text-lg font-black text-slate-900 mb-4 uppercase tracking-tight">Vidéos</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($videos as $video): ?>
                            <video controls class="w-full rounded-2xl border border-slate-100">
                                <source src="assets/uploads/properties/<?php echo htmlspecialchars($video['url']); ?>">
                            </video>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (trim($property['description']) !== ''): ?>
                <div class="prose prose-slate max-w-none mb-10">
                    <h3 class="font-sans text-xl sm:text-2xl font-black text-slate-900 mb-4 uppercase tracking-tight">Description</h3>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base font-light">
                        <?php echo nl2br(htmlspecialchars($property['description'])); ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php if ($isMeuble): ?>
                <div class="py-8 border-t border-gray-100">
                    <h3 class="font-sans text-xl sm:text-2xl font-black text-slate-900 mb-6 uppercase tracking-tight">Sécurité et accès</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ([
                            ['fa-user-shield', 'Gardien sur place', $property['has_guard'] ? 'Oui' : 'Non', (bool)$property['has_guard']],
                            ['fa-shield-halved', 'Barrière sécurisée', $property['has_secure_gate'] ? 'Oui' : 'Non', (bool)$property['has_secure_gate']],
                            ['fa-clock', 'Disponible 24h/24', $property['open_24h'] ? 'Oui' : 'Non', (bool)$property['open_24h']],
                            ['fa-road', 'Distance de la route', $property['distance_route'] ?: 'Non précisée', (bool)$property['distance_route']],
                        ] as [$icon, $label, $value, $yes]): ?>
                        <div class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center flex-shrink-0 <?php echo $yes ? 'text-tenezer-gold' : 'text-gray-300'; ?>">
                                <i class="fas <?php echo $icon; ?> text-sm"></i>
                            </div>
                            <div>
                                <dt class="text-[9px] font-black text-gray-400 uppercase tracking-widest"><?php echo $label; ?></dt>
                                <dd class="font-bold <?php echo $yes ? 'text-slate-900' : 'text-gray-400'; ?>"><?php echo htmlspecialchars($value); ?></dd>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </dl>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-8 border-t border-gray-100">
                    <?php if ($property['category'] === 'logement'): ?>
                        <?php if ($property['bedrooms'] !== null): ?>
                        <div><span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Chambres</span><span class="font-bold text-slate-900"><?php echo (int)$property['bedrooms']; ?></span></div>
                        <?php endif; ?>
                        <?php if ($property['bathrooms'] !== null): ?>
                        <div><span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Douches</span><span class="font-bold text-slate-900"><?php echo (int)$property['bathrooms']; ?></span></div>
                        <?php endif; ?>
                        <?php if ($property['surface_salon']): ?>
                        <div><span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Salon</span><span class="font-bold text-slate-900"><?php echo (int)$property['surface_salon']; ?> m²</span></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($property['superficie_terrain']): ?>
                        <div><span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Superficie</span><span class="font-bold text-slate-900"><?php echo (int)$property['superficie_terrain']; ?> m²</span></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($property['has_parking']): ?>
                    <div><span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Parking</span><span class="font-bold text-slate-900"><i class="fas fa-check text-green-600"></i></span></div>
                    <?php endif; ?>
                    <?php if ($property['distance_route']): ?>
                    <div><span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Distance route</span><span class="font-bold text-slate-900"><?php echo htmlspecialchars($property['distance_route']); ?></span></div>
                    <?php endif; ?>
                    <?php if ($property['is_new'] !== null): ?>
                    <div><span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">État</span><span class="font-bold text-slate-900"><?php echo $property['is_new'] ? 'Neuf' : 'Ancien'; ?></span></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($features)): ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 py-8 border-t border-gray-100">
                    <?php foreach ($features as $f): $meta = TENEZER_FEATURES[$f] ?? null; ?>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-tenezer-gold shadow-sm flex-shrink-0">
                            <i class="fas <?php echo $meta['icon'] ?? 'fa-check'; ?> text-sm"></i>
                        </div>
                        <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest"><?php echo htmlspecialchars($meta['label'] ?? $f); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-6">
                    <div class="bg-tenezer-dark rounded-2xl sm:rounded-[2.5rem] p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
                        <p class="text-tenezer-gold font-black text-[9px] uppercase tracking-[0.3em] mb-4">Tenezer Immobilier</p>
                        <div class="mb-6 pb-4 border-b border-white/10">
                            <span class="block text-[8px] text-gray-500 uppercase font-black mb-1">Prix</span>
                            <span class="text-3xl sm:text-4xl font-black tracking-tight">
                                <?php echo number_format((float)$property['price'], 0, ',', ' '); ?>
                                <small class="text-xs font-bold opacity-40 uppercase">FCFA</small>
                                <?php if (isset(TENEZER_PRICE_PERIODS[$property['price_period'] ?? ''])): ?>
                                    <small class="text-xs font-bold opacity-40"><?php echo TENEZER_PRICE_PERIODS[$property['price_period']]; ?></small>
                                <?php endif; ?>
                            </span>
                        </div>

                        <a href="<?php echo htmlspecialchars(tenezer_whatsapp_url('237672163188', $whatsapp_text)); ?>" target="_blank"
                           class="whatsapp-track-btn block w-full bg-tenezer-gold text-tenezer-dark py-4 rounded-2xl text-center font-black text-xs uppercase tracking-[0.2em] hover:brightness-110 transition-all shadow-lg mb-3"
                           data-entity-id="<?php echo (int)$property['id']; ?>">
                            <i class="fab fa-whatsapp mr-2"></i>Contacter sur WhatsApp
                        </a>
                        <button type="button" onclick="document.getElementById('visit-form-wrap').scrollIntoView({behavior:'smooth'})"
                                class="block w-full border border-white/20 text-white py-4 rounded-2xl text-center font-black text-xs uppercase tracking-[0.2em] hover:border-tenezer-gold hover:text-tenezer-gold transition-all">
                            <?php echo $isMeuble ? 'Réserver ce logement' : 'Demander une visite'; ?>
                        </button>
                    </div>

                    <?php if ($isMeuble): ?>
                    <div id="visit-form-wrap" class="bg-slate-50 rounded-[2rem] p-6 sm:p-8 border border-gray-100 scroll-mt-24">
                        <h4 class="font-black text-tenezer-dark text-sm uppercase tracking-wide mb-4">Demande de réservation</h4>
                        <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                            <i class="fas fa-circle-info mr-1"></i> Inscription : <strong>2 000 FCFA</strong> · Commission propriétaire : <strong>10 %</strong> après satisfaction.
                        </div>
                        <form class="tenezer-request-form space-y-3">
                            <input type="hidden" name="module" value="meuble">
                            <input type="hidden" name="property_id" value="<?php echo (int)$property['id']; ?>">
                            <input type="hidden" name="city" value="<?php echo htmlspecialchars($property['city']); ?>">
                            <input type="hidden" name="extra[type_logement]" value="<?php echo htmlspecialchars(TENEZER_MEUBLE_TYPES[$property['type']] ?? tenezer_type_label($property['type'])); ?>">
                            <input type="hidden" name="extra[quartier]" value="<?php echo htmlspecialchars($property['quartier'] ?? ''); ?>">
                            <input type="hidden" name="extra[prix_annonce]" value="<?php echo htmlspecialchars(tenezer_price_label($property)); ?>">
                            <label for="r-nom" class="sr-only">Nom et prénom</label>
                            <input type="text" id="r-nom" name="first_name" required autocomplete="name" placeholder="Nom et prénom" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <label for="r-tel" class="sr-only">Numéro de téléphone</label>
                            <input type="tel" id="r-tel" name="phone" required autocomplete="tel" placeholder="Numéro de téléphone" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <fieldset class="pt-1">
                                <legend class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Durée du séjour *</legend>
                                <div class="grid grid-cols-2 gap-2">
                                    <?php foreach (array_merge(TENEZER_MEUBLE_DUREES, ['Autre durée']) as $duree): ?>
                                    <label>
                                        <input type="radio" name="extra[duree_sejour]" value="<?php echo $duree; ?>" required class="peer sr-only" <?php echo $duree === 'Autre durée' ? 'data-reveal="r-duree-autre"' : ''; ?>>
                                        <span class="block cursor-pointer text-center px-3 py-3 rounded-xl border-2 border-gray-200 bg-white text-sm font-bold text-tenezer-dark transition-colors peer-checked:border-tenezer-gold peer-checked:bg-tenezer-gold/10 peer-focus-visible:ring-2 peer-focus-visible:ring-tenezer-gold"><?php echo $duree; ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                                <div id="r-duree-autre" class="mt-2" hidden>
                                    <label for="r-duree-precisee" class="sr-only">Précisez la durée du séjour</label>
                                    <input type="text" id="r-duree-precisee" name="extra[duree_precisee]" placeholder="Précisez la durée, ex : 2 semaines" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                                </div>
                            </fieldset>
                            <label for="r-com" class="sr-only">Commentaire</label>
                            <textarea id="r-com" name="extra[commentaire]" rows="3" placeholder="Date d'arrivée, nombre de personnes… (optionnel)" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold"></textarea>
                            <p class="form-msg hidden text-xs text-red-600" role="alert"></p>
                            <button type="submit" class="w-full bg-tenezer-dark text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                                Envoyer ma demande
                            </button>
                        </form>
                    </div>
                    <?php else: ?>
                    <div id="visit-form-wrap" class="bg-slate-50 rounded-[2rem] p-6 sm:p-8 border border-gray-100">
                        <h4 class="font-black text-tenezer-dark text-sm uppercase tracking-wide mb-4">Demande de visite / contact</h4>
                        <?php if ($property['category'] === 'logement'): ?>
                        <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                            <i class="fas fa-circle-info mr-1"></i> Frais d'inscription : <strong>5 000 FCFA</strong> · Commission : <strong>1 mois de loyer</strong> après satisfaction.
                        </div>
                        <?php endif; ?>
                        <form id="visit-form" class="space-y-3">
                            <input type="hidden" name="module" value="location">
                            <input type="hidden" name="property_id" value="<?php echo (int)$property['id']; ?>">
                            <input type="hidden" name="city" value="<?php echo htmlspecialchars($property['city']); ?>">
                            <input type="hidden" name="extra[type_bien]" value="<?php echo htmlspecialchars(tenezer_type_label($property['type'])); ?>">
                            <input type="hidden" name="extra[quartier]" value="<?php echo htmlspecialchars($property['quartier'] ?? ''); ?>">
                            <input type="hidden" name="extra[budget]" value="<?php echo htmlspecialchars($property['price']); ?>">
                            <input type="text" name="first_name" required placeholder="Nom" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <input type="text" name="last_name" placeholder="Prénom" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <input type="tel" name="phone" required placeholder="Numéro de téléphone" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <textarea name="extra[commentaire]" rows="3" placeholder="Commentaire (optionnel)" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold"></textarea>
                            <button type="submit" class="w-full bg-tenezer-dark text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                                Envoyer la demande
                            </button>
                            <p id="visit-form-msg" class="text-xs text-center hidden"></p>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'similaire.php'; ?>

</main>

<script>
document.querySelectorAll('.whatsapp-track-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const data = new FormData();
        data.append('entity_type', 'property');
        data.append('entity_id', this.getAttribute('data-entity-id'));
        fetch('track_whatsapp.php', { method: 'POST', body: data }).catch(() => {});
    });
});

document.getElementById('visit-form')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const form = e.target;
    const msg = document.getElementById('visit-form-msg');
    const data = new FormData(form);

    fetch('process_request.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                window.location.href = 'confirmation.php?id=' + res.id;
            } else {
                msg.textContent = res.message || "Une erreur est survenue.";
                msg.classList.remove('hidden');
                msg.classList.add('text-red-600');
            }
        })
        .catch(() => {
            msg.textContent = "Erreur réseau, veuillez réessayer.";
            msg.classList.remove('hidden');
            msg.classList.add('text-red-600');
        });
});
</script>
<?php if ($isMeuble): ?>
<script src="assets/js/request-form.js"></script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
