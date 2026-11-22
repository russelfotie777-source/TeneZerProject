<?php
require_once 'mfbase/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Biens en vedette (tolère l'absence de la table le temps de la migration de schéma)
$featured = [];
try {
    $stmt = $pdo->query("SELECT *
                          FROM properties
                          WHERE status = 'active'
                          ORDER BY created_at DESC
                          LIMIT 6");
    $featured = $stmt->fetchAll();
} catch (PDOException $e) {
    $featured = [];
}
?>

<main class="bg-white">

    <!-- HERO -->
    <section class="relative bg-tenezer-dark overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_top_right,_#F1A80A,_transparent_60%)]"></div>
        <div class="max-w-7xl mx-auto px-6 py-24 md:py-36 relative z-10 text-center">
            <span class="inline-block text-[10px] md:text-xs font-black uppercase tracking-[0.5em] text-tenezer-gold mb-6">Servir pour être Servi</span>
            <h1 class="font-serif text-4xl md:text-6xl lg:text-7xl font-black text-white leading-tight mb-8">
                TENEZER <span class="text-tenezer-gold">— </span><br class="hidden md:block">Le Carrefour de l'Immobilier
            </h1>
            <p class="text-gray-300 text-sm md:text-base max-w-2xl mx-auto mb-12 font-light">
                Location de logements, terrains, achat &amp; vente, logements meublés, services ménagers et objets perdus — Tenezer est l'intermédiaire de confiance entre vous et les propriétaires certifiés.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="biens.php" class="w-full sm:w-auto px-10 py-4 bg-tenezer-gold text-tenezer-dark rounded-full font-black text-xs uppercase tracking-[0.3em] hover:brightness-110 transition-all shadow-xl">
                    Voir les biens disponibles
                </a>
                <a href="https://wa.me/237672163188" target="_blank" class="w-full sm:w-auto px-10 py-4 border border-white/20 text-white rounded-full font-black text-xs uppercase tracking-[0.3em] hover:border-tenezer-gold hover:text-tenezer-gold transition-all flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp"></i> Nous écrire
                </a>
            </div>
        </div>
    </section>

    <!-- RECHERCHE RAPIDE -->
    <section class="max-w-5xl mx-auto px-6 -mt-8 relative z-10">
        <form action="biens.php" method="GET" class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div class="sm:col-span-2">
                <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Ville</label>
                <input type="text" name="city" placeholder="Ex: Douala, Yaoundé..." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Catégorie</label>
                <select name="category" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    <option value="logement">Logements</option>
                    <option value="terrain">Terrains</option>
                </select>
            </div>
            <button type="submit" class="w-full sm:w-auto bg-tenezer-dark text-white rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                <i class="fas fa-search mr-2"></i>Rechercher
            </button>
        </form>
    </section>

    <!-- 5 MODULES -->
    <section class="max-w-7xl mx-auto px-6 pt-16 pb-20">
        <div class="text-center mb-14">
            <span class="text-tenezer-gold text-[10px] font-black uppercase tracking-[0.4em]">Nos services</span>
            <h2 class="font-serif text-3xl md:text-4xl font-black text-tenezer-dark mt-3">Tout l'immobilier, en un seul endroit</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <a href="biens.php?category=logement" class="group p-8 rounded-3xl border border-gray-100 hover:border-tenezer-gold hover:shadow-xl transition-all">
                <div class="w-14 h-14 rounded-2xl bg-tenezer-dark text-tenezer-gold flex items-center justify-center text-xl mb-6 group-hover:bg-tenezer-gold group-hover:text-tenezer-dark transition-all"><i class="fas fa-house-user"></i></div>
                <h3 class="font-black text-sm uppercase tracking-wide text-tenezer-dark mb-2">Location de logements</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Chambres, studios, appartements, bureaux, magasins.</p>
            </a>
            <a href="biens.php?category=terrain" class="group p-8 rounded-3xl border border-gray-100 hover:border-tenezer-gold hover:shadow-xl transition-all">
                <div class="w-14 h-14 rounded-2xl bg-tenezer-dark text-tenezer-gold flex items-center justify-center text-xl mb-6 group-hover:bg-tenezer-gold group-hover:text-tenezer-dark transition-all"><i class="fas fa-map-marked-alt"></i></div>
                <h3 class="font-black text-sm uppercase tracking-wide text-tenezer-dark mb-2">Terrains</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Des terrains vérifiés dans les zones qui vous intéressent.</p>
            </a>
            <a href="achat-vente.php" class="group p-8 rounded-3xl border border-gray-100 hover:border-tenezer-gold hover:shadow-xl transition-all">
                <div class="w-14 h-14 rounded-2xl bg-tenezer-dark text-tenezer-gold flex items-center justify-center text-xl mb-6 group-hover:bg-tenezer-gold group-hover:text-tenezer-dark transition-all"><i class="fas fa-handshake"></i></div>
                <h3 class="font-black text-sm uppercase tracking-wide text-tenezer-dark mb-2">Achat &amp; Vente</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Maisons, villas, immeubles, terrains à acheter ou vendre.</p>
            </a>
            <a href="meubles.php" class="group p-8 rounded-3xl border border-gray-100 hover:border-tenezer-gold hover:shadow-xl transition-all">
                <div class="w-14 h-14 rounded-2xl bg-tenezer-dark text-tenezer-gold flex items-center justify-center text-xl mb-6 group-hover:bg-tenezer-gold group-hover:text-tenezer-dark transition-all"><i class="fas fa-suitcase-rolling"></i></div>
                <h3 class="font-black text-sm uppercase tracking-wide text-tenezer-dark mb-2">Logements Meublés</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Séjours courts, meublés et équipés, partout au Cameroun.</p>
            </a>
            <a href="services-menagers.php" class="group p-8 rounded-3xl border border-gray-100 hover:border-tenezer-gold hover:shadow-xl transition-all">
                <div class="w-14 h-14 rounded-2xl bg-tenezer-dark text-tenezer-gold flex items-center justify-center text-xl mb-6 group-hover:bg-tenezer-gold group-hover:text-tenezer-dark transition-all"><i class="fas fa-broom"></i></div>
                <h3 class="font-black text-sm uppercase tracking-wide text-tenezer-dark mb-2">Services Ménagers</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Trouvez un(e) employé(e) de maison ou proposez vos services.</p>
            </a>
        </div>
        <div class="mt-6 text-center">
            <a href="objets.php" class="inline-flex items-center gap-2 text-tenezer-dark text-xs font-black uppercase tracking-widest hover:text-tenezer-gold transition-colors">
                <i class="fas fa-id-card"></i> Pièce d'identité perdue ou retrouvée ? Cliquez ici
            </a>
        </div>
    </section>

    <!-- BIENS EN VEDETTE -->
    <?php if (!empty($featured)): ?>
    <section class="bg-slate-50 py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-end justify-between mb-14">
                <div>
                    <span class="text-tenezer-gold text-[10px] font-black uppercase tracking-[0.4em]">Sélection Tenezer</span>
                    <h2 class="font-serif text-3xl md:text-4xl font-black text-tenezer-dark mt-3">Biens en vedette</h2>
                </div>
                <a href="biens.php" class="hidden sm:block text-xs font-black uppercase tracking-widest text-tenezer-dark hover:text-tenezer-gold transition-colors">Tout voir &rarr;</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($featured as $p): ?>
                <a href="detail.php?id=<?php echo (int)$p['id']; ?>" class="group rounded-3xl overflow-hidden bg-white border border-gray-100 shadow-sm hover:shadow-2xl transition-all">
                    <div class="h-52 overflow-hidden bg-gray-100">
                        <?php $photo = tenezer_main_photo($pdo, $p['id']); ?>
                        <?php if ($photo): ?>
                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-image text-4xl"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <span class="text-[9px] font-black uppercase tracking-widest text-tenezer-gold"><?php echo htmlspecialchars($p['category'] === 'terrain' ? 'Terrain' : ($p['category'] === 'meuble' ? tenezer_meuble_label($p['type']) : ucfirst($p['type']))); ?></span>
                        <h3 class="font-bold text-slate-900 mt-2 mb-1"><?php echo htmlspecialchars($p['title']); ?></h3>
                        <p class="text-xs text-gray-500 mb-4"><i class="fas fa-location-dot mr-1"></i><?php echo htmlspecialchars($p['quartier'] ? $p['quartier'] . ', ' . $p['city'] : $p['city']); ?></p>
                        <p class="font-black text-tenezer-dark"><?php echo htmlspecialchars(tenezer_price_label($p)); ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- COMMENT CA MARCHE -->
    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="text-center mb-14">
            <span class="text-tenezer-gold text-[10px] font-black uppercase tracking-[0.4em]">Simple &amp; rapide</span>
            <h2 class="font-serif text-3xl md:text-4xl font-black text-tenezer-dark mt-3">Comment ça marche</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">
            <div>
                <div class="w-16 h-16 mx-auto rounded-full bg-tenezer-dark text-tenezer-gold flex items-center justify-center text-xl font-black mb-6">1</div>
                <h3 class="font-black text-sm uppercase tracking-wide mb-2">Consultez librement</h3>
                <p class="text-xs text-gray-500 max-w-xs mx-auto">Parcourez les biens disponibles sans créer de compte.</p>
            </div>
            <div>
                <div class="w-16 h-16 mx-auto rounded-full bg-tenezer-dark text-tenezer-gold flex items-center justify-center text-xl font-black mb-6">2</div>
                <h3 class="font-black text-sm uppercase tracking-wide mb-2">Envoyez une demande</h3>
                <p class="text-xs text-gray-500 max-w-xs mx-auto">Depuis l'application ou directement par WhatsApp.</p>
            </div>
            <div>
                <div class="w-16 h-16 mx-auto rounded-full bg-tenezer-dark text-tenezer-gold flex items-center justify-center text-xl font-black mb-6">3</div>
                <h3 class="font-black text-sm uppercase tracking-wide mb-2">Tenezer vous met en relation</h3>
                <p class="text-xs text-gray-500 max-w-xs mx-auto">Notre équipe fait l'intermédiaire avec le propriétaire ou le bailleur.</p>
            </div>
        </div>
    </section>

    <!-- ESPACE PROPRIETAIRE CTA -->
    <section class="bg-tenezer-dark py-20">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <h2 class="font-serif text-2xl md:text-3xl font-black text-white mb-4">Vous êtes propriétaire ou bailleur ?</h2>
            <p class="text-gray-400 text-sm max-w-xl mx-auto mb-8">Inscrivez-vous, faites valider votre compte, et publiez vos biens directement sur Tenezer.</p>
            <a href="espace-proprietaire/inscription.php" class="inline-block px-10 py-4 bg-tenezer-gold text-tenezer-dark rounded-full font-black text-xs uppercase tracking-[0.3em] hover:brightness-110 transition-all">
                Devenir propriétaire partenaire
            </a>
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>
