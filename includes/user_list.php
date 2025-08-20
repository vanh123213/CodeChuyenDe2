<?php


// ===== XÓA USER =====
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location:  admin.php?page=user");
    exit;
}

// ===== THÊM / SỬA USER =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if (!empty($_POST['id'])) {
        // Cập nhật
        if ($password) {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, phone=?, password=? WHERE id=?");
            $stmt->execute([$name, $email, $phone, $password, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
            $stmt->execute([$name, $email, $phone, $_POST['id']]);
        }
    } else {
        // Thêm mới
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $phone, $password]);
    }
    header("Location: admin.php?page=user");
    exit;
}

// ===== LẤY DỮ LIỆU SỬA =====
$editUser = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ===== TÌM KIẾM USER =====
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $pdo->prepare("
        SELECT * FROM users 
        WHERE name LIKE ? 
           OR username LIKE ? 
           OR email LIKE ? 
           OR phone LIKE ?
        ORDER BY id DESC
    ");
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
} else {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
}

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h3>👤 Quản lý User</h3>



<!-- FORM TÌM KIẾM -->
<form method="GET" action="admin.php" class="mb-3 d-flex gap-2">
    <input type="hidden" name="page" value="user">
    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, username, email, SĐT"
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="max-width:300px;">
    <button type="submit"class="btn btn-primary">Tìm kiếm</button>
    <a href="admin.php?page=user" class="btn btn-secondary">Xóa tìm</a>
</form>


<!-- DANH SÁCH USER -->
<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users): ?>
        <?php foreach ($users as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
        <td>
    <a href="user_detail.php?username=<?= urlencode($row['username']) ?>">
    <?= htmlspecialchars($row['name']) ?>
</a>

</td>


            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
                <a href="admin.php?page=user&edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
<a href="admin.php?page=user&delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
   onclick="return confirm('Xóa user này?')">Xóa</a>

            </td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td colspan="6" class="text-center">Chưa có user nào.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
