<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) && !isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$username = null;

// Nếu là admin và có username trên URL (?username=xxx)
if (isset($_SESSION['admin']) && isset($_GET['username'])) {
    $username = $_GET['username'];
}
// Nếu là user thường thì lấy từ session
elseif (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}

if (!$username) {
    echo "<p style='color:red; text-align:center;'>Không xác định được user.</p>";
    exit();
}

// lấy user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p style='color:red; text-align:center;'>Không tìm thấy user.</p>";
    exit();
}

// lấy các xe user đã thuê
$stmt2 = $pdo->prepare("SELECT * FROM bookings WHERE username = ?");
$stmt2->execute([$user['username']]);
$rentals = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang cá nhân</title>
    <!-- Có thể bỏ Tailwind nếu không dùng -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->

    <!-- CSS nhúng trực tiếp -->
    <style>
      /* ========== Base & Layout ========== */
      :root{
        --bg: #0f172a;           /* nền xanh đen */
        --card: #111827;         /* thẻ */
        --muted: #94a3b8;        /* chữ phụ */
        --text: #e5e7eb;         /* chữ chính */
        --primary: #22d3ee;      /* điểm nhấn */
        --ring: rgba(34,211,238,.35);
        --radius: 16px;
      }
      * { box-sizing: border-box; }
      html, body { height: 100%; }
      body{
        margin:0;
        font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji";
        background: radial-gradient(1200px 800px at 10% 0%, #1f2937 0%, var(--bg) 35%) fixed;
        color: var(--text);
        line-height: 1.6;

        /* căn giữa khung nội dung */
        display:flex;
        justify-content:center;
      }
      .container{
        width: 100%;
        max-width: 920px;
        padding: 28px 20px;
      }

      /* ========== Typography & Titles ========== */
      h2, h3{
        margin: 0 0 14px;
        letter-spacing: .2px;
      }
      h2{
        font-size: 28px;
        font-weight: 800;
        background: linear-gradient(90deg, #fff, #a7f3d0 40%, var(--primary));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
      }
      h3{
        font-size: 20px;
        font-weight: 700;
        color: #cbd5e1;
        margin-top: 28px;
      }
      p{
        margin: 0;
        color: #e5e7eb;
      }
      p strong{ color:#a5b4fc; font-weight:700; }

      /* ========== Card cho “Thông tin cá nhân” ========== */
      /* Tạo card cho 4 thẻ p đầu sau h2 */
      h2 + p, h2 + p + p, h2 + p + p + p, h2 + p + p + p + p{
        background: linear-gradient(180deg, #111827, #0b1220);
        border: 1px solid #1f2937;
        border-radius: var(--radius);
        padding: 16px 18px;
        margin: 10px 0 0;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.02) inset;
      }
      /* Khoảng cách giữa các p trong card */
      h2 + p + p, h2 + p + p + p, h2 + p + p + p + p{ margin-top: 8px; }

      /* ========== Danh sách xe ========== */
      ul{
        list-style: none;
        padding: 0;
        margin: 12px 0 0;
        display: grid;
        gap: 12px;
      }
      li{
        background: linear-gradient(180deg, #0e1627, #0b1220);
        border: 1px solid #1e293b;
        border-radius: calc(var(--radius) - 2px);
        padding: 14px 16px;
        position: relative;
        box-shadow: 0 8px 24px -12px rgba(0,0,0,.7);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
      }
      li::before{
        content:"";
        position:absolute;
        inset: -1px;
        border-radius: inherit;
        pointer-events:none;
        background: radial-gradient(120px 60px at 0% 0%, var(--ring), transparent 60%);
        opacity:.6;
        transition: opacity .18s ease;
      }
      li:hover{
        transform: translateY(-2px);
        border-color: rgba(34,211,238,.5);
        box-shadow: 0 14px 36px -14px rgba(34,211,238,.35);
      }
      li:hover::before{ opacity: .9; }
      li b, li strong{ color:#7dd3fc; font-weight:700; }

      /* ========== Link điều hướng ========== */
      a{
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
      }
      a:hover{ text-decoration: underline; }
      /* style cho nút quay lại (thẻ p > a ở cuối) */
      .back-link{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        margin-top: 20px;
        border: 1px solid #1f2937;
        border-radius: 999px;
        background: linear-gradient(180deg, #0f172a, #0b1220);
        box-shadow: 0 6px 18px -12px rgba(0,0,0,.7);
        transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
      }
      .back-link::before{
        content: "⬅";
        font-size: 14px;
        opacity: .9;
      }
      .back-link:hover{
        transform: translateY(-1px);
        border-color: rgba(34,211,238,.55);
        box-shadow: 0 10px 26px -14px rgba(34,211,238,.35);
        text-decoration: none;
      }

      /* ========== Trạng thái/Alert ========== */
      p[style*="color:red"]{
        background: #4c0519;
        color: #fecdd3 !important;
        border: 1px solid #7f1d1d;
        padding: 12px 14px;
        border-radius: 12px;
        max-width: 920px;
        margin: 16px auto;
        text-align: center !important;
      }

      /* ========== Responsive ========== */
      @media (max-width: 640px){
        .container{ padding: 20px 14px; }
        h2{ font-size: 24px; }
        h3{ font-size: 18px; }
        li{ padding: 12px 12px; }
        .back-link{ padding: 9px 12px; }
      }
    </style>
</head>
<body>
  <div class="container">
    <h2>Thông tin cá nhân</h2>
    <p><strong>Họ và tên:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Tên đăng nhập:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($user['phone']) ?></p>

    <h3>Xe đang thuê</h3>
    <?php if (!empty($rentals)): ?>
        <ul>
            <?php foreach ($rentals as $rent): ?>
                <li>
                    <?= htmlspecialchars($rent['car_type'] ?? $rent['car_name']) ?> —
                    <strong>Loại thuê:</strong> <?= htmlspecialchars($rent['rental_type']) ?> —
                    <strong>Ngày thuê:</strong> <?= htmlspecialchars($rent['pickup_date']) ?> —
                    <strong>Ngày trả:</strong> <?= htmlspecialchars($rent['return_date']) ?>
                    <?php if (!empty($rent['note'])): ?>
                        — <strong>Ghi chú:</strong> <?= htmlspecialchars($rent['note']) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Chưa thuê xe nào.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['admin'])): ?>
        <p><a class="back-link" href="admin.php?page=user">Quay lại danh sách user</a></p>
    <?php else: ?>
        <p><a class="back-link" href="user.php">Quay lại</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
