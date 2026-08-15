<?php
require_once 'mfbase/database.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="bg-slate-50 min-h-screen pb-20">

    <section class="bg-tenezer-dark py-16">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <span class="text-tenezer-gold text-[10px] font-black uppercase tracking-[0.4em]">Module 5</span>
            <h1 class="font-serif text-3xl md:text-5xl font-black text-white mt-3">Pièces d'identité perdues ou retrouvées</h1>
            <p class="text-gray-400 text-sm mt-4 max-w-xl mx-auto">Vous avez perdu un document ou vous en avez retrouvé un ? Signalez-le ici.</p>
        </div>
    </section>

    <section class="max-w-3xl mx-auto px-6 -mt-8">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">

            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                <i class="fas fa-circle-info mr-1"></i> La personne ayant retrouvé le document peut être récompensée. La personne qui récupère son document paie après satisfaction — montant maximal : <strong>5 000 FCFA</strong>.
            </div>

            <form class="tenezer-request-form space-y-5">
                <input type="hidden" name="module" value="objet_perdu">

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
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Type de document *</label>
                        <select name="extra[type_document]" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <option value="CNI">CNI</option>
                            <option value="Passeport">Passeport</option>
                            <option value="Acte de naissance">Acte de naissance</option>
                            <option value="Permis">Permis</option>
                            <option value="Autres">Autres</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Situation *</label>
                        <select name="extra[situation]" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                            <option value="Perdu">Perdu</option>
                            <option value="Retrouvé">Retrouvé</option>
                        </select>
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
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Date</label>
                        <input type="date" name="extra[date]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Heure</label>
                        <input type="time" name="extra[heure]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Lieu précis</label>
                        <input type="text" name="extra[lieu_precis]" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Commentaire</label>
                    <textarea name="extra[commentaire]" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Téléphone *</label>
                        <input type="tel" name="phone" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">WhatsApp</label>
                        <input type="tel" name="whatsapp" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Facebook</label>
                    <input type="text" name="extra[facebook]" placeholder="Lien ou nom du profil" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-tenezer-gold">
                </div>

                <p class="form-msg hidden text-xs text-red-600"></p>

                <button type="submit" class="w-full bg-tenezer-dark text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                    Envoyer
                </button>
            </form>
        </div>
    </section>

</main>

<script src="assets/js/request-form.js"></script>
<?php require_once 'includes/footer.php'; ?>
