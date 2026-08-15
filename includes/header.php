<?php
require_once __DIR__ . '/functions.php';
$page_title = $page_title ?? "Tenezer Immobilier | Le Carrefour de l'Immobilier";
$og_description = $og_description ?? "Location de logements, terrains, achat/vente, logements meublés, services ménagers et objets perdus — Tenezer, le carrefour de l'immobilier au Cameroun.";
$og_image = $og_image ?? tenezer_absolute_url('includes/icon-512.png');
$og_url = $og_url ?? tenezer_base_url() . ($_SERVER['REQUEST_URI'] ?? '/');
?>
<!DOCTYPE html>
<html lang="fr">


<!-- Optimisation images & styles -->
<style>
    /* Reset + optimisation */
    img {
        max-width: 100%;
        height: auto;
        display: block;
    }
    
    /* Fallback pour images qui ne chargent pas */
    img:before {
        content: "🖼️";
        display: block;
        background: #f0f0f0;
        text-align: center;
        line-height: 200px;
    }
    
    /* Améliorer les performances des animations */
    .swiper-slide {
        will-change: transform;
    }
</style>


<!-- Meta Pixel Code --> 
 <script> !function(f,b,e,v,n,t,s) {if(f.fbq)return;n=f.fbq=function(){n.callMethod? n.callMethod.apply(n,arguments):n.queue.push(arguments)}; if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0'; n.queue=[];t=b.createElement(e);t.async=!0; t.src=v;s=b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s)}(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js'); fbq('init', '1705953560413103'); // ID CORRIGÉ ICI fbq('track', 'PageView'); </script> <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1705953560413103&ev=PageView&noscript=1" /></noscript> <!-- End Meta Pixel Code -->






<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($og_description); ?>">

    <!-- Open Graph / WhatsApp / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Tenezer Immobilier">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($og_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($og_url); ?>">
    <meta property="og:locale" content="fr_CM">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($og_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image); ?>">

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0B0B0B">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Tenezer">
    <link rel="apple-touch-icon" href="includes/icon-192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="includes/icon-192.png">
    <link rel="icon" type="image/png" sizes="72x72" href="includes/icon-72.png">



    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fslightbox/3.4.1/index.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* PWA Banner Styles */
.pwa-banner-install {
    position: fixed;
    bottom: 20px;
    left: 20px;
    right: 20px;
    z-index: 999999;
}

.pwa-banner-card {
    max-width: 500px;
    margin: 0 auto;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 28px;
    padding: 24px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(35,138,70,0.3);
    position: relative;
}

.pwa-banner-icon-wrapper {
    text-align: center;
    margin-bottom: 16px;
    position: relative;
}

.pwa-banner-icon {
    width: 64px;
    height: 64px;
    border-radius: 18px;
    object-fit: cover;
}

.pwa-banner-icon-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 64px;
    height: 64px;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, rgba(35,138,70,0.4), transparent);
    border-radius: 18px;
    animation: pulseRing 1.5s infinite;
}

@keyframes pulseRing {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; }
    100% { transform: translate(-50%, -50%) scale(1.4); opacity: 0; }
}

.pwa-banner-title {
    text-align: center;
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 8px;
    color: white;
}

.pwa-banner-title span {
    background: linear-gradient(135deg, #238A46, #F1A80A);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.pwa-banner-description {
    text-align: center;
    color: #a0a0a0;
    font-size: 13px;
    margin-bottom: 20px;
    line-height: 1.5;
}

.pwa-install-steps {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.pwa-step {
    flex: 1;
    text-align: center;
    background: rgba(255,255,255,0.05);
    padding: 12px 8px;
    border-radius: 16px;
}

.pwa-step-number {
    width: 28px;
    height: 28px;
    background: #238A46;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-weight: bold;
    font-size: 14px;
    color: white;
}

.pwa-step-text {
    font-size: 11px;
    color: white;
}

.pwa-step-text strong {
    color: #F1A80A;
}

.pwa-step-arrow {
    color: #238A46;
    font-size: 20px;
    font-weight: bold;
}

.pwa-banner-buttons {
    display: flex;
    gap: 12px;
}

.pwa-banner-btn {
    flex: 1;
    padding: 14px;
    border-radius: 40px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    border: none;
    transition: all 0.3s;
}

.pwa-banner-btn-primary {
    background: linear-gradient(135deg, #238A46, #1a6b36);
    color: white;
}

.pwa-banner-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(35,138,70,0.4);
}

.pwa-banner-btn-secondary {
    background: rgba(255,255,255,0.1);
    color: white;
}

.pwa-banner-btn-secondary:hover {
    background: rgba(255,255,255,0.15);
}

.pwa-banner-note {
    text-align: center;
    font-size: 10px;
    color: #666;
    margin-top: 16px;
}

.pwa-banner-close {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(255,255,255,0.1);
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    cursor: pointer;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.pwa-banner-close:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.1);
}

/* Modal */
.pwa-install-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.pwa-install-modal.visible {
    opacity: 1;
    visibility: visible;
}

.pwa-install-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(8px);
}

.pwa-install-modal-content {
    position: relative;
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 32px;
    max-width: 420px;
    width: 90%;
    max-height: 85vh;
    overflow-y: auto;
    z-index: 1;
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        transform: scale(0.9) translateY(20px);
        opacity: 0;
    }
    to {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
}

.pwa-install-modal-header {
    padding: 24px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    position: relative;
}

.pwa-install-modal-icon img {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    margin-bottom: 12px;
}

.pwa-install-modal-header h3 {
    color: white;
    font-size: 20px;
    margin: 0;
}

.pwa-install-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    background: none;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    opacity: 0.7;
}

.pwa-install-modal-close:hover {
    opacity: 1;
}

.pwa-install-modal-body {
    padding: 24px;
}

.pwa-install-modal-body p {
    color: #a0a0a0;
    margin-bottom: 20px;
    font-size: 14px;
}

.pwa-modal-step {
    display: flex;
    gap: 14px;
    margin-bottom: 24px;
}

.pwa-modal-step-icon {
    width: 36px;
    height: 36px;
    background: #238A46;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    flex-shrink: 0;
    color: white;
}

.pwa-modal-step-content {
    flex: 1;
}

.pwa-modal-step-content strong {
    display: block;
    color: white;
    margin-bottom: 6px;
    font-size: 14px;
}

.pwa-modal-step-content span {
    font-size: 13px;
    color: #a0a0a0;
    line-height: 1.4;
}

.pwa-install-modal-tip {
    background: rgba(35,138,70,0.15);
    padding: 14px;
    border-radius: 16px;
    margin-top: 20px;
    font-size: 13px;
    color: #c0c0c0;
    border-left: 3px solid #238A46;
}

.pwa-install-modal-footer {
    padding: 20px 24px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.pwa-modal-btn-primary {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #238A46, #1a6b36);
    border: none;
    border-radius: 40px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.pwa-modal-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(35,138,70,0.3);
}

@media (max-width: 640px) {
    .pwa-step-arrow {
        display: none;
    }
    
    .pwa-install-steps {
        flex-direction: column;
    }
    
    .pwa-step {
        width: 100%;
    }
    
    .pwa-banner-card {
        padding: 20px;
    }
    
    .pwa-banner-title {
        font-size: 20px;
    }
}
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                        serif: ['Playfair Display', 'serif']
                    },
                    colors: {
                        tenezer: {
                            dark: '#0B0B0B',    /* Noir Tenezer */
                            charcoal: '#1A1A1A',
                            gold: '#F1A80A'     /* Or Tenezer */
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Masquage strict de l'interface Google Translate par défaut */
        .goog-te-banner-frame.skiptranslate, .goog-logo-link, .goog-te-gadget span, .goog-te-combo { display: none !important; }
        body { top: 0px !important; }
        #google_translate_element { display: none; }
        .skiptranslate { display: none !important; }

        /* Effet de survol sur les liens de navigation basés sur le vert du logo */
        .nav-link { position: relative; }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #F1A80A;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after { width: 100%; }

        /* Animation Dropdown Langues */
        .lang-dropdown {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .lang-container:hover .lang-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>

    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'fr', 
                includedLanguages: 'fr,en,es,de,it,pt,ar,zh-CN',
                autoDisplay: false
            }, 'google_translate_element');
        }

        function changeLanguage(langCode, flagUrl, langName) {
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                select.value = langCode;
                select.dispatchEvent(new Event('change'));
                document.getElementById('current-flag').src = flagUrl;
                document.getElementById('current-lang-name').innerText = langName;
            }
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>
<body class="antialiased text-tenezer-dark bg-white">

<div id="google_translate_element"></div>