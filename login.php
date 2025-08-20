<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin & User Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }

        .input-field {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.7);
        }

        .input-field:focus {
            background: white;
            box-shadow: 0 5px 15px rgba(92, 107, 192, 0.2);
        }

        .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 6px rgba(92, 107, 192, 0.15);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(92, 107, 192, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .logo {
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.1));
        }

        .tab {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="w-full max-w-md mx-4">
        <div class="glass-effect p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-6">
                    <img src="img/anhlogo.jpg" alt="Logo" class="logo w-24 h-24 object-contain">
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">HỆ THỐNG</h1>
                <p class="text-gray-600">Đăng nhập hoặc đăng ký</p>
            </div>

            <?php

session_start();
require 'db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // --- Kiểm tra admin ---
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username AND password = MD5(:password)");
        $stmt->execute([':username' => $username, ':password' => $password]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            $_SESSION['admin'] = $admin['username'];
            $_SESSION['name']  = $admin['name']; // tên đầy đủ
            header("Location: admin.php");
            exit();
        }

        // --- Kiểm tra user ---
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = MD5(:password)");
        $stmt->execute([':username' => $username, ':password' => $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username']; // để đăng nhập
            $_SESSION['name']     = $user['name'];     // tên đầy đủ hiển thị
            header("Location: user.php");
            exit();
        }

        $msg = "Tên đăng nhập hoặc mật khẩu sai!";
    } elseif ($_POST['action'] == 'register') {
        $name     = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $phone    = $_POST['phone'] ?? '';

        $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password, phone) 
                               VALUES (:name, :username, :email, MD5(:password), :phone)");
        try {
            $stmt->execute([
                ':name'     => $name,
                ':username' => $username,
                ':email'    => $email,
                ':password' => $password,
                ':phone'    => $phone
            ]);
            $msg = "Đăng ký thành công, mời bạn đăng nhập!";
        } catch (Exception $e) {
            $msg = "Lỗi: " . $e->getMessage();
        }
    }
}

if ($msg) {
    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">' . $msg . '</div>';
}
?>



            <!-- Tabs -->
            <div class="flex mb-6">
                <div class="tab w-1/2 text-center py-2 font-medium border-b-2 border-indigo-500 text-indigo-600"
                    onclick="showForm('login')">Đăng nhập</div>
                <div class="tab w-1/2 text-center py-2 font-medium border-b-2 border-gray-200 text-gray-600"
                    onclick="showForm('register')">Đăng ký</div>
            </div>

            <!-- Login Form -->
            <form id="loginForm" method="POST" class="space-y-6">
                <input type="hidden" name="action" value="login">
                <div>
                    <label class="text-sm font-medium">Tên đăng nhập</label>
                    <input type="text" name="username" required
                        class="input-field w-full px-5 py-3 rounded-xl focus:ring-2 focus:ring-indigo-200">
                </div>
                <div>
                    <label class="text-sm font-medium">Mật khẩu</label>
                    <input type="password" name="password" required
                        class="input-field w-full px-5 py-3 rounded-xl focus:ring-2 focus:ring-indigo-200">
                </div>
                <button type="submit" class="login-btn w-full py-3 px-4 rounded-xl text-white font-medium">Đăng nhập</button>
            </form>

            <!-- Register Form -->
            <form id="registerForm" method="POST" class="space-y-6 hidden">
                <input type="hidden" name="action" value="register">
                <div>
    <label class="text-sm font-medium">Tên người dùng</label>
    <input type="text" name="name" required
           class="input-field w-full px-5 py-3 rounded-xl focus:ring-2 focus:ring-indigo-200">
</div>
                <div>
                    <label class="text-sm font-medium">Tên đăng nhập</label>
                    <input type="text" name="username" required
                        class="input-field w-full px-5 py-3 rounded-xl focus:ring-2 focus:ring-indigo-200">
                </div>
                
                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" required
                        class="input-field w-full px-5 py-3 rounded-xl focus:ring-2 focus:ring-indigo-200">
                </div>
                <div>
                    <label class="text-sm font-medium">Mật khẩu</label>
                    <input type="password" name="password" required
                        class="input-field w-full px-5 py-3 rounded-xl focus:ring-2 focus:ring-indigo-200">
                </div>
                 <div>
        <label class="text-sm font-medium">Số điện thoại</label>
        <input type="text" name="phone" required
            class="input-field w-full px-5 py-3 rounded-xl focus:ring-2 focus:ring-indigo-200">
    </div>
                <button type="submit" class="login-btn w-full py-3 px-4 rounded-xl text-white font-medium">Đăng ký</button>
            </form>

            <div class="mt-6 text-center">
                <a href="index.html" class="text-gray-800 hover:text-blue-600">Về trang chủ</a>
            </div>
        </div>
    </div>

    <script>
        function showForm(type) {
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registerForm').classList.add('hidden');
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600');
                tab.classList.add('border-gray-200', 'text-gray-600');
            });
            if (type === 'login') {
                document.getElementById('loginForm').classList.remove('hidden');
                tabs[0].classList.add('border-indigo-500', 'text-indigo-600');
                tabs[0].classList.remove('border-gray-200', 'text-gray-600');
            } else {
                document.getElementById('registerForm').classList.remove('hidden');
                tabs[1].classList.add('border-indigo-500', 'text-indigo-600');
                tabs[1].classList.remove('border-gray-200', 'text-gray-600');
            }
        }
    </script>
</body>

</html>
