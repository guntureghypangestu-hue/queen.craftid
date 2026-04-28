<?php require_once 'includes/header.php'; ?>

<!-- CSS Khusus Halaman Kontak (TERISOLASI) -->
<!-- Gaya-gaya ini hanya akan berlaku untuk elemen di dalam .contact-page-container -->
<style>
    /* ===================================
       KONTAINER UTAMA HALAMAN KONTAK
       =================================== */
    .contact-page-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        color: #333;
        line-height: 1.6;
    }

    /* ===================================
       1. HERO SECTION (ELEGAN & RESPONSIF)
       =================================== */
    .contact-page-container .contact-hero {
        background: linear-gradient(135deg, rgba(232, 180, 184, 0.85), rgba(74, 106, 82, 0.85)), 
                    url('https://picsum.photos/seed/pink-flowers/1920/1080.jpg') no-repeat center center/cover;
        background-attachment: fixed;
        padding: 140px 20px;
        text-align: center;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        margin-top: 80px;
    }

    .contact-page-container .contact-hero::before {
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

    .contact-page-container .contact-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .contact-page-container .contact-hero-content {
        max-width: 700px;
        margin: 0 auto;
        animation: fadeInDown 0.8s ease-out;
        position: relative;
        z-index: 2;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .contact-page-container .contact-hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 700;
        margin-bottom: 20px;
        letter-spacing: -1px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .contact-page-container .contact-hero p {
        font-size: 1.2rem;
        font-weight: 300;
        margin-bottom: 30px;
        opacity: 0.95;
        line-height: 1.8;
    }

    /* Dekorasi garis di bawah heading */
    .contact-page-container .hero-decoration {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 25px 0;
        gap: 15px;
    }

    .contact-page-container .hero-decoration span {
        display: inline-block;
        width: 40px;
        height: 2px;
        background-color: rgba(255, 255, 255, 0.6);
    }

    .contact-page-container .hero-decoration span.center {
        width: auto;
        height: auto;
        background: none;
        font-size: 20px;
        opacity: 0.8;
    }

    .contact-page-container .hero-cta-button {
        display: inline-block;
        padding: 14px 35px;
        background-color: #fff;
        color: #4A6A52;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .contact-page-container .hero-cta-button:hover {
        background-color: #E8B4B8;
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 7px 25px rgba(232, 180, 184, 0.4);
    }

    /* ===================================
       2. KONTEN UTAMA (INFO, PETA, FORM)
       =================================== */
    .contact-page-container .contact-main-content {
        padding: 80px 20px;
        background-color: #f8f9fc;
    }

    .contact-page-container .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    /* ===================================
       3. KARTU INFORMASI KONTAK
       =================================== */
    .contact-page-container .contact-info-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        display: flex;
        flex-direction: column;
        height: 100%;
        border-top: 4px solid #E8B4B8;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .contact-page-container .contact-info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(232, 180, 184, 0.15);
    }

    .contact-page-container .contact-info-card h2 {
        font-size: 1.8rem;
        color: #4A6A52;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
        font-family: 'Playfair Display', serif;
    }

    .contact-page-container .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }

    .contact-page-container .info-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #E8B4B8, #db7093);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .contact-page-container .info-icon i {
        font-size: 1.3rem;
        color: #ffffff;
    }

    .contact-page-container .info-item:hover .info-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 5px 15px rgba(232, 180, 184, 0.3);
    }

    .contact-page-container .info-text h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #4A6A52;
        margin-bottom: 4px;
    }

    .contact-page-container .info-text p, .contact-page-container .info-text a {
        font-size: 0.95rem;
        color: #6c757d;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .contact-page-container .info-text a:hover {
        color: #E8B4B8;
    }

    /* ===================================
       4. KARTU PETA
       =================================== */
    .contact-page-container .map-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        height: 100%;
        min-height: 450px;
    }

    .contact-page-container .map-card iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* ===================================
       5. FORMULIR KONTAK
       =================================== */
    .contact-page-container .contact-form-section {
        grid-column: 1 / -1; /* Membuat form memenuhi lebar penuh */
        background: #ffffff;
        border-radius: 16px;
        padding: 50px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        margin-top: 20px;
    }

    .contact-page-container .contact-form-section h2 {
        font-size: 2rem;
        color: #001845;
        text-align: center;
        margin-bottom: 10px;
    }

    .contact-page-container .contact-form-section p {
        text-align: center;
        color: #6c757d;
        margin-bottom: 40px;
    }

    .contact-page-container .contact-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .contact-page-container .form-group {
        position: relative;
    }

    .contact-page-container .form-group.full-width {
        grid-column: 1 / -1;
    }

    .contact-page-container .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #343a40;
    }

    .contact-page-container .form-group input,
    .contact-page-container .form-group textarea {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
    }

    .contact-page-container .form-group input:focus,
    .contact-page-container .form-group textarea:focus {
        outline: none;
        border-color: #2962ff;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(41, 98, 255, 0.1);
    }

    .contact-page-container .form-group.error input,
    .contact-page-container .form-group.error textarea {
        border-color: #dc3545;
    }

    .contact-page-container .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 6px;
        display: none;
    }

    .contact-page-container .form-group.error .error-message {
        display: block;
    }

    .contact-page-container .submit-btn {
        grid-column: 1 / -1;
        padding: 16px 24px;
        background: linear-gradient(135deg, #4A6A52, #3a5342);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .contact-page-container .submit-btn:hover {
        background: linear-gradient(135deg, #E8B4B8, #db7093);
        transform: translateY(-2px);
        box-shadow: 0 7px 20px rgba(232, 180, 184, 0.35);
    }

    .contact-page-container .submit-btn:disabled {
        background-color: #adb5bd;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .contact-page-container .success-notification {
        grid-column: 1 / -1;
        background-color: #d4edda;
        color: #155724;
        padding: 18px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #c3e6cb;
        display: none;
        animation: fadeInUp 0.5s ease;
    }

    .contact-page-container .success-notification.show {
        display: block;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ===================================
       6. ANIMASI FADE IN UP
       =================================== */
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

    .contact-page-container .animate-on-load {
        opacity: 0;
        animation-name: fadeInUp;
        animation-duration: 0.8s;
        animation-fill-mode: forwards;
        animation-timing-function: ease-out;
    }

    .contact-page-container .animate-on-load.delay-1 { animation-delay: 0.1s; }
    .contact-page-container .animate-on-load.delay-2 { animation-delay: 0.2s; }
    .contact-page-container .animate-on-load.delay-3 { animation-delay: 0.3s; }
    .contact-page-container .animate-on-load.delay-4 { animation-delay: 0.4s; }

    /* ===================================
       7. RESPONSIVITAS
       =================================== */
    @media (max-width: 992px) {
        .contact-page-container .content-wrapper {
            grid-template-columns: 1fr;
        }
        .contact-page-container .contact-form-section {
            margin-top: 30px;
        }
        .contact-page-container .contact-hero {
            margin-top: 70px;
            padding: 120px 20px;
        }
    }

    @media (max-width: 768px) {
        .contact-page-container .contact-hero {
            padding: 80px 20px;
            margin-top: 60px;
        }

        .contact-page-container .contact-hero h1 {
            font-size: 2rem;
        }

        .contact-page-container .contact-hero p {
            font-size: 1rem;
        }

        .contact-page-container .hero-decoration {
            margin: 20px 0;
            gap: 10px;
        }

        .contact-page-container .hero-decoration span {
            width: 30px;
            height: 2px;
        }

        .contact-page-container .hero-decoration span.center {
            font-size: 16px;
        }

        .contact-page-container .hero-cta-button {
            padding: 12px 25px;
            font-size: 0.9rem;
        }

        .contact-page-container .contact-form-grid {
            grid-template-columns: 1fr;
        }
        .contact-page-container .form-group.full-width {
            grid-column: 1;
        }
        .contact-page-container .submit-btn {
            grid-column: 1;
        }
        .contact-page-container .success-notification {
            grid-column: 1;
        }
    }
</style>

<!-- Wrapper untuk mengisolasi konten -->
<main class="contact-page-container">
    <!-- Hero Section Elegan & Responsif -->
    <section class="contact-hero">
        <div class="contact-hero-content">
            <h1 class="animate-on-load delay-1">Hubungi Kami</h1>
            <div class="hero-decoration animate-on-load delay-2">
                <span></span>
                <span class="center">✿</span>
                <span></span>
            </div>
            <p class="animate-on-load delay-3">Ada pertanyaan atau siap membuat momen istimewa? Tim kami siap membantu Anda 24/7.</p>
            <a href="#contact-form" class="hero-cta-button animate-on-load delay-4">Kirim Pesan Sekarang</a>
        </div>
    </section>

    <!-- Main Content Section -->
    <section id="contact-form" class="contact-main-content">
        <div class="content-wrapper">
            <!-- Kartu Informasi Kontak -->
            <div class="contact-info-card">
                <h2>Info & Lokasi</h2>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-text">
                        <h3>Alamat</h3>
                        <p>Jl abu kasan, RT.01/RW.01 prayungan, Lingkung Satu, Paju, Kec. Ponorogo, Kabupaten Ponorogo </p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="info-text">
                        <h3>Telepon</h3>
                        <a href="tel:+628123456789">+62 821-3256-4840</a>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div class="info-text">
                        <h3>Email</h3>
                        <a href="mailto:info@queencraft.id">haifayogianaa15@gmail.com </a>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-clock"></i></div>
                    <div class="info-text">
                        <h3>Jam Operasional</h3>
                        <p>Senin - Sabtu: 08:00 - 17:00 WIB</p>
                    </div>
                </div>
            </div>

            <!-- Kartu Peta -->
            <div class="map-card">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.076154702501!2d111.44721211083741!3d-7.887101478421572!2m2!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e799f5a022ec3a1%3A0x3daeb2bf6db136ef!2sQueen%20craftid!5e0!3m2!1sid!2sid!4v1769606668204!5m2!1sid!2sid" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <!-- Formulir Kontak -->
            <div class="contact-form-section">
                <h2>Kirimkan Kami Pesan</h2>
                <p>Isi formulir di bawah ini dan kami akan merespons sesegera mungkin.</p>
                
                <form id="kontakForm" class="contact-form-grid">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required>
                        <div class="error-message">Nama wajib diisi.</div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                        <div class="error-message">Masukkan email yang valid.</div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="subject">Subjek</label>
                        <input type="text" id="subject" name="subject" placeholder="Contoh: Pesanan Buket Wisuda" required>
                        <div class="error-message">Subjek wajib diisi.</div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="message">Pesan Anda</label>
                        <textarea id="message" name="message" rows="6" required placeholder="Jelaskan permintaan atau pertanyaan Anda..."></textarea>
                        <div class="error-message">Pesan tidak boleh kosong.</div>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>

                    <div class="success-notification">
                        <i class="fas fa-check-circle"></i> Pesan Anda berhasil terkirim! Kami akan menghubungi Anda melalui WhatsApp.
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<!-- JavaScript Khusus Halaman Kontak -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kontakForm = document.getElementById('kontakForm');
    const formGroups = kontakForm.querySelectorAll('.form-group');
    
    // Smooth scroll untuk tombol CTA
    const ctaButton = document.querySelector('.hero-cta-button');
    if(ctaButton) {
        ctaButton.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('contact-form').scrollIntoView({ behavior: 'smooth' });
        });
    }

    // Validasi form real-time
    formGroups.forEach(group => {
        const input = group.querySelector('input, textarea');
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (group.classList.contains('error')) {
                validateField(input);
            }
        });
    });
    
    function validateField(input) {
        const formGroup = input.closest('.form-group');
        const value = input.value.trim();
        let isValid = true;
        
        if (value === '') {
            isValid = false;
        } else if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            isValid = false;
        }
        
        formGroup.classList.toggle('error', !isValid);
        return isValid;
    }

    // Logika pengiriman form
    if(kontakForm) {
        kontakForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isFormValid = true;
            formGroups.forEach(group => {
                const input = group.querySelector('input, textarea');
                if (!validateField(input)) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) {
                kontakForm.querySelector('.form-group.error input, .form-group.error textarea').focus();
                return;
            }

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;

            const submitBtn = kontakForm.querySelector('.submit-btn');
            const originalBtnContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            submitBtn.disabled = true;

            setTimeout(() => {
                const whatsappMessage = `Halo Queencraft.id, saya ada pesan dari website.%0A%0A*Nama:* ${name}%0A*Email:* ${email}%0A*Subjek:* ${subject}%0A%0A*Pesan:*%0A${message}`;
                const adminPhoneNumber = "6282233278088"; 
                const whatsappUrl = `https://wa.me/${adminPhoneNumber}?text=${whatsappMessage}`;
                
                window.open(whatsappUrl, '_blank');
                
                const successNotification = kontakForm.querySelector('.success-notification');
                successNotification.classList.add('show');
                
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Terkirim!';
                
                setTimeout(() => {
                    kontakForm.reset();
                    submitBtn.innerHTML = originalBtnContent;
                    submitBtn.disabled = false;
                    setTimeout(() => successNotification.classList.remove('show'), 5000);
                }, 2000);
            }, 1500);
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>