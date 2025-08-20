<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra user login
    $isLoggedIn = isset($_SESSION['username']); 
    $username = '';
    $name = '';
    $phone = '';

    if ($isLoggedIn) {
        // Nếu login → lấy username từ session
        $username = $_SESSION['username'];

        // Lấy name, phone từ bảng users
        $stmt = $pdo->prepare("SELECT name, phone FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        $name  = $userInfo['name'] ?? '';
        $phone = $userInfo['phone'] ?? '';

    } else {
        // Nếu guest → nhập name + phone
        $guestName  = trim($_POST['name'] ?? '');
        $guestPhone = trim($_POST['phone'] ?? '');

        $username   = $guestName . " - " . $guestPhone;
        $name       = $guestName;
        $phone      = $guestPhone;
    }

    // Các input chung
    $carType     = $_POST['car-type'] ?? '';
    $rentalType  = $_POST['rental-type'] ?? '';
    $pickupDate  = $_POST['pickup-date'] ?? '';
    $returnDate  = $_POST['return-date'] ?? '';
    $note        = trim($_POST['note'] ?? '');

    // Kiểm tra đầy đủ
    if ($username && $carType && $rentalType && $pickupDate && $returnDate) {

        if (strtotime($returnDate) < strtotime($pickupDate)) {
            die("<div style='color:red;text-align:center;'>Ngày trả phải sau ngày thuê!</div>");
        }

        try {
            // Kiểm tra xe còn không
            $checkCar = $pdo->prepare("SELECT id, total_quantity, rented_quantity FROM cars WHERE name = :name");
            $checkCar->execute(['name' => $carType]);
            $car = $checkCar->fetch(PDO::FETCH_ASSOC);

            if (!$car) die("<div style='color:red;text-align:center;'>Xe không tồn tại!</div>");
            if ($car['rented_quantity'] >= $car['total_quantity']) {
                die("<div style='color:red;text-align:center;'>🚫 Xe đã hết!</div>");
            }

            $pdo->beginTransaction();

            // Chèn booking mới
            $stmt = $pdo->prepare("
                INSERT INTO bookings (username, name, phone, car_type, rental_type, pickup_date, return_date, note) 
                VALUES (:username, :name, :phone, :car_type, :rental_type, :pickup_date, :return_date, :note)
            ");
            $stmt->execute([
                'username'    => $username,
                'name'        => $name,
                'phone'       => $phone,
                'car_type'    => $carType,
                'rental_type' => $rentalType,
                'pickup_date' => $pickupDate,
                'return_date' => $returnDate,
                'note'        => $note
            ]);

            // Update số lượng xe đã thuê
            $updateCar = $pdo->prepare("UPDATE cars SET rented_quantity = rented_quantity + 1 WHERE id = :id");
            $updateCar->execute(['id' => $car['id']]);

            $pdo->commit();

            if (isset($_SESSION['username'])) {
    // User đã đăng nhập
    echo "<div style='text-align:center; padding:40px;'>
            <h2 style='color:green;'>✅ Đặt xe thành công!</h2>
            <p>Chúng tôi sẽ liên hệ lại với bạn sớm nhất.</p>
            <a href='user.php' style='padding:10px 20px; background:#007bff; color:white; border-radius:6px; text-decoration:none;'>Về trang cá nhân</a>
          </div>";
} else {
    // Guest
    echo "<div style='text-align:center; padding:40px;'>
            <h2 style='color:green;'>✅ Đặt xe thành công!</h2>
            <p>Chúng tôi sẽ liên hệ lại với bạn sớm nhất.</p>
            <a href='index.html' style='padding:10px 20px; background:#007bff; color:white; border-radius:6px; text-decoration:none;'>Quay lại Trang chủ</a>
          </div>";
}


        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "<div style='color:red;text-align:center;'>Lỗi: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div style='color:orange;text-align:center;'>⚠️ Vui lòng điền đầy đủ thông tin.</div>";
    }
} else {
    echo "<div style='text-align:center;'>⛔ Không thể truy cập trực tiếp.</div>";
}
?>
