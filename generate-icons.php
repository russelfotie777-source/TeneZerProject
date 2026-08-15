<?php
header('Content-Type: text/html; charset=utf-8');
echo '<h1>Génération des icônes PWA</h1>';

$source = 'includes/logo.jpeg';
$sizes = [72, 96, 128, 144, 152, 192, 384, 512];

if (!file_exists($source)) {
    die('❌ Erreur: logo.jpeg non trouvé dans le dossier includes/');
}

foreach ($sizes as $size) {
    $img = imagecreatefromjpeg($source);
    $newImg = imagecreatetruecolor($size, $size);
    
    // Fond blanc
    $white = imagecolorallocate($newImg, 255, 255, 255);
    imagefill($newImg, 0, 0, $white);
    
    // Redimensionnement
    imagecopyresampled($newImg, $img, 0, 0, 0, 0, $size, $size, imagesx($img), imagesy($img));
    
    // Sauvegarde
    $output = "includes/icon-{$size}.png";
    imagepng($newImg, $output);
    imagedestroy($newImg);
    imagedestroy($img);
    
    echo "✅ Généré: <strong>{$output}</strong> ({$size}x{$size})<br>";
}
echo '<hr><h2>Terminé ! Vous pouvez maintenant installer la PWA.</h2>';
echo '<a href="index.php">Retour au site</a>';
?>