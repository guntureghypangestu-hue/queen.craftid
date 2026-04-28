<?php
// Mulai Output Buffering. Ini adalah kunci untuk menyelesaikan masalah redirect.
ob_start();

session_start();
require_once '../includes/db.php';

 $error = '';

// Proses login saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cari user di database berdasarkan username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika user ditemukan dan password cocok
    if ($user && password_verify($password, $user['password_hash'])) {
        // Set session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        
        // Arahkan ke halaman dashboard
        header('Location: index.php');
        exit; // Penting: hentikan eksekusi skrip setelah redirect
    } else {
        $error = 'Username atau Password salah!';
    }
}

// Akhiri Output Buffering dan kirim ke browser
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BuketQueen</title>
    
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Kustom untuk Halaman Login -->
    <style>
        /* --- Impor Variabel dari Style Utama (sesuaikan path jika perlu) --- */
        :root {
            --primary-color: #E8B4B8;
            --secondary-color: #4A6A52;
            --accent-color: #F5F5F5;
            --text-color: #333;
            --light-text: #777;
            --white-color: #fff;
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Poppins', sans-serif;
            --transition: all 0.3s ease;
        }

        /* --- General Styling --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: linear-gradient(135deg, rgba(232, 180, 184, 0.9), rgba(74, 106, 82, 0.9)), url('https://picsum.photos/seed/admin-bg/1920/1080.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* --- Login Container --- */
        .login-wrapper {
            width: 100%;
            max-width: 450px;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .login-header {
            background-color: var(--white-color);
            padding: 30px 40px;
            text-align: center;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .login-header h1 {
            font-family: var(--font-heading);
            font-size: 2rem;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }

        .login-header p {
            color: var(--light-text);
            font-size: 0.9rem;
        }

        .login-form {
            padding: 40px;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-group input {
            width: 100%;
            padding: 15px 15px 15px 50px; /* Padding kiri untuk ikon */
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(232, 180, 184, 0.2);
            background-color: var(--white-color);
        }

        .form-group .input-icon {
            position: absolute;
            left: 18px;
            top: 68%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.1rem;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color), #d6a5b0);
            color: var(--white-color);
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(232, 180, 184, 0.3);
        }

        .back-to-site {
            text-align: center;
            padding: 20px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .back-to-site a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .back-to-site a:hover {
            color: var(--primary-color);
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }

        /* --- Responsif --- */
        @media (max-width: 500px) {
            .login-wrapper {
                max-width: 100%;
                margin: 0 10px;
            }
            .login-header {
                padding: 25px 30px;
            }
            .login-form {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p>Selamat datang kembali</p>
        </div>
        <form class="login-form" action="login.php" method="post">
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="username">Username</label>
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
        <div class="back-to-site">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> Kembali ke Toko</a>
        </div>
    </div>
</body>
</html>