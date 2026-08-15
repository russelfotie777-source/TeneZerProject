<?php
require_once 'includes_admin/auth_check.php';
require_once '../mfbase/database.php';

// Protection stricte : Seul le Super Admin entre ici
if ($_SESSION['admin_role'] !== 'super_admin') {
    header('Location: index.php');
    exit();
}

require_once 'includes_admin/header_admin.php';
require_once 'includes_admin/sidebar.php';

// Récupération des logs avec jointure pour avoir le nom de l'admin
$query = "SELECT l.*, a.username, a.full_name, a.role 
          FROM audit_logs l 
          LEFT JOIN admins a ON l.admin_id = a.id 
          ORDER BY l.created_at DESC LIMIT 200";
$logs = $pdo->query($query)->fetchAll();

// Statistiques des logs
$total_actions = count($logs);
$unique_admins = count(array_unique(array_column($logs, 'admin_id')));
$today_count = $pdo->query("SELECT COUNT(*) FROM audit_logs WHERE DATE(created_at) = CURDATE()")->fetchColumn() ?: 0;
$week_count = $pdo->query("SELECT COUNT(*) FROM audit_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn() ?: 0;

// Types d'actions distincts
$action_types = $pdo->query("SELECT DISTINCT action FROM audit_logs ORDER BY action")->fetchAll(PDO::FETCH_COLUMN);
?>

<style>
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes pulseBorder {
        0%, 100% {
            border-color: rgba(59, 130, 246, 0.1);
        }
        50% {
            border-color: rgba(59, 130, 246, 0.4);
        }
    }
    
    .animate-slide {
        animation: slideIn 0.5s ease-out forwards;
    }
    
    .animate-fade-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .log-row {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        background: white;
    }
    
    .log-row::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        transform: scaleY(0);
        transition: transform 0.3s ease;
        border-radius: 0 4px 4px 0;
    }
    
    .log-row:hover::before {
        transform: scaleY(1);
    }
    
    .log-row:hover {
        background: linear-gradient(90deg, #fafbff 0%, #ffffff 100%);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .status-pill {
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 2px 8px;
        border-radius: 20px;
        display: inline-block;
    }
    
    .filter-btn-active {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: white;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .filter-btn {
        transition: all 0.2s ease;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
        border-color: #3b82f6;
    }
    
    /* Scrollbar personnalisée */
    .custom-scroll::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .custom-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Animations de chargement */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<main class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 via-white to-slate-100 p-6 lg:p-10 custom-scroll">
    
    <!-- En-tête avec statistiques -->
    <div class="animate-fade-up">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center shadow-xl">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Tenezer Sentinel</h1>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.15em] mt-1">Système de traçabilité avancé</p>
                    </div>
                </div>
            </div>
            
            <!-- Carte de statut système -->
            <div class="flex items-center gap-4 bg-white rounded-2xl px-5 py-3 shadow-md border border-slate-200">
                <div class="relative">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full animate-ping absolute"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full relative"></div>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase">État du système</p>
                    <p class="text-sm font-black text-emerald-600">SURVEILLANCE ACTIVE</p>
                </div>
                <div class="w-px h-8 bg-slate-200"></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase">Actions aujourd'hui</p>
                    <p class="text-xl font-black text-slate-800"><?php echo $today_count; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Cartes de statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total actions</p>
                        <p class="text-3xl font-black text-slate-800 mt-1"><?php echo number_format($total_actions); ?></p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-blue-600 text-lg"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-3">Dernières 200 entrées</p>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Administrateurs actifs</p>
                        <p class="text-3xl font-black text-slate-800 mt-1"><?php echo $unique_admins; ?></p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-emerald-600 text-lg"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-3">Ont effectué des actions</p>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Actions (7 jours)</p>
                        <p class="text-3xl font-black text-slate-800 mt-1"><?php echo number_format($week_count); ?></p>
                    </div>
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-amber-600 text-lg"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-3">Tendance hebdomadaire</p>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Types d'actions</p>
                        <p class="text-3xl font-black text-slate-800 mt-1"><?php echo count($action_types); ?></p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tag text-purple-600 text-lg"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-3">Catégories différentes</p>
            </div>
        </div>
    </div>

    <!-- Filtres améliorés -->
    <div class="flex flex-wrap gap-3 mb-8 animate-fade-up" style="animation-delay: 0.1s">
        <button data-filter="all" class="filter-btn px-5 py-2.5 rounded-full text-[11px] font-black uppercase transition-all bg-slate-900 text-white shadow-md">
            <i class="fas fa-list-ul mr-2"></i>Tous les évènements
        </button>
        <button data-filter="VALIDATION_PAIEMENT" class="filter-btn px-5 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-full text-[11px] font-black uppercase hover:border-emerald-400 transition-all">
            <i class="fas fa-credit-card mr-2 text-emerald-500"></i>Paiements
        </button>
        <button data-filter="LOGIN" class="filter-btn px-5 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-full text-[11px] font-black uppercase hover:border-blue-400 transition-all">
            <i class="fas fa-sign-in-alt mr-2 text-blue-500"></i>Connexions
        </button>
        <button data-filter="LOGOUT" class="filter-btn px-5 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-full text-[11px] font-black uppercase hover:border-blue-400 transition-all">
            <i class="fas fa-sign-out-alt mr-2 text-orange-500"></i>Déconnexions
        </button>
        <button data-filter="CREATE" class="filter-btn px-5 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-full text-[11px] font-black uppercase hover:border-emerald-400 transition-all">
            <i class="fas fa-plus-circle mr-2 text-emerald-500"></i>Créations
        </button>
        <button data-filter="UPDATE" class="filter-btn px-5 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-full text-[11px] font-black uppercase hover:border-amber-400 transition-all">
            <i class="fas fa-edit mr-2 text-amber-500"></i>Modifications
        </button>
        <button data-filter="DELETE" class="filter-btn px-5 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-full text-[11px] font-black uppercase hover:border-red-400 transition-all">
            <i class="fas fa-trash-alt mr-2 text-red-500"></i>Suppressions
        </button>
    </div>

    <!-- Tableau des logs -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xl animate-fade-up" style="animation-delay: 0.2s">
        <div class="px-8 py-5 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-wrap justify-between items-center gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-800 flex items-center gap-2">
                    <i class="fas fa-history text-slate-400"></i>
                    Flux d'audit en temps réel
                </h2>
                <p class="text-xs text-slate-500 mt-1">Journal détaillé des actions administrateurs</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <i class="fas fa-database"></i>
                    <span><?php echo $total_actions; ?> enregistrements</span>
                </div>
                <button onclick="exportLogsToCSV()" class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-bold hover:bg-emerald-100 transition-all">
                    <i class="fas fa-download mr-2"></i>Exporter CSV
                </button>
                <button onclick="window.location.reload()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all">
                    <i class="fas fa-sync-alt mr-2"></i>Rafraîchir
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto custom-scroll">
            <table class="w-full text-left border-collapse" id="logsTable">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase font-black tracking-wider border-b-2 border-slate-200">
                        <th class="px-8 py-4 cursor-pointer hover:text-slate-800 transition-colors" onclick="sortTable(0)">
                            <i class="fas fa-clock mr-1"></i>Horodatage
                        </th>
                        <th class="px-8 py-4 cursor-pointer hover:text-slate-800 transition-colors" onclick="sortTable(1)">
                            <i class="fas fa-user-shield mr-1"></i>Opérateur
                        </th>
                        <th class="px-8 py-4 cursor-pointer hover:text-slate-800 transition-colors" onclick="sortTable(2)">
                            <i class="fas fa-bolt mr-1"></i>Action
                        </th>
                        <th class="px-8 py-4">
                            <i class="fas fa-info-circle mr-1"></i>Détails de l'opération
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="logsTableBody">
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="4" class="px-8 py-16 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-inbox text-3xl text-slate-400"></i>
                                </div>
                                <div class="text-sm font-black text-slate-400 uppercase tracking-widest">
                                    Aucune donnée d'audit
                                </div>
                                <p class="text-xs text-slate-400">Les actions des administrateurs apparaîtront ici</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($logs as $index => $log): ?>
                    <tr class="log-row transition-all duration-300" data-action="<?php echo htmlspecialchars($log['action']); ?>" style="animation: slideIn 0.4s ease-out <?php echo $index * 0.02; ?>s both">
                        <td class="px-8 py-5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 text-center">
                                    <i class="far fa-calendar-alt text-slate-300 text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-black text-slate-800 font-mono"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></div>
                                    <div class="text-[10px] text-slate-400 font-medium"><?php echo date('d M Y', strtotime($log['created_at'])); ?></div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-sm font-black text-slate-700 shadow-inner">
                                    <?php 
                                        $initial = strtoupper(substr($log['username'] ?? 'S', 0, 1));
                                        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                                        $color = $colors[ord($initial) % count($colors)];
                                    ?>
                                    <span style="color: <?php echo $color; ?>"><?php echo $initial; ?></span>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($log['username'] ?? 'Système Automatique'); ?></div>
                                    <span class="status-pill <?php echo ($log['role'] == 'super_admin') ? 'bg-blue-100 text-blue-700' : (($log['role'] == 'moderateur') ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600'); ?>">
                                        <?php 
                                            $role_labels = [
                                                'super_admin' => '👑 Super Admin',
                                                'moderateur' => '🛡️ Modérateur',
                                                'editeur' => '✏️ Éditeur'
                                            ];
                                            echo $role_labels[$log['role']] ?? '🤖 Bot Système';
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-8 py-5">
                            <?php
                                $action_badges = [
                                    'VALIDATION_PAIEMENT' => ['bg-emerald-100 text-emerald-700', 'fa-credit-card', 'Paiement validé'],
                                    'LOGIN' => ['bg-blue-100 text-blue-700', 'fa-sign-in-alt', 'Connexion'],
                                    'LOGOUT' => ['bg-orange-100 text-orange-700', 'fa-sign-out-alt', 'Déconnexion'],
                                    'CREATE' => ['bg-teal-100 text-teal-700', 'fa-plus-circle', 'Création'],
                                    'UPDATE' => ['bg-amber-100 text-amber-700', 'fa-edit', 'Modification'],
                                    'DELETE' => ['bg-red-100 text-red-700', 'fa-trash-alt', 'Suppression'],
                                ];
                                $badge = $action_badges[$log['action']] ?? ['bg-slate-100 text-slate-700', 'fa-clock', $log['action']];
                            ?>
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[10px] font-black uppercase <?php echo $badge[0]; ?>">
                                <i class="fas <?php echo $badge[1]; ?> text-xs"></i>
                                <?php echo $badge[2]; ?>
                            </span>
                        </td>
                        
                        <td class="px-8 py-5">
                            <div class="max-w-md">
                                <div class="text-sm text-slate-600 leading-relaxed">
                                    <?php 
                                        $details = htmlspecialchars($log['details']);
                                        // Mettre en évidence les chiffres
                                        $details = preg_replace('/(\d+[\s]*FCFA|\d+)/', '<span class="font-bold text-emerald-600">$1</span>', $details);
                                        echo $details;
                                    ?>
                                </div>
                                <?php
                                    $entityLinks = [
                                        'property' => 'manage_properties.php',
                                        'owner'    => 'manage_owners.php',
                                        'request'  => 'manage_requests.php',
                                    ];
                                ?>
                                <?php if (!empty($log['entity_id']) && isset($entityLinks[$log['entity_type']])): ?>
                                    <div class="mt-2">
                                        <a href="<?php echo $entityLinks[$log['entity_type']]; ?>" class="inline-flex items-center gap-1 text-[10px] font-black text-blue-600 hover:text-blue-800 transition-colors">
                                            <i class="fas fa-external-link-alt text-[8px]"></i>
                                            Voir <?php echo htmlspecialchars($log['entity_type']); ?> #<?php echo (int)$log['entity_id']; ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pied de tableau -->
        <?php if (!empty($logs)): ?>
        <div class="px-8 py-4 border-t border-slate-200 bg-slate-50/30 flex flex-wrap justify-between items-center gap-4 text-xs">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-2">
                    <i class="fas fa-chart-simple text-slate-400"></i>
                    <span class="font-bold text-slate-600">Affichage: <span id="visibleCount"><?php echo count($logs); ?></span> / <?php echo $total_actions; ?></span>
                </span>
                <span class="flex items-center gap-2">
                    <i class="fas fa-calendar-week text-slate-400"></i>
                    <span class="text-slate-500">Dernière mise à jour: <?php echo date('d/m/Y H:i:s'); ?></span>
                </span>
            </div>
            <div class="text-slate-400">
                <i class="fas fa-shield-alt mr-1"></i>
                Journal de sécurité infalsifiable
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Filtrage des logs par action
document.querySelectorAll('[data-filter]').forEach(button => {
    button.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        
        // Mise à jour du style des boutons
        document.querySelectorAll('[data-filter]').forEach(btn => {
            btn.classList.remove('bg-slate-900', 'text-white', 'shadow-md');
            btn.classList.add('bg-white', 'text-slate-600');
        });
        if (filter !== 'all') {
            this.classList.remove('bg-white', 'text-slate-600');
            this.classList.add('bg-slate-900', 'text-white', 'shadow-md');
        } else {
            this.classList.remove('bg-white', 'text-slate-600');
            this.classList.add('bg-slate-900', 'text-white', 'shadow-md');
        }
        
        // Filtrage des lignes
        const rows = document.querySelectorAll('#logsTableBody tr');
        let visible = 0;
        
        rows.forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
                visible++;
            } else {
                const action = row.getAttribute('data-action');
                if (action === filter) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        // Mise à jour du compteur
        document.getElementById('visibleCount').innerText = visible;
    });
});

// Tri du tableau
let sortDirection = {0: 'desc', 1: 'desc', 2: 'desc'};

function sortTable(columnIndex) {
    const table = document.getElementById('logsTable');
    const tbody = document.getElementById('logsTableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Inverser la direction
    sortDirection[columnIndex] = sortDirection[columnIndex] === 'asc' ? 'desc' : 'asc';
    const direction = sortDirection[columnIndex];
    
    rows.sort((a, b) => {
        let aValue, bValue;
        
        switch(columnIndex) {
            case 0: // Date
                const aDate = a.querySelector('td:first-child .text-sm')?.innerText || '';
                const bDate = b.querySelector('td:first-child .text-sm')?.innerText || '';
                aValue = aDate;
                bValue = bDate;
                break;
            case 1: // Opérateur
                aValue = a.querySelector('td:nth-child(2) .text-sm')?.innerText || '';
                bValue = b.querySelector('td:nth-child(2) .text-sm')?.innerText || '';
                break;
            case 2: // Action
                aValue = a.querySelector('td:nth-child(3) span')?.innerText || '';
                bValue = b.querySelector('td:nth-child(3) span')?.innerText || '';
                break;
            default:
                return 0;
        }
        
        if (direction === 'asc') {
            return aValue.localeCompare(bValue);
        } else {
            return bValue.localeCompare(aValue);
        }
    });
    
    // Réorganiser le tableau
    rows.forEach(row => tbody.appendChild(row));
}

// Export CSV
function exportLogsToCSV() {
    const rows = document.querySelectorAll('#logsTableBody tr');
    let csvContent = "Horodatage,Opérateur,Rôle,Action,Détails,Réservation\n";
    
    rows.forEach(row => {
        if(row.style.display !== 'none') {
            const date = row.querySelector('td:first-child .text-sm')?.innerText || '';
            const dateFull = row.querySelector('td:first-child .text-\\[10px\\]')?.innerText || '';
            const operator = row.querySelector('td:nth-child(2) .text-sm')?.innerText || '';
            const role = row.querySelector('td:nth-child(2) .status-pill')?.innerText || '';
            const action = row.querySelector('td:nth-child(3) span')?.innerText || '';
            const details = row.querySelector('td:nth-child(4) .text-sm')?.innerText || '';
            const reservation = row.querySelector('td:nth-child(4) a')?.innerText?.match(/#(\d+)/)?.[1] || '';
            
            csvContent += `"${dateFull} ${date}","${operator}","${role}","${action}","${details}","${reservation}"\n`;
        }
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `audit_logs_${new Date().toISOString().slice(0,19)}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Actualisation automatique toutes les 30 secondes (optionnelle)
let autoRefresh = true;
setInterval(() => {
    if(autoRefresh && !document.hidden) {
        window.location.reload();
    }
}, 30000);
</script>

<?php require_once 'includes_admin/footer_admin.php'; ?>