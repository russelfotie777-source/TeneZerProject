<header class="sticky top-0 z-[100] bg-tenezer-dark border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 md:px-6 h-16 md:h-20 flex items-center justify-between">

        <a href="index.php" class="flex items-center gap-3 group">
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl overflow-hidden border border-tenezer-gold/30 shadow-sm transition-transform group-hover:scale-105">
                <img src="includes/logo.jpeg" alt="Tenezer Immobilier" class="w-full h-full object-cover">
            </div>
            <div class="flex flex-col leading-none">
                <span class="font-serif font-black text-lg md:text-xl tracking-tight text-white">TENEZER</span>
                <span class="text-[8px] md:text-[9px] font-bold uppercase tracking-[0.25em] text-tenezer-gold -mt-0.5">Servir pour être Servi</span>
            </div>
        </a>

        <nav class="hidden lg:flex items-center gap-8">
            <a href="index.php" class="nav-link text-[10px] font-black uppercase tracking-[0.2em] text-white/90 hover:text-tenezer-gold transition-colors">Accueil</a>
            <a href="biens.php?category=logement" class="nav-link text-[10px] font-black uppercase tracking-[0.2em] text-white/90 hover:text-tenezer-gold transition-colors">Location</a>
            <a href="biens.php?category=terrain" class="nav-link text-[10px] font-black uppercase tracking-[0.2em] text-white/90 hover:text-tenezer-gold transition-colors">Terrains</a>

            <div class="lang-container relative group">
                <button class="nav-link flex items-center gap-1 text-[10px] font-black uppercase tracking-[0.2em] text-white/90 hover:text-tenezer-gold transition-colors">
                    Nos Services <i class="fas fa-chevron-down text-[8px]"></i>
                </button>
                <div class="lang-dropdown absolute left-0 mt-4 w-56 bg-white rounded-2xl shadow-2xl overflow-hidden py-2 z-[110]">
                    <a href="achat-vente.php" class="block px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-tenezer-dark hover:bg-tenezer-gold/10 transition-colors">Achat &amp; Vente</a>
                    <a href="meubles.php" class="block px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-tenezer-dark hover:bg-tenezer-gold/10 transition-colors">Logements Meublés</a>
                    <a href="services-menagers.php" class="block px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-tenezer-dark hover:bg-tenezer-gold/10 transition-colors">Services Ménagers</a>
                    <a href="objets.php" class="block px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-tenezer-dark hover:bg-tenezer-gold/10 transition-colors">Objets Perdus/Retrouvés</a>
                </div>
            </div>

            <a href="contact.php" class="nav-link text-[10px] font-black uppercase tracking-[0.2em] text-white/90 hover:text-tenezer-gold transition-colors">Contact</a>
        </nav>

        <div class="flex items-center gap-3">
            <a href="espace-proprietaire/login.php" class="hidden md:flex items-center gap-2 px-4 py-2 rounded-full border border-tenezer-gold/40 text-tenezer-gold text-[10px] font-black uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                <i class="fas fa-user-tie text-xs"></i> Espace Propriétaire
            </a>

            <a href="https://wa.me/237672163188" target="_blank" class="flex items-center justify-center w-10 h-10 md:w-11 md:h-11 bg-tenezer-gold text-tenezer-dark rounded-full hover:brightness-110 transition-all duration-300 shadow-lg">
                <i class="fab fa-whatsapp text-lg"></i>
            </a>

            <button id="mobile-menu-btn" class="lg:hidden text-white text-xl">
                <i class="fas fa-bars-staggered"></i>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden lg:hidden bg-tenezer-charcoal border-t border-white/10">
        <div class="flex flex-col py-2">
            <a href="index.php" class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-white/90 hover:text-tenezer-gold">Accueil</a>
            <a href="biens.php?category=logement" class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-white/90 hover:text-tenezer-gold">Location de logements</a>
            <a href="biens.php?category=terrain" class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-white/90 hover:text-tenezer-gold">Terrains</a>
            <a href="achat-vente.php" class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-white/90 hover:text-tenezer-gold">Achat &amp; Vente</a>
            <a href="meubles.php" class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-white/90 hover:text-tenezer-gold">Logements Meublés</a>
            <a href="services-menagers.php" class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-white/90 hover:text-tenezer-gold">Services Ménagers</a>
            <a href="objets.php" class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-white/90 hover:text-tenezer-gold">Objets Perdus/Retrouvés</a>
            <a href="contact.php" class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-white/90 hover:text-tenezer-gold">Contact</a>
            <a href="espace-proprietaire/login.php" class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-tenezer-gold">Espace Propriétaire</a>
        </div>
    </div>
</header>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function () {
        document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });
</script>
