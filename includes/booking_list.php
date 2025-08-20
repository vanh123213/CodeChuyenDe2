<?php


// ===== XÓA =====
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ===== THÊM / SỬA =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = $_POST['name'];
    $phone       = $_POST['phone'];
    $car_type    = $_POST['car_type'];
    $rental_type = $_POST['rental_type'];
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    $note        = $_POST['note'];

    if (!empty($_POST['id'])) {
        // Cập nhật
        $stmt = $pdo->prepare("UPDATE bookings SET name=?, phone=?, car_type=?, rental_type=?, pickup_date=?, return_date=?, note=? WHERE id=?");
        $stmt->execute([$name, $phone, $car_type, $rental_type, $pickup_date, $return_date, $note, $_POST['id']]);
    } else {
        // Thêm mới
        $stmt = $pdo->prepare("INSERT INTO bookings (name, phone, car_type, rental_type, pickup_date, return_date, note, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $phone, $car_type, $rental_type, $pickup_date, $return_date, $note]);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ===== LẤY DỮ LIỆU SỬA =====
$editBooking = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editBooking = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ===== TÌM KIẾM =====
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
if ($search != "") {
    $stmt = $pdo->prepare("
        SELECT * FROM bookings 
        WHERE id LIKE :kw OR phone LIKE :kw OR name LIKE :kw 
        ORDER BY id DESC
    ");
    $stmt->execute(['kw' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM bookings ORDER BY id DESC");
}
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h3>🚗 Quản lý Đặt xe</h3>

<!-- FORM THÊM / SỬA -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <?= $editBooking ? "✏️ Sửa đơn đặt xe" : "➕ Thêm đơn đặt xe" ?>
    </div>
    <div class="card-body">
        <form method="post">
            <?php if ($editBooking): ?>
            <input type="hidden" name="id" value="<?= $editBooking['id'] ?>">
            <?php endif; ?>
            <div class="row g-2">
                <div class="col"><input type="text" name="name" class="form-control" placeholder="Họ tên" required
                        value="<?= $editBooking['name'] ?? '' ?>"></div>
                <div class="col"><input type="text" name="phone" class="form-control" placeholder="Số điện thoại"
                        required value="<?= $editBooking['phone'] ?? '' ?>"></div>
                <div class="col"><input type="text" name="car_type" class="form-control" placeholder="Loại xe" required
                        value="<?= $editBooking['car_type'] ?? '' ?>"></div>
                <div class="col"><input type="text" name="rental_type" class="form-control" placeholder="Hình thức thuê"
                        required value="<?= $editBooking['rental_type'] ?? '' ?>"></div>
                <div class="col"><input type="date" name="pickup_date" class="form-control" required
                        value="<?= $editBooking['pickup_date'] ?? '' ?>"></div>
                <div class="col"><input type="date" name="return_date" class="form-control" required
                        value="<?= $editBooking['return_date'] ?? '' ?>"></div>
                <div class="col"><input type="text" name="note" class="form-control" placeholder="Ghi chú"
                        value="<?= $editBooking['note'] ?? '' ?>"></div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success"><?= $editBooking ? "Cập nhật" : "Thêm" ?></button>
                    <?php if ($editBooking): ?>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">Hủy</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- FORM TÌM KIẾM -->
<form method="get" class="mb-3 d-flex gap-2">
    <input type="text" name="search" class="form-control" placeholder="Tìm theo ID, SĐT hoặc Tên"
        value="<?= htmlspecialchars($search) ?>" style="max-width:300px;">
    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">Xóa tìm</a>
</form>

<!-- DANH SÁCH -->
<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Số điện thoại</th>
            <th>Loại xe</th>
            <th>Hình thức thuê</th>
            <th>Ngày nhận</th>
            <th>Ngày trả</th>
            <th>Ghi chú</th>
            <th>Ngày đặt</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($bookings): ?>
        <?php foreach ($bookings as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['car_type']) ?></td>
            <td><?= htmlspecialchars($row['rental_type']) ?></td>
            <td><?= htmlspecialchars($row['pickup_date']) ?></td>
            <td><?= htmlspecialchars($row['return_date']) ?></td>
            <td><?= htmlspecialchars($row['note']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
                <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                    onclick="return confirm('Xóa đơn này?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td colspan="10" class="text-center">Chưa có đơn đặt xe.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>