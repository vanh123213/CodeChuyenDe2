<?php
// Hiển thị lỗi khi debug (xóa khi chạy thật)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu
    $name    = trim($_POST['contact-name'] ?? '');
    $email   = trim($_POST['contact-email'] ?? '');
    $subject = trim($_POST['contact-subject'] ?? '');
    $message = trim($_POST['contact-message'] ?? '');

    // Kiểm tra thông tin bắt buộc
    if ($name && $email && $subject && $message) {
        try {
            $sql = "INSERT INTO contacts (name, email, subject, message)
                    VALUES (:name, :email, :subject, :message)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name'    => $name,
                'email'   => $email,
                'subject' => $subject,
                'message' => $message
            ]);

            echo "<div style='text-align:center; padding:40px; font-family:Arial'>
                    <h2 style='color:green;'>✅ Cảm ơn bạn đã liên hệ!</h2>
                    <p>Chúng tôi sẽ phản hồi lại bạn trong thời gian sớm nhất.</p>
                    <a href='index.html' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#007bff; color:#fff; text-decoration:none; border-radius:6px;'>Quay lại Trang chủ</a>
                  </div>";
        } catch (PDOException $e) {
            echo "<div style='color:red; text-align:center;'>❌ Lỗi khi lưu liên hệ: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div style='color:orange; text-align:center;'>⚠️ Vui lòng điền đầy đủ thông tin trước khi gửi.</div>";
    }
} else {
    echo "<div style='text-align:center;'>⛔ Truy cập không hợp lệ.</div>";
}
?>