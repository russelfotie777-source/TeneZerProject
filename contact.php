<?php
require_once 'mfbase/database.php';

// Coordonnées de contact Tenezer Immobilier
$phone_display = "+237 672 163 188";
$phone_link = "237672163188";
$email_contact = "ztegnidemanou@gmail.com";

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="bg-slate-50 min-h-screen pt-20 pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-24 relative">
            <div class="absolute inset-0 -top-20 flex justify-center opacity-[0.03] pointer-events-none">
                <span class="font-sans text-[12px] md:text-[15rem] font-black uppercase tracking-tighter text-tenezer-dark">Contact</span>
            </div>
            <h1 class="font-sans text-5xl md:text-7xl font-black text-slate-900 mb-6 tracking-tight relative z-10 uppercase">Parlons de votre projet</h1>
            <p class="text-tenezer-gold font-bold text-[10px] uppercase tracking-[0.6em] flex justify-center items-center gap-4 relative z-10">
                <span class="w-16 h-px bg-tenezer-gold/40"></span>
                Tenezer — Le Carrefour de l'Immobilier
                <span class="w-16 h-px bg-tenezer-gold/40"></span>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">

            <div class="lg:col-span-5 space-y-12">

                <div class="space-y-10">
                    <a href="https://wa.me/<?php echo $phone_link; ?>" target="_blank" class="group flex items-center gap-8 p-8 bg-white rounded-[2.5rem] border border-gray-100 hover:bg-tenezer-dark transition-all duration-700 shadow-sm hover:shadow-2xl">
                        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-tenezer-gold group-hover:bg-tenezer-gold group-hover:text-tenezer-dark transition-all duration-500 shadow-inner">
                            <i class="fab fa-whatsapp text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-tenezer-gold">WhatsApp Direct</p>
                            <span class="text-xl font-bold text-slate-900 group-hover:text-white transition-colors"><?php echo $phone_display; ?></span>
                        </div>
                    </a>

                    <a href="mailto:<?php echo $email_contact; ?>" class="group flex items-center gap-8 p-8 bg-white rounded-[2.5rem] border border-gray-100 hover:bg-tenezer-dark transition-all duration-700 shadow-sm hover:shadow-2xl">
                        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-tenezer-gold group-hover:bg-tenezer-gold group-hover:text-tenezer-dark transition-all duration-500 shadow-inner">
                            <i class="fas fa-envelope text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-tenezer-gold">Email</p>
                            <span class="text-xl font-bold text-slate-900 group-hover:text-white transition-colors"><?php echo $email_contact; ?></span>
                        </div>
                    </a>
                </div>

                <div class="bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 opacity-[0.03] text-slate-900">
                        <i class="fas fa-map-marked-alt text-[15rem]"></i>
                    </div>
                    <h3 class="font-sans text-2xl font-black text-slate-900 mb-8 uppercase tracking-tight">Nos Canaux</h3>
                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div class="text-tenezer-gold text-lg mt-0.5"><i class="fab fa-facebook"></i></div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Facebook</p>
                                <p class="text-sm font-bold text-slate-800">TENEZER</p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="text-tenezer-gold text-lg mt-0.5"><i class="fab fa-instagram"></i></div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Instagram</p>
                                <p class="text-sm font-bold text-slate-800">Tenezer Immobilier</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-tenezer-dark rounded-[3.5rem] p-12 md:p-16 text-white shadow-2xl relative overflow-hidden border border-white/5">
                    <div class="absolute -top-20 -left-20 w-64 h-64 bg-white/5 rounded-full blur-[100px]"></div>

                    <div class="relative z-10">
                        <h3 class="font-sans text-3xl font-black uppercase tracking-tight mb-4">Écrivez-nous</h3>
                        <p class="text-gray-400 text-xs font-light mb-12 italic">Un conseiller Tenezer vous répondra dans les meilleurs délais.</p>

                        <form action="https://wa.me/<?php echo $phone_link; ?>" method="GET" target="_blank" class="grid grid-cols-1 md:grid-cols-2 gap-8" onsubmit="return prepareWhatsAppMessage(this);">
                            <div class="md:col-span-1">
                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-3 tracking-[0.2em]">Votre Nom complet</label>
                                <input type="text" id="contact_name" required class="w-full bg-white/5 border-b border-white/10 py-4 focus:border-tenezer-gold outline-none text-sm transition-all font-medium tracking-wide text-white" placeholder="Ex: Peter Makosso">
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-3 tracking-[0.2em]">Votre Téléphone</label>
                                <input type="tel" id="contact_phone" required class="w-full bg-white/5 border-b border-white/10 py-4 focus:border-tenezer-gold outline-none text-sm transition-all font-medium tracking-wide text-white" placeholder="+237 ...">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-3 tracking-[0.2em]">Objet de votre message</label>
                                <div class="relative">
                                    <select id="contact_subject" class="w-full bg-transparent border-b border-white/10 py-4 focus:border-tenezer-gold outline-none text-sm transition-all font-medium text-gray-400 appearance-none">
                                        <option value="Location de logement" class="bg-tenezer-dark text-white">Location de logement</option>
                                        <option value="Terrain" class="bg-tenezer-dark text-white">Terrain</option>
                                        <option value="Achat / Vente" class="bg-tenezer-dark text-white">Achat / Vente de propriété</option>
                                        <option value="Logement meublé" class="bg-tenezer-dark text-white">Logement meublé</option>
                                        <option value="Service ménager" class="bg-tenezer-dark text-white">Service ménager</option>
                                        <option value="Objet perdu/retrouvé" class="bg-tenezer-dark text-white">Objet perdu / retrouvé</option>
                                        <option value="Devenir propriétaire partenaire" class="bg-tenezer-dark text-white">Devenir propriétaire partenaire</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-3 tracking-[0.2em]">Votre Message</label>
                                <textarea id="contact_message" rows="4" required class="w-full bg-white/5 border-b border-white/10 py-4 focus:border-tenezer-gold outline-none text-sm transition-all font-medium resize-none text-white" placeholder="Comment pouvons-nous vous aider ?"></textarea>
                            </div>
                            <div class="md:col-span-2 pt-8">
                                <button type="submit" class="w-full bg-tenezer-gold text-tenezer-dark py-6 rounded-2xl font-black text-xs uppercase tracking-[0.4em] hover:bg-white transition-all duration-500 shadow-xl transform hover:-translate-y-1">
                                    <i class="fab fa-whatsapp mr-2"></i>Envoyer via WhatsApp
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    function prepareWhatsAppMessage(form) {
        const name = document.getElementById('contact_name').value;
        const phone = document.getElementById('contact_phone').value;
        const subject = document.getElementById('contact_subject').value;
        const message = document.getElementById('contact_message').value;
        const text = `Bonjour Tenezer,%0AJe m'appelle ${encodeURIComponent(name)} (${encodeURIComponent(phone)}).%0AObjet : ${encodeURIComponent(subject)}.%0AMessage : ${encodeURIComponent(message)}`;
        form.action = "https://wa.me/<?php echo $phone_link; ?>?text=" + text;
        return true;
    }
</script>

<?php require_once 'includes/footer.php'; ?>
