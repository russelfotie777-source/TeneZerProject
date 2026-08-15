<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';
require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';

$moduleLabels = [
    'location'        => 'Location',
    'achat_vente'      => 'Achat / Vente',
    'meuble'           => 'Meublé',
    'service_menager'  => 'Service ménager',
    'objet_perdu'      => 'Objet perdu',
];

// --- KPIs ---
$total_requests = (int)$pdo->query("SELECT COUNT(*) FROM requests")->fetchColumn();
$closed_requests = (int)$pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'closed'")->fetchColumn();
$taux_cloture = $total_requests > 0 ? round(($closed_requests / $total_requests) * 100) : 0;
$total_properties = (int)$pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'active'")->fetchColumn();
$total_owners = (int)$pdo->query("SELECT COUNT(*) FROM owners WHERE status = 'approved'")->fetchColumn();

// --- Alertes : demandes en attente depuis plus de 48h ---
$stale_requests = $pdo->query("
    SELECT id, first_name, last_name, phone, module, created_at
    FROM requests
    WHERE status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 2 DAY)
    ORDER BY created_at ASC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// --- Demandes par mois (année en cours) ---
$monthly_stats = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%b') as month, COUNT(*) as total
    FROM requests
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY DATE_FORMAT(created_at, '%Y-%m'), month
    ORDER BY MIN(created_at) ASC
")->fetchAll(PDO::FETCH_ASSOC);
$labels = json_encode(array_column($monthly_stats, 'month'));
$monthly_counts = json_encode(array_map('intval', array_column($monthly_stats, 'total')));

// --- Répartition par module ---
$module_stats = $pdo->query("SELECT module, COUNT(*) as total FROM requests GROUP BY module ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);
$module_display_labels = array_map(fn($m) => $moduleLabels[$m['module']] ?? $m['module'], $module_stats);

// --- Top villes des demandes ---
$geo_cities = $pdo->query("SELECT city, COUNT(*) as count FROM requests WHERE city IS NOT NULL AND city != '' GROUP BY city ORDER BY count DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .chart-container { min-height: 350px; }
</style>

<main class="flex-1 overflow-y-auto p-4 lg:p-10">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Statistiques</h1>
            <p class="text-slate-500 font-medium">Activité et tendances de Tenezer Immobilier.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
            <span class="h-2 w-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-xs font-bold text-slate-700">SYSTÈME LIVE</span>
        </div>
    </div>

    <?php if (!empty($stale_requests)): ?>
    <div class="mb-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($stale_requests as $alert): ?>
        <div class="flex items-center gap-4 p-4 rounded-2xl border bg-red-50 border-red-100 shadow-sm">
            <div class="h-12 w-12 rounded-full bg-red-500 text-white flex items-center justify-center shadow-lg">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-red-600">En attente depuis <?php echo (new DateTime($alert['created_at']))->diff(new DateTime())->days; ?> j</p>
                <p class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars(trim($alert['first_name'] . ' ' . ($alert['last_name'] ?? ''))); ?></p>
                <p class="text-xs text-slate-500 italic"><?php echo htmlspecialchars($moduleLabels[$alert['module']] ?? $alert['module']); ?></p>
            </div>
            <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $alert['phone']); ?>" target="_blank" class="ml-auto h-8 w-8 bg-white rounded-full flex items-center justify-center text-emerald-500 shadow-sm border border-slate-100">
                <i class="fab fa-whatsapp"></i>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total demandes</p>
            <h3 class="text-3xl font-black text-slate-900"><?php echo $total_requests; ?></h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm border-l-4 border-l-emerald-500">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Taux de clôture</p>
            <h3 class="text-3xl font-black text-slate-900"><?php echo $taux_cloture; ?> <span class="text-sm text-slate-400">%</span></h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Biens actifs</p>
            <h3 class="text-3xl font-black text-slate-900"><?php echo $total_properties; ?></h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Propriétaires validés</p>
            <h3 class="text-3xl font-black text-slate-900"><?php echo $total_owners; ?></h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10">

        <div class="lg:col-span-8 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <h2 class="text-xl font-extrabold text-slate-900 mb-8 flex items-center gap-2">
                <i class="fas fa-chart-area text-blue-600"></i> Demandes par mois (<?php echo date('Y'); ?>)
            </h2>
            <div id="requestsChart" class="chart-container"></div>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tighter mb-6 flex items-center gap-2">
                    <i class="fas fa-layer-group text-blue-500"></i> Répartition par module
                </h3>
                <div id="moduleChart"></div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tighter mb-6 flex items-center gap-2">
                    <i class="fas fa-city text-emerald-500"></i> Top villes
                </h3>
                <div id="cityChart"></div>
            </div>
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
var revOptions = {
    series: [{ name: 'Demandes', data: <?php echo $monthly_counts; ?> }],
    chart: { type: 'area', height: 350, toolbar: { show: false }, zoom: { enabled: false } },
    colors: ['#F1A80A'],
    stroke: { curve: 'smooth', width: 4 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
    xaxis: { categories: <?php echo $labels; ?>, labels: { style: { colors: '#64748b', fontWeight: 600 } } },
    yaxis: { labels: { style: { colors: '#64748b', fontWeight: 600 } } },
    tooltip: { theme: 'light' }
};
new ApexCharts(document.querySelector("#requestsChart"), revOptions).render();

var moduleOptions = {
    series: [{ name: 'Demandes', data: <?php echo json_encode(array_map('intval', array_column($module_stats, 'total'))); ?> }],
    chart: { type: 'bar', height: 220, toolbar: { show: false } },
    plotOptions: { bar: { horizontal: true, borderRadius: 8, barHeight: '40%' } },
    colors: ['#0B0B0B'],
    xaxis: { categories: <?php echo json_encode($module_display_labels); ?>, labels: { show: false } },
    grid: { show: false },
    tooltip: { theme: 'light' }
};
new ApexCharts(document.querySelector("#moduleChart"), moduleOptions).render();

var cityOptions = {
    series: [{ name: 'Demandes', data: <?php echo json_encode(array_map('intval', array_column($geo_cities, 'count'))); ?> }],
    chart: { type: 'bar', height: 180, toolbar: { show: false } },
    plotOptions: { bar: { horizontal: true, borderRadius: 8, barHeight: '40%' } },
    colors: ['#10b981'],
    xaxis: { categories: <?php echo json_encode(array_column($geo_cities, 'city')); ?>, labels: { show: false } },
    grid: { show: false },
    tooltip: { theme: 'light' }
};
new ApexCharts(document.querySelector("#cityChart"), cityOptions).render();
</script>
