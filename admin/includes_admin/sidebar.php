<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0 lg:static lg:h-screen flex flex-col border-r border-slate-800 shadow-2xl lg:shadow-none">
    <div class="h-20 flex items-center px-6 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-[#F1A80A] flex items-center justify-center text-slate-900 font-black text-lg">
                T
            </div>
            <div>
                <h1 class="text-lg font-black text-white tracking-wide">TENEZER</h1>
                <p class="text-[10px] text-[#F1A80A] uppercase tracking-widest font-bold">Administration</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Principal</p>
        <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $current_page == 'index.php' ? 'bg-[#F1A80A]/10 text-[#F1A80A] font-medium' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-chart-pie w-5 text-center"></i>
            <span>Tableau de bord</span>
        </a>
        <a href="manage_properties.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $current_page == 'manage_properties.php' ? 'bg-[#F1A80A]/10 text-[#F1A80A] font-medium' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-building w-5 text-center"></i>
            <span>Logements &amp; Terrains</span>
        </a>
        <a href="manage_meubles.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo in_array($current_page, ['manage_meubles.php', 'meuble-form.php'], true) ? 'bg-[#F1A80A]/10 text-[#F1A80A] font-medium' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-couch w-5 text-center"></i>
            <span>Logements meublés</span>
        </a>
        <a href="manage_owners.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $current_page == 'manage_owners.php' ? 'bg-[#F1A80A]/10 text-[#F1A80A] font-medium' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-user-check w-5 text-center"></i>
            <span>Propriétaires</span>
        </a>
        <a href="manage_requests.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $current_page == 'manage_requests.php' ? 'bg-[#F1A80A]/10 text-[#F1A80A] font-medium' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-inbox w-5 text-center"></i>
            <span>Demandes (5 modules)</span>
        </a>

        <div class="mt-8 mb-3">
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Analyse</p>
        </div>
        <a href="stats.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $current_page == 'stats.php' ? 'bg-[#F1A80A]/10 text-[#F1A80A] font-medium' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span>Statistiques</span>
        </a>
        <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
        <a href="logs.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $current_page == 'logs.php' ? 'bg-[#F1A80A]/10 text-[#F1A80A] font-medium' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-fingerprint w-5 text-center"></i>
            <span>Journal d'activité</span>
        </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
            <div class="mt-8 mb-3">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sécurité</p>
            </div>
            <a href="manage_users.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $current_page == 'manage_users.php' ? 'bg-[#F1A80A]/10 text-[#F1A80A] font-medium' : 'hover:bg-slate-800 hover:text-white'; ?>">
                <i class="fas fa-user-shield w-5 text-center"></i>
                <span>Gestion des Comptes</span>
            </a>
        <?php endif; ?>
    </nav>
    <div class="p-4 border-t border-slate-800">
        <a href="logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-red-500/10 hover:text-red-400 transition-colors">
            <i class="fas fa-sign-out-alt w-5 text-center"></i>
            <span class="font-medium">Déconnexion</span>
        </a>
    </div>
</aside>
<div class="flex-1 flex flex-col h-screen min-w-0">
