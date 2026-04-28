<?php require_once 'includes/header.php'; ?>

<style>
    /* --- CSS untuk Halaman Tentang Kami dengan Animasi --- */
    .about-page-container {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        line-height: 1.6;
        overflow-x: hidden;
    }

    /* --- Hero Section dengan Background Image --- */
    .about-page-container .about-hero {
        background: linear-gradient(135deg, rgba(255, 182, 193, 0.8), rgba(219, 112, 147, 0.8)), 
                    url('https://picsum.photos/seed/pink-flowers/1920/1080.jpg') no-repeat center center/cover;
        background-attachment: fixed;
        color: #fff;
        padding: 120px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
        margin-top: 80px;
    }

    .about-page-container .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://picsum.photos/seed/flower-pattern/200/200.jpg') repeat;
        opacity: 0.05;
        pointer-events: none;
    }

    .about-page-container .about-hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .about-page-container .about-hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .about-page-container .about-hero p {
        font-size: 1.2rem;
        margin-bottom: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .about-page-container .hero-decoration {
        display: flex;
        justify-content: center;
        margin: 30px 0;
    }

    .about-page-container .hero-decoration span {
        display: inline-block;
        width: 50px;
        height: 2px;
        background-color: #fff;
        margin: 0 10px;
        position: relative;
    }

    .about-page-container .hero-decoration span::before {
        content: '✿';
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        color: #fff;
        font-size: 18px;
    }

    /* --- Story Section --- */
    .about-page-container .story-section {
        background-color: #fff;
        padding: 80px 0;
        position: relative;
    }

    .about-page-container .story-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100px;
        background: linear-gradient(to bottom, #fff9fa, #fff);
        z-index: 1;
    }

    .about-page-container .story-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .about-page-container .story-image {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(219, 112, 147, 0.15);
    }

    .about-page-container .story-image img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.5s ease;
    }

    .about-page-container .story-image:hover img {
        transform: scale(1.03);
    }

    .about-page-container .story-image::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 182, 193, 0.2), rgba(219, 112, 147, 0.2));
    }

    .about-page-container .story-content h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        color: #d63384;
        margin-bottom: 20px;
        position: relative;
    }

    .about-page-container .story-content h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 80px;
        height: 3px;
        background-color: #d63384;
    }

    .about-page-container .story-content p {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 20px;
    }

    .about-page-container .story-content .quote {
        font-style: italic;
        padding: 20px;
        background-color: #fff9fa;
        border-left: 4px solid #d63384;
        margin: 30px 0;
        border-radius: 0 8px 8px 0;
    }

    /* --- Values Section --- */
    .about-page-container .values-section {
        background-color: #fff9fa;
        padding: 80px 0;
    }

    .about-page-container .section-header {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 60px;
        padding: 0 20px;
    }

    .about-page-container .section-header h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        color: #d63384;
        margin-bottom: 15px;
    }

    .about-page-container .section-header p {
        font-size: 1.1rem;
        color: #666;
    }

    .about-page-container .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .about-page-container .value-card {
        background-color: #fff;
        border-radius: 15px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 10px 20px rgba(219, 112, 147, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .about-page-container .value-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(to right, #ffb6c1, #db7093);
    }

    .about-page-container .value-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(219, 112, 147, 0.15);
    }

    .about-page-container .value-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #ffb6c1, #db7093);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
        color: #fff;
    }

    .about-page-container .value-card h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        color: #d63384;
        margin-bottom: 15px;
    }

    .about-page-container .value-card p {
        color: #666;
        line-height: 1.6;
    }

    /* --- Timeline Section --- */
    .about-page-container .timeline-section {
        background-color: #fff;
        padding: 80px 0;
        position: relative;
    }

    .about-page-container .timeline-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
    }

    .about-page-container .timeline-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 100%;
        background: linear-gradient(to bottom, #ffb6c1, #db7093);
    }

    .about-page-container .timeline-item {
        position: relative;
        margin-bottom: 50px;
    }

    .about-page-container .timeline-item:nth-child(odd) .timeline-content {
        margin-right: 50%;
        padding-right: 40px;
        text-align: right;
    }

    .about-page-container .timeline-item:nth-child(even) .timeline-content {
        margin-left: 50%;
        padding-left: 40px;
    }

    .about-page-container .timeline-dot {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 20px;
        height: 20px;
        background-color: #fff;
        border: 4px solid #db7093;
        border-radius: 50%;
        z-index: 2;
    }

    .about-page-container .timeline-content {
        background-color: #fff9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(219, 112, 147, 0.1);
    }

    .about-page-container .timeline-content h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        color: #d63384;
        margin-bottom: 10px;
    }

    .about-page-container .timeline-content p {
        color: #666;
    }

    /* --- Team Section --- */
    .about-page-container .team-section {
        background-color: #fff9fa;
        padding: 80px 0;
    }

    .about-page-container .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .about-page-container .team-member {
        background-color: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(219, 112, 147, 0.08);
        transition: transform 0.3s ease;
    }

    .about-page-container .team-member:hover {
        transform: translateY(-10px);
    }

    .about-page-container .member-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .about-page-container .member-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .about-page-container .team-member:hover .member-image img {
        transform: scale(1.05);
    }

    .about-page-container .member-image::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 50%;
        background: linear-gradient(to top, rgba(219, 112, 147, 0.8), transparent);
    }

    .about-page-container .member-info {
        padding: 25px;
        text-align: center;
    }

    .about-page-container .member-info h4 {
        font-family: 'Playfair Display', serif;
        font-size: 1.3rem;
        color: #d63384;
        margin-bottom: 5px;
    }

    .about-page-container .member-info p {
        color: #666;
        margin-bottom: 15px;
    }

    .about-page-container .member-social {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .about-page-container .member-social a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background-color: #fff9fa;
        border-radius: 50%;
        color: #d63384;
        transition: all 0.3s ease;
    }

    .about-page-container .member-social a:hover {
        background-color: #d63384;
        color: #fff;
    }

    /* --- CTA Section --- */
    .about-page-container .cta-section {
        background: linear-gradient(135deg, #ffb6c1, #db7093);
        padding: 80px 0;
        text-align: center;
        color: #fff;
    }

    .about-page-container .cta-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .about-page-container .cta-content h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-bottom: 20px;
    }

    .about-page-container .cta-content p {
        font-size: 1.2rem;
        margin-bottom: 30px;
    }

    .about-page-container .cta-button {
        display: inline-block;
        padding: 15px 35px;
        background-color: #fff;
        color: #d63384;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .about-page-container .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    /* --- ANIMASI FADE IN UP --- */
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

    .about-page-container .animate-on-load {
        opacity: 0;
        animation-name: fadeInUp;
        animation-duration: 0.8s;
        animation-fill-mode: forwards;
        animation-timing-function: ease-out;
    }

    .about-page-container .animate-on-load.delay-1 { animation-delay: 0.1s; }
    .about-page-container .animate-on-load.delay-2 { animation-delay: 0.2s; }
    .about-page-container .animate-on-load.delay-3 { animation-delay: 0.3s; }
    .about-page-container .animate-on-load.delay-4 { animation-delay: 0.4s; }
    .about-page-container .animate-on-load.delay-5 { animation-delay: 0.5s; }
    .about-page-container .animate-on-load.delay-6 { animation-delay: 0.6s; }
    .about-page-container .animate-on-load.delay-7 { animation-delay: 0.7s; }
    .about-page-container .animate-on-load.delay-8 { animation-delay: 0.8s; }
    .about-page-container .animate-on-load.delay-9 { animation-delay: 0.9s; }
    .about-page-container .animate-on-load.delay-10 { animation-delay: 1.0s; }

    /* --- Responsif untuk Halaman Tentang Kami --- */
    @media (max-width: 992px) {
        .about-page-container .story-container {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .about-page-container .timeline-container::before {
            left: 30px;
        }
        
        .about-page-container .timeline-item:nth-child(odd) .timeline-content,
        .about-page-container .timeline-item:nth-child(even) .timeline-content {
            margin-left: 70px;
            margin-right: 0;
            padding-left: 0;
            padding-right: 0;
            text-align: left;
        }
        
        .about-page-container .timeline-dot {
            left: 30px;
        }
    }

    @media (max-width: 768px) {
        .about-page-container .about-hero {
            padding: 100px 0;
            margin-top: 60px;
        }
        
        .about-page-container .values-grid,
        .about-page-container .team-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


<!-- Wrapper untuk mengisolasi konten -->
<main class="about-page-container">
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="about-hero-content">
            <h1 class="animate-on-load delay-1" >Tentang QUEEN.CRAFTID</h1>
            <div class="hero-decoration animate-on-load delay-2">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <p class="animate-on-load delay-3">Kami adalah tim yang bersemangat untuk menciptakan kebahagiaan dalam setiap karangan bunga.</p>
        </div>
    </section>

    <!-- Story Section -->
    <section id="cerita" class="story-section">
        <div class="story-container">
            <div class="story-image animate-on-load delay-4">
                <img src="https://picsum.photos/seed/flower-shop/600/400.jpg" alt="Toko Bunga Queencraft">
            </div>
            <div class="story-content">
                <h2 class="animate-on-load delay-5">Cerita Kami</h2>
                <p class="animate-on-load delay-6">QUEEN.CRAFTID dimulai dari sebuah kecintaan sederhana terhadap keindahan bunga dan keinginan untuk berbagi kebahagiaan melalui seni merangkai.</p>
                <p class="animate-on-load delay-7">Didirikan pada tahun 2022, kami telah berkembang dari usaha kecil yang saat itu belum terlalu banyak penjualnya di ponorogo menjadi toko bunga online terpercaya di Ponorogo dan sekitarnya.</p>
                <div class="quote animate-on-load delay-8">
                    "Bunga adalah musik tanah. Kami hanya mencoba menangkap melodi indahnya untuk Anda."
                </div>
                <p class="animate-on-load delay-9">Setiap buket yang kami buat adalah cerminan dedikasi kami untuk kualitas, keindahan, dan kepuasan pelanggan.</p>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="section-header animate-on-load delay-10">
            <h2>Nilai-Nilai Kami</h2>
            <p>Prinsip yang memandu setiap karangan bunga yang kami ciptakan</p>
        </div>
        <div class="values-grid">
            <?php 
            $value_counter = 0;
            // Simulasi data nilai-nilai
            $values = [
                ['icon' => '✿', 'title' => 'Kualitas Premium', 'desc' => 'Kami hanya menggunakan bunga segar berkualitas tinggi yang dipilih dengan teliti untuk memastikan setiap buket bertahan lebih lama dan terlihat menawan.'],
                ['icon' => '❀', 'title' => 'Kreativitas Tanpa Batas', 'desc' => 'Tim desainer kami selalu berinovasi dengan tren terbaru untuk menciptakan rangkaian bunga yang unik dan memukau.'],
                ['icon' => '✽', 'title' => 'Kepuasan Pelanggan', 'desc' => 'Kepuasan Anda adalah prioritas utama kami. Kami berkomitmen untuk memberikan pengalaman berbelanja yang menyenangkan dari awal hingga akhir.']
            ];
            foreach ($values as $value): 
            $value_counter++;
            ?>
                <div class="value-card animate-on-load delay-<?php echo $value_counter + 10; ?>">
                    <div class="value-icon"><?php echo $value['icon']; ?></div>
                    <h3><?php echo $value['title']; ?></h3>
                    <p><?php echo $value['desc']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

       <!-- Workshop & Produksi Section (Pengganti Team Section) -->
    <section class="team-section">
        <div class="section-header animate-on-load delay-<?php echo $timeline_counter + 15; ?>">
            <h2>Workshop & Produksi Kami</h2>
            <p>Setiap buket dirangkai langsung dengan penuh ketelitian dan cinta untuk menciptakan momen spesial Anda</p>
        </div>

        <div class="team-grid">
            <?php 
            $team_counter = 0;
            // Informasi proses produksi
            $workshop_items = [
                [
                    'title' => 'Bunga Segar Setiap Hari',
                    'desc' => 'Kami memilih bunga segar setiap hari agar kualitas buket tetap terjaga dan tahan lebih lama.',
                    'img' => 'assets/images/aboutus/1.jpeg'
                ],
                [
                    'title' => 'Perangkaian Handmade',
                    'desc' => 'Setiap buket dirangkai secara manual dengan perhatian pada detail dan estetika.',
                    'img' => 'assets/images/aboutus/2.jpg'
                ],
                [
                    'title' => 'Desain Buket Eksklusif',
                    'desc' => 'Setiap buket dirancang dengan desain unik dan eksklusif, memberikan kesan indah dan istimewa untuk setiap momen spesial.',
                    'img' => 'assets/images/aboutus/3.jpeg'
                ]
            ];

            foreach ($workshop_items as $item): 
            $team_counter++;
            ?>
                <div class="team-member animate-on-load delay-<?php echo $team_counter + 18; ?>">
                    <div class="member-image">
                        <img src="<?php echo $item['img']; ?>" alt="<?php echo $item['title']; ?>">
                    </div>
                    <div class="member-info">
                        <h4><?php echo $item['title']; ?></h4>
                        <p><?php echo $item['desc']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content animate-on-load delay-<?php echo $team_counter + 19; ?>">
            <h2>Siap Membuat Momen Spesial Anda?</h2>
            <p>Jelajahi koleksi buket bunga kami dan temukan yang sempurna untuk kesempatan istimewa Anda</p>
            <a href="index.php" class="cta-button">Lihat Koleksi Kami</a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>