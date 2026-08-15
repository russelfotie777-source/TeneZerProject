<?php
require_once 'mfbase/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$base = tenezer_base_url() . tenezer_site_root_path();

$staticPages = [
    ['loc' => 'index.php', 'priority' => '1.0'],
    ['loc' => 'biens.php?category=logement', 'priority' => '0.9'],
    ['loc' => 'biens.php?category=terrain', 'priority' => '0.9'],
    ['loc' => 'achat-vente.php', 'priority' => '0.7'],
    ['loc' => 'meubles.php', 'priority' => '0.7'],
    ['loc' => 'services-menagers.php', 'priority' => '0.7'],
    ['loc' => 'objets.php', 'priority' => '0.6'],
    ['loc' => 'contact.php', 'priority' => '0.5'],
    ['loc' => 'espace-proprietaire/inscription.php', 'priority' => '0.6'],
];

$properties = [];
try {
    $properties = $pdo->query("SELECT id, created_at FROM properties WHERE status = 'active'")->fetchAll();
} catch (PDOException $e) {
    $properties = [];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $page): ?>
    <url>
        <loc><?php echo htmlspecialchars($base . '/' . $page['loc']); ?></loc>
        <priority><?php echo $page['priority']; ?></priority>
    </url>
<?php endforeach; ?>
<?php foreach ($properties as $p): ?>
    <url>
        <loc><?php echo htmlspecialchars($base . '/detail.php?id=' . $p['id']); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($p['created_at'])); ?></lastmod>
        <priority>0.8</priority>
    </url>
<?php endforeach; ?>
</urlset>
