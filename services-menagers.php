<?php
require_once 'mfbase/database.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="bg-slate-50 min-h-screen pb-20">

    <section class="bg-tenezer-dark py-16">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <span class="text-tenezer-gold text-[10px] font-black uppercase tracking-[0.4em]">Module 4</span>
            <h1 class="font-serif text-3xl md:text-5xl font-black text-white mt-3">Services Ménagers</h1>
            <p class="text-gray-400 text-sm mt-4 max-w-xl mx-auto">Trouvez un(e) employé(e) de maison de confiance, ou proposez vos services.</p>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 -mt-8">
        <div class="flex justify-center gap-3 mb-6">
            <button type="button" onclick="showTab('worker')" id="tab-btn-worker" class="px-6 py-2 rounded-full text-[11px] font-black uppercase tracking-widest bg-tenezer-gold text-tenezer-dark transition-all">Recherche de travailleur</button>
            <button type="button" onclick="showTab('job')" id="tab-btn-job" class="px-6 py-2 rounded-full text-[11px] font-black uppercase tracking-widest bg-white border border-gray-200 text-gray-600 transition-all">Recherche d'emploi</button>
        </div>

        <!-- A. RECHERCHE DE TRAVAILLEUR -->
        <div id="tab-worker" class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                <i class="fas fa-circle-info mr-1"></i> Inscription : <strong>2 000 FCFA</strong> · Commission après satisfaction : <strong>10 000 FCFA</strong>.
            </div>
            <form class="tenezer-request-form space-y-5">
                <input type="hidden" name="module" value="service_menager">
                <input type="hidden" name="sub_type" value="recherche_travailleur">

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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Ville *</label>
                        <input type="text" name="city" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Quartier</label>
                        <input type="text" name="extra[quartier]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Personne recherchée</label>
                        <select name="extra[type_personne]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <option value="Homme">Homme</option>
                            <option value="Femme">Femme</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tranche d'âge</label>
                        <input type="text" name="extra[tranche_age]" placeholder="Ex: 25-35 ans" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Religion</label>
                        <input type="text" name="extra[religion]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Travail demandé</label>
                    <input type="text" name="extra[travail_demande]" placeholder="Ex: Ménage, cuisine, garde d'enfants..." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Salaire proposé</label>
                        <input type="text" name="extra[salaire]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Horaires</label>
                        <input type="text" name="extra[horaires]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Jours de travail</label>
                        <input type="text" name="extra[jours_travail]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
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

        <!-- B. RECHERCHE D'EMPLOI -->
        <div id="tab-job" class="hidden bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800">
                <i class="fas fa-circle-info mr-1"></i> Aucun frais d'inscription.
            </div>
            <form class="tenezer-request-form space-y-5">
                <input type="hidden" name="module" value="service_menager">
                <input type="hidden" name="sub_type" value="recherche_emploi">

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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Ville *</label>
                        <input type="text" name="city" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Quartier</label>
                        <input type="text" name="extra[quartier]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Sexe</label>
                        <select name="extra[sexe]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <option value="Homme">Homme</option>
                            <option value="Femme">Femme</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tranche d'âge</label>
                        <input type="text" name="extra[tranche_age]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Région d'origine</label>
                        <input type="text" name="extra[region_origine]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Expériences professionnelles</label>
                    <textarea name="extra[experiences]" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Disponibilité</label>
                    <input type="text" name="extra[disponibilite]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
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

<script>
function showTab(tab) {
    document.getElementById('tab-worker').classList.toggle('hidden', tab !== 'worker');
    document.getElementById('tab-job').classList.toggle('hidden', tab !== 'job');
    document.getElementById('tab-btn-worker').className = 'px-6 py-2 rounded-full text-[11px] font-black uppercase tracking-widest transition-all ' + (tab === 'worker' ? 'bg-tenezer-gold text-tenezer-dark' : 'bg-white border border-gray-200 text-gray-600');
    document.getElementById('tab-btn-job').className = 'px-6 py-2 rounded-full text-[11px] font-black uppercase tracking-widest transition-all ' + (tab === 'job' ? 'bg-tenezer-gold text-tenezer-dark' : 'bg-white border border-gray-200 text-gray-600');
}
</script>
<script src="assets/js/request-form.js"></script>
<?php require_once 'includes/footer.php'; ?>
