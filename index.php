<?php
$anggota = [
    ["name" => "Yusuf Herlian Ananta Ril", "npm" => "2317051083", "image" => "lian.jpg"],
    ["name" => "Lutfi Harya Ferdian", "npm" => "2317051096", "image" => "🔥"],
    ["name" => "Muhammad Randi Putra Kurniawan", "npm" => "2317051009", "image" => "randi.png"],
    ["name" => "Indriazan Alkautsar", "npm" => "2317051074", "image" => "⭐"],
    ["name" => "Muhammad Ilham Bintang Faiz Efendi", "npm" => "2357051023", "image" => "⭐"],
    ["name" => "Faras Raditia Maulana", "npm" => "2357051020", "image" => "⭐"],
    ["name" => "Muhammad Zidan Rosyid", "npm" => "2317051044", "image" => "⭐"],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAP SIG</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background-color: #0f1113;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(108, 199, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 133, 95, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 0%, rgba(108, 199, 255, 0.03) 0%, transparent 70%);
            position: relative;
            overflow-x: hidden;
        }

        .star-field {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            pointer-events: none;
        }

        .star {
            position: absolute;
            width: 1px;
            height: 1px;
            background: white;
            border-radius: 50%;
            opacity: 0.3;
            animation: twinkle 4s infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.2;
            }

            50% {
                opacity: 0.6;
            }
        }

        /* Glass morphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.02),
                0 12px 30px rgba(2, 6, 23, 0.6);
        }

        /* Glowing pill buttons */
        .btn-glow {
            background: linear-gradient(135deg, #6cc7ff 0%, #60b3f5 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 24px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(108, 199, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(108, 199, 255, 0.5);
        }

        .btn-glow:active {
            transform: translateY(0);
        }

        /* Ghost button */
        .btn-ghost {
            background: transparent;
            color: #6cc7ff;
            border: 1.5px solid rgba(108, 199, 255, 0.4);
            padding: 12px 28px;
            border-radius: 24px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-ghost:hover {
            background: rgba(108, 199, 255, 0.1);
            border-color: rgba(108, 199, 255, 0.8);
            box-shadow: 0 8px 25px rgba(108, 199, 255, 0.15);
        }

        /* Floating animation */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .float {
            animation: float 4s ease-in-out infinite;
        }

        .float-delay-1 {
            animation-delay: 0s;
        }

        .float-delay-2 {
            animation-delay: 0.5s;
        }

        .float-delay-3 {
            animation-delay: 1s;
        }

        /* Fade up animation */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp 0.8s ease-out forwards;
        }

        .fade-up-delay-1 {
            animation-delay: 0.1s;
        }

        .fade-up-delay-2 {
            animation-delay: 0.2s;
        }

        .fade-up-delay-3 {
            animation-delay: 0.3s;
        }

        .fade-up-delay-4 {
            animation-delay: 0.4s;
        }

        /* Feature card */
        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 32px 28px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(108, 199, 255, 0.3);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(108, 199, 255, 0.1);
        }

        .feature-icon {
            font-size: 40px;
            margin-bottom: 16px;
        }

        .feature-title {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .feature-desc {
            color: #9aa0a6;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Mockup card container */
        .mockup-container {
            perspective: 1000px;
        }

        .mockup-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 24px;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.02),
                0 12px 30px rgba(2, 6, 23, 0.6),
                0 8px 25px rgba(108, 199, 255, 0.15);
            backdrop-filter: blur(10px);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 12px 20px;
            border-radius: 16px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-item {
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
        }

        .sidebar-item {
            width: 100%;
            height: 32px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .pill-badge {
            display: inline-block;
            background: #6cc7ff;
            color: #0f1113;
            padding: 6px 14px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
        }

        .text-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #9aa0a6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>

<body>
    <!-- Star field background -->
    <div class="star-field" id="starField"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 py-6 px-6 md:px-12">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">

                <h1 class="text-2xl font-bold mb-1 text-white"><i class="fas fa-map-marked-alt mr-2"></i>UAP Sistem Informasi Geografis</h1>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="min-h-screen flex flex-col items-center justify-center px-6 pt-60">
        <div class="max-w-6xl w-full">
            <!-- Hero Text -->
            <div class="text-center mb-16 fade-up fade-up-delay-1">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    Ujian Akhir Praktikum <br class="hidden md:block" /> <span class="text-gradient">Sistem Informasi Geografi</span>
                </h1>

            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-20 fade-up fade-up-delay-2">
                <a href="/src/map.php" class="btn-glow">
                    Mulai Sekarang
                </a>
                <a href="#anggota" class="btn-ghost">
                    Anggota Kami →
                </a>
            </div>




        </div>
    </section>

    <!-- Features Section -->
    <section id="anggota" class="py-20 px-6 md:px-12 bg-gradient-to-b from-transparent via-blue-500/5 to-transparent">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-white text-center mb-16">
                Anggota Kelompok
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($anggota as $i => $p): ?>
                    <div class="feature-card fade-up fade-up-delay-<?= $i + 1 ?> flex flex-col items-center text-center">
                        <div class="feature-icon w-28 h-28 rounded-full overflow-hidden flex items-center justify-center">
                            <img src="assets/images/<?= $p['image'] ?>" alt="<?= $p['name'] ?>" class="w-full h-full object-cover">
                        </div>

                        <div class="feature-title mt-2"><?= $p["name"] ?></div>
                        <div class="feature-desc text-gray-500"><?= $p["npm"] ?></div>
                    </div>

                <?php endforeach; ?>


            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-12 px-6 md:px-12">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-gray-400 mb-6">
                Trusted by over 50,000 designers and developers
            </p>
            <div class="flex justify-center gap-4 mb-8">
                <img src="/placeholder.svg?height=40&width=40" alt="User 1" class="w-10 h-10 rounded-full border border-white/10">
                <img src="/placeholder.svg?height=40&width=40" alt="User 2" class="w-10 h-10 rounded-full border border-white/10">
                <img src="/placeholder.svg?height=40&width=40" alt="User 3" class="w-10 h-10 rounded-full border border-white/10">
                <img src="/placeholder.svg?height=40&width=40" alt="User 4" class="w-10 h-10 rounded-full border border-white/10">
                <img src="/placeholder.svg?height=40&width=40" alt="User 5" class="w-10 h-10 rounded-full border border-white/10">
            </div>
            <p class="text-gray-500 text-sm">
                © 2025 SIG. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Generate random stars
        function generateStars() {
            const starField = document.getElementById('starField');
            const starCount = Math.min(50, window.innerWidth / 10);

            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.animationDelay = Math.random() * 4 + 's';
                starField.appendChild(star);
            }
        }

        generateStars();

        // Smooth scroll for buttons
        document.querySelectorAll('.btn-glow, .btn-ghost').forEach(button => {
            button.addEventListener('click', function() {
                if (this.textContent.includes('Get started')) {
                    window.location.href = '#get-started';
                }
            });
        });
    </script>
</body>

</html>