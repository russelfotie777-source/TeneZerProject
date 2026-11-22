<?php
// Inclus par detail.php — attend $property, $pdo déjà disponibles.
if (isset($property) && isset($pdo)) {

    $current_id = (int)$property['id'];

    $stmt_sim = $pdo->prepare("
        SELECT * FROM properties
        WHERE category = :category
        AND id != :current_id
        AND status = 'active'
        ORDER BY RAND()
        LIMIT 4
    ");
    $stmt_sim->execute(['category' => $property['category'], 'current_id' => $current_id]);
    $similar_properties = $stmt_sim->fetchAll();

    if (count($similar_properties) > 0) :
?>

<div class="mt-16 sm:mt-24 pt-10 border-t border-gray-100 max-w-7xl mx-auto px-4 sm:px-6 pb-10">
    <div class="mb-8">
        <h3 class="font-sans text-xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight">Vous aimerez aussi</h3>
        <p class="text-xs sm:text-sm text-tenezer-gold font-bold tracking-widest uppercase mt-1">Biens similaires</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($similar_properties as $sim): $sim_photo = tenezer_main_photo($pdo, $sim['id']); ?>
            <a href="detail.php?id=<?php echo (int)$sim['id']; ?>" class="group block rounded-3xl overflow-hidden bg-white border border-slate-100 shadow-sm hover:shadow-2xl transition-all">
                <div class="h-48 overflow-hidden bg-slate-50 flex items-center justify-center">
                    <?php if ($sim_photo): ?>
                        <img src="<?php echo htmlspecialchars($sim_photo); ?>" alt="<?php echo htmlspecialchars($sim['title']); ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                        <i class="fas fa-image text-3xl text-gray-300"></i>
                    <?php endif; ?>
                </div>
                <div class="p-5">
                    <span class="text-[9px] font-black uppercase tracking-widest text-tenezer-gold"><?php echo htmlspecialchars($sim['category'] === 'meuble' ? tenezer_meuble_label($sim['type']) : tenezer_type_label($sim['type'])); ?></span>
                    <h4 class="font-black text-slate-900 text-sm mt-1 mb-2 truncate"><?php echo htmlspecialchars($sim['title']); ?></h4>
                    <p class="font-black text-tenezer-dark text-sm"><?php echo htmlspecialchars(tenezer_price_label($sim)); ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php
    endif;
}
?>
