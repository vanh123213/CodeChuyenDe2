<?php
// Thêm liên hệ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_contact'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($name && $email && $subject && $message) {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $subject, $message]);
    }
    header("Location: admin.php?page=contact");
    exit;
}

// Sửa liên hệ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_contact'])) {
    $id = (int) $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE contacts SET name=?, email=?, subject=?, message=? WHERE id=?");
        $stmt->execute([$name, $email, $subject, $message, $id]);
    }
    header("Location: admin.php?page=contact");
    exit;
}

// Xóa liên hệ
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: admin.php?page=contact");
    exit;
}

// ================== TÌM KIẾM ==================
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM contacts 
        WHERE name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ? 
        ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h3>📬 Danh sách Liên hệ</h3>

<!-- Form tìm kiếm -->
<form method="get" class="mb-3">
    <input type="hidden" name="page" value="contact">
    <input type="text" name="search" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($search) ?>"
        class="form-control w-25 d-inline-block">
    <button type="submit" class="btn btn-primary">Tìm</button>
</form>

<!-- Form thêm mới -->
<form method="post" class="mb-3">
    <input type="hidden" name="add_contact" value="1">
    <div class="row">
        <div class="col"><input type="text" name="name" class="form-control" placeholder="Họ tên" required></div>
        <div class="col"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
        <div class="col"><input type="text" name="subject" class="form-control" placeholder="Tiêu đề" required></div>
    </div>
    <div class="row mt-2">
        <div class="col"><textarea name="message" class="form-control" placeholder="Nội dung" required></textarea></div>
    </div>
    <button type="submit" class="btn btn-success mt-2">➕ Thêm</button>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
<th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Ngày gửi</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($contacts): ?>
        <?php foreach ($contacts as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['subject']) ?></td>
            <td><?= nl2br(htmlspecialchars($c['message'])) ?></td>
            <td><?= $c['created_at'] ?></td>
            <td>
                <!-- Nút sửa -->
                <button class="btn btn-warning btn-sm"
                    onclick="editContact(<?= $c['id'] ?>, '<?= htmlspecialchars($c['name']) ?>', '<?= htmlspecialchars($c['email']) ?>', '<?= htmlspecialchars($c['subject']) ?>', '<?= htmlspecialchars($c['message']) ?>')">Sửa</button>
                <!-- Nút xóa -->
                <a href="admin.php?page=contact&delete=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                    onclick="return confirm('Xóa liên hệ này?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td colspan="7" class="text-center">Không có liên hệ nào</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- FORM SỬA LIÊN HỆ -->
<div id="editForm" style="display:none;" class="card mb-4">
    <div class="card-header bg-primary text-white">✏️ Sửa liên hệ</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="edit_contact" value="1">
            <input type="hidden" name="id" id="edit_id">

            <div class="row g-2">
                <div class="col">
                    <input type="text" name="name" id="edit_name" class="form-control" placeholder="Họ tên" required>
                </div>
                <div class="col">
                    <input type="email" name="email" id="edit_email" class="form-control" placeholder="Email" required>
                </div>
                <div class="col">
                    <input type="text" name="subject" id="edit_subject" class="form-control" placeholder="Tiêu đề"
                        required>
                </div>
            </div>

            <div class="row g-2 mt-2">
                <div class="col">
                    <textarea name="message" id="edit_message" class="form-control" placeholder="Nội dung"
                        required></textarea>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">💾 Lưu</button>
                <button type="button" class="btn btn-secondary"
                    onclick="document.getElementById('editForm').style.display='none'">Hủy</button>
            </div>
        </form>
</div>
</div>

<script>
function editContact(id, name, email, subject, message) {
    document.getElementById('editForm').style.display = 'block';
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_subject').value = subject;
    document.getElementById('edit_message').value = message;
    window.scrollTo(0, 0);
}
</script>