<footer class="bg-tenezer-dark pt-24 pb-12 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 mb-20">

            <div class="lg:col-span-5 space-y-8">
                <a href="index.php" class="flex items-center gap-4 group">
                    <img src="includes/logo.jpeg" alt="Tenezer Immobilier" class="h-14 w-14 rounded-xl object-cover border-2 border-tenezer-gold/30 shadow-2xl transition-transform duration-500 group-hover:scale-105">
                    <div class="flex flex-col">
                        <span class="font-serif text-2xl font-black tracking-tight text-white">TENEZER</span>
                        <span class="text-[9px] font-bold uppercase tracking-[0.3em] text-tenezer-gold">Le Carrefour de l'Immobilier</span>
                    </div>
                </a>
                <p class="text-gray-300 text-sm leading-relaxed max-w-sm font-light">
                    Tenezer met en relation locataires, acheteurs et propriétaires certifiés partout au Cameroun : location de logements, terrains, achat/vente, logements meublés, services ménagers et objets perdus — tout passe par nous.
                </p>
            </div>

            <div class="lg:col-span-3 space-y-8">
                <h4 class="text-white font-black text-[10px] uppercase tracking-[0.4em] mb-10">Nos Modules</h4>
                <ul class="space-y-4">
                    <li><a href="biens.php?category=logement" class="text-gray-400 text-xs font-bold uppercase tracking-widest hover:text-tenezer-gold transition-all flex items-center gap-2 group"><span class="w-0 group-hover:w-4 h-px bg-tenezer-gold transition-all"></span> Location de logements</a></li>
                    <li><a href="biens.php?category=terrain" class="text-gray-400 text-xs font-bold uppercase tracking-widest hover:text-tenezer-gold transition-all flex items-center gap-2 group"><span class="w-0 group-hover:w-4 h-px bg-tenezer-gold transition-all"></span> Terrains</a></li>
                    <li><a href="achat-vente.php" class="text-gray-400 text-xs font-bold uppercase tracking-widest hover:text-tenezer-gold transition-all flex items-center gap-2 group"><span class="w-0 group-hover:w-4 h-px bg-tenezer-gold transition-all"></span> Achat &amp; Vente</a></li>
                    <li><a href="meubles.php" class="text-gray-400 text-xs font-bold uppercase tracking-widest hover:text-tenezer-gold transition-all flex items-center gap-2 group"><span class="w-0 group-hover:w-4 h-px bg-tenezer-gold transition-all"></span> Logements Meublés</a></li>
                    <li><a href="services-menagers.php" class="text-gray-400 text-xs font-bold uppercase tracking-widest hover:text-tenezer-gold transition-all flex items-center gap-2 group"><span class="w-0 group-hover:w-4 h-px bg-tenezer-gold transition-all"></span> Services Ménagers</a></li>
                    <li><a href="objets.php" class="text-gray-400 text-xs font-bold uppercase tracking-widest hover:text-tenezer-gold transition-all flex items-center gap-2 group"><span class="w-0 group-hover:w-4 h-px bg-tenezer-gold transition-all"></span> Objets Perdus/Retrouvés</a></li>
                </ul>
            </div>

            <div class="lg:col-span-4 space-y-10">
                <h4 class="text-white font-black text-[10px] uppercase tracking-[0.4em]">Contact</h4>

                <div class="space-y-6">
                    <a href="https://wa.me/237672163188" target="_blank" class="flex items-center gap-5 group">
                        <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-tenezer-gold group-hover:bg-tenezer-gold group-hover:text-tenezer-dark transition-all">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">WhatsApp Direct</span>
                            <span class="text-white font-bold text-sm">+237 672 163 188</span>
                        </div>
                    </a>

                    <a href="mailto:ztegnidemanou@gmail.com" class="flex items-center gap-5 group">
                        <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-tenezer-gold group-hover:bg-tenezer-gold group-hover:text-tenezer-dark transition-all">
                            <i class="fas fa-envelope text-xl"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Email</span>
                            <span class="text-white font-bold text-sm">ztegnidemanou@gmail.com</span>
                        </div>
                    </a>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <a href="#" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:bg-tenezer-gold hover:text-tenezer-dark hover:border-transparent transition-all"><i class="fab fa-facebook-f text-xs"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white hover:bg-tenezer-gold hover:text-tenezer-dark hover:border-transparent transition-all"><i class="fab fa-instagram text-xs"></i></a>
                </div>
            </div>
        </div>

        <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.3em]">
                &copy; <?php echo date('Y'); ?> <span class="text-white">TENEZER</span>. Tous droits réservés.
            </p>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.3em] italic">
                « Le Carrefour de l'Immobilier et des Services »
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fslightbox/3.4.1/index.min.js"></script>
<script src="assets/js/pwa-app.js"></script>
</body>
</html>
