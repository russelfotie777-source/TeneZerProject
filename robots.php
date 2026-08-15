<?php
require_once 'mfbase/database.php';
require_once 'includes/functions.php';

header('Content-Type: text/plain; charset=utf-8');

$base = tenezer_base_url() . tenezer_site_root_path();
?>
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /espace-proprietaire/dashboard.php
Disallow: /espace-proprietaire/bien-form.php
Disallow: /process_request.php
Disallow: /track_whatsapp.php
Disallow: /mfbase/

Sitemap: <?php echo $base; ?>/sitemap.php
