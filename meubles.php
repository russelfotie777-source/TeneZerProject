<?php
require_once 'mfbase/database.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="bg-slate-50 min-h-screen pb-20">

    <section class="bg-tenezer-dark py-16">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <span class="text-tenezer-gold text-[10px] font-black uppercase tracking-[0.4em]">Module 3</span>
            <h1 class="font-serif text-3xl md:text-5xl font-black text-white mt-3">Logements Meublés</h1>
            <p class="text-gray-400 text-sm mt-4 max-w-xl mx-auto">Pour un séjour court, dites-nous vos critères, nous vous trouvons un logement meublé adapté.</p>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 -mt-8">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">

            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                <i class="fas fa-circle-info mr-1"></i> Inscription : <strong>2 000 FCFA</strong> · Commission propriétaire : <strong>10 %</strong> après satisfaction.
            </div>

            <form class="tenezer-request-form space-y-5">
                <input type="hidden" name="module" value="meuble">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nom *</label>
                        <input type="text" name="first_name" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Prénom</label>
                        <input type="text" name="last_name" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Ville recherchée *</label>
                    <input type="text" name="city" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Quartiers recherchés (jusqu'à 3 propositions)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <input type="text" name="extra[quartier_1]" placeholder="Quartier 1" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                        <input type="text" name="extra[quartier_2]" placeholder="Quartier 2" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                        <input type="text" name="extra[quartier_3]" placeholder="Quartier 3" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Type de logement</label>
                        <select name="extra[type_logement]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <option value="Chambre">Chambre</option>
                            <option value="Studio">Studio</option>
                            <option value="Appartement">Appartement</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Chambres</label>
                        <input type="number" name="extra[nb_chambres]" min="0" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Douches</label>
                        <input type="number" name="extra[nb_douches]" min="0" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Budget (FCFA)</label>
                        <input type="number" name="extra[budget]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Date d'arrivée</label>
                        <input type="date" name="extra[date_arrivee]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Durée du séjour</label>
                        <input type="text" name="extra[duree_sejour]" placeholder="Ex: 2 semaines" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Numéro du référent *</label>
                    <input type="tel" name="phone" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Commentaire</label>
                    <textarea name="extra[commentaire]" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold"></textarea>
                </div>

                <p class="form-msg hidden text-xs text-red-600"></p>

                <button type="submit" class="w-full bg-tenezer-dark text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                    Envoyer ma demande
                </button>
            </form>
        </div>
    </section>

</main>

<script src="assets/js/request-form.js"></script>
<?php require_once 'includes/footer.php'; ?>
