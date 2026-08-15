<?php
require_once 'mfbase/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
$stmt->execute([$id]);
$req = $stmt->fetch();

if (!$req) {
    header('Location: index.php');
    exit;
}

$moduleLabels = [
    'location'        => 'Location de logement',
    'achat_vente'      => 'Achat / Vente',
    'meuble'           => 'Logement meublé',
    'service_menager'  => 'Service ménager',
    'objet_perdu'      => 'Objet perdu / retrouvé',
];

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="bg-slate-50 min-h-screen pt-16 pb-24">
    <div class="max-w-2xl mx-auto px-6 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-tenezer-gold text-tenezer-dark rounded-full mb-6 shadow-2xl">
            <i class="fas fa-check text-3xl"></i>
        </div>
        <h1 class="font-serif text-3xl md:text-5xl font-black text-tenezer-dark mb-4">Demande envoyée</h1>
        <p class="text-gray-400 font-black uppercase text-[10px] tracking-[0.4em] mb-10">Référence #TNZ-<?php echo str_pad((string)$req['id'], 5, '0', STR_PAD_LEFT); ?></p>

        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm text-left space-y-4 mb-10">
            <div class="flex justify-between border-b border-gray-50 pb-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Module</span>
                <span class="font-bold text-tenezer-dark"><?php echo htmlspecialchars($moduleLabels[$req['module']] ?? $req['module']); ?></span>
            </div>
            <div class="flex justify-between border-b border-gray-50 pb-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nom</span>
                <span class="font-bold text-tenezer-dark"><?php echo htmlspecialchars(trim($req['first_name'] . ' ' . ($req['last_name'] ?? ''))); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Téléphone</span>
                <span class="font-bold text-tenezer-dark"><?php echo htmlspecialchars($req['phone']); ?></span>
            </div>
        </div>

        <p class="text-sm text-gray-500 mb-8">Un conseiller Tenezer va traiter votre demande et vous recontacter très prochainement. Pour aller plus vite, écrivez-nous directement sur WhatsApp en mentionnant votre référence.</p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo htmlspecialchars(tenezer_whatsapp_url('237672163188', 'Bonjour Tenezer, je fais suite à ma demande #TNZ-' . str_pad((string)$req['id'], 5, '0', STR_PAD_LEFT))); ?>" target="_blank" class="px-8 py-4 bg-tenezer-gold text-tenezer-dark rounded-full font-black text-xs uppercase tracking-widest hover:brightness-110 transition-all">
                <i class="fab fa-whatsapp mr-2"></i>Suivre sur WhatsApp
            </a>
            <a href="index.php" class="px-8 py-4 border border-gray-200 text-tenezer-dark rounded-full font-black text-xs uppercase tracking-widest hover:border-tenezer-gold transition-all">
                Retour à l'accueil
            </a>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
