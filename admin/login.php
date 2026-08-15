<?php
session_start();
require_once '../mfbase/database.php';

// Redirection si déjà connecté
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

$error = '';
// Extrait de la logique de connexion mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 1. Vérification en Base de données
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND is_active = 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role']; // Stockage du rôle
        header('Location: index.php');
        exit();
    } else {
        $error = 'Identifiants incorrects';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Tenezer Immobilier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body {
            background: linear-gradient(135deg, #0b0b0b 0%, #1a1a1a 100%);
            min-height: 100vh;
        }
        .login-card {
            animation: fadeInUp 0.6s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .input-group {
            transition: all 0.3s ease;
        }
        .input-group:focus-within {
            transform: translateX(5px);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="login-card bg-white rounded-2xl shadow-2xl overflow-hidden max-w-md w-full">
        <div class="bg-gradient-to-r from-[#0b0b0b] to-[#2a2a2a] p-8 text-center">
            <div class="inline-block p-4 bg-[#F1A80A]/20 rounded-full mb-4">
                <i class="fas fa-key text-4xl text-[#F1A80A]"></i>
            </div>
            <h2 class="text-3xl font-bold text-white">Administration</h2>
            <p class="text-white/80 mt-2">Tenezer Immobilier</p>
        </div>
        
        <div class="p-8">
            <?php if ($error): ?>
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg animate-shake">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-6">
                <div class="input-group">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-user mr-2 text-[#b8860b]"></i>Nom d'utilisateur
                    </label>
                    <input type="text" name="username" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#F1A80A] transition-all"
                           placeholder="admin">
                </div>

                <div class="input-group">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-lock mr-2 text-[#b8860b]"></i>Mot de passe
                    </label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#F1A80A] transition-all"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-[#0b0b0b] to-[#2a2a2a] text-[#F1A80A] py-3 rounded-lg font-semibold hover:shadow-lg transition-all transform hover:scale-105">
                    <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                </button>
            </form>
            
            <div class="mt-6 text-center text-sm text-gray-500">
                <i class="fas fa-shield-alt mr-1"></i>Accès sécurisé
            </div>
        </div>
    </div>
</body>
</html>