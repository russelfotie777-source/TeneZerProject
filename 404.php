<?php
http_response_code(404);
require_once 'mfbase/database.php';
require_once 'includes/functions.php';

$page_title = "Page introuvable | Tenezer Immobilier";

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="bg-white min-h-screen flex items-center justify-center py-24 px-6">
    <div class="text-center max-w-xl">
        <span class="font-serif text-8xl md:text-9xl font-black text-slate-100">404</span>
        <h1 class="font-serif text-2xl md:text-3xl font-black text-tenezer-dark -mt-6 mb-4">Cette page n'existe pas</h1>
        <p class="text-gray-500 text-sm mb-10">Le lien que vous avez suivi est peut-être erroné, ou le bien recherché n'est plus disponible.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="index.php" class="px-8 py-4 bg-tenezer-dark text-white rounded-full font-black text-xs uppercase tracking-widest hover:bg-tenezer-gold hover:text-tenezer-dark transition-all">
                Retour à l'accueil
            </a>
            <a href="biens.php" class="px-8 py-4 border border-gray-200 text-tenezer-dark rounded-full font-black text-xs uppercase tracking-widest hover:border-tenezer-gold transition-all">
                Voir les biens disponibles
            </a>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
