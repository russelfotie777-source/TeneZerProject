<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | ' : ''; ?>Espace Propriétaire — Tenezer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Montserrat', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: { tenezer: { dark: '#0B0B0B', charcoal: '#1A1A1A', gold: '#F1A80A' } }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-tenezer-dark antialiased">

<header class="bg-tenezer-dark border-b border-white/10">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="<?php echo isset($_SESSION['owner_logged_in']) ? 'dashboard.php' : 'login.php'; ?>" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl overflow-hidden border border-tenezer-gold/30">
                <img src="../includes/logo.jpeg" alt="Tenezer" class="w-full h-full object-cover">
            </div>
            <span class="font-serif font-black text-white text-sm">TENEZER <span class="text-tenezer-gold">· Propriétaire</span></span>
        </a>
        <div class="flex items-center gap-5">
            <a href="../index.php" class="text-[10px] font-black uppercase tracking-widest text-white/70 hover:text-tenezer-gold transition-colors">Retour au site</a>
            <?php if (isset($_SESSION['owner_logged_in'])): ?>
            <a href="logout.php" class="text-[10px] font-black uppercase tracking-widest text-white/70 hover:text-red-400 transition-colors"><i class="fas fa-sign-out-alt mr-1"></i>Déconnexion</a>
            <?php endif; ?>
        </div>
    </div>
</header>
