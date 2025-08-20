<?php include 'db.php'; 


?>
<!DOCTYPE html>
<html lang="vi">



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị - AHK Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .sidebar {
        height: 100vh;
        background: #343a40;
        padding-top: 20px;
        position: fixed;
        width: 220px;
    }

    .sidebar a {
        color: #fff;
        padding: 10px 20px;
        display: block;
        text-decoration: none;
    }

    .sidebar a:hover {
        background: #495057;
    }

    .content {
        margin-left: 230px;
        padding: 20px;
    }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-white text-center">Admin Panel</h4>
        <a href="admin.php?page=booking">📑 Quản lý Đặt xe</a>
        <a href="admin.php?page=contact">📬 Quản lý Liên hệ</a>
        <a href="admin.php?page=user">👤 Quản lý User</a>
        <a href="admin.php?page=car">🚗 Quản lý Xe</a>

        <a href="logout.php">
            🚪 Đăng xuất
            (<?= isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin' ?>)</a>




    </div>

    <!-- Nội dung chính -->
    <div class="content">
        <h2>Trang Quản trị</h2>
        <hr>
        <?php
        $page = $_GET['page'] ?? 'booking'; // mặc định là booking
        $file = "includes/" . $page . "_list.php";

        if (file_exists($file)) {
            include $file;
        } else {
            echo "<div class='alert alert-danger'>Không tìm thấy trang yêu cầu!</div>";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>