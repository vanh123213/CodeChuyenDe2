<?php

// Lấy danh sách xe
$stmt = $pdo->query("SELECT * FROM cars");
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách xe</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-4">
        <h2 class="mb-4 text-center text-primary">🚗 Danh sách xe</h2>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tên xe</th>
                            <th>Loại xe</th>
                            <th>Tổng số lượng</th>
                            <th>Đã thuê</th>
                            <th>Còn lại</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?= htmlspecialchars($car['id']) ?></td>
                            <td class="fw-bold text-primary"><?= htmlspecialchars($car['name']) ?></td>
                            <td><?= htmlspecialchars($car['type']) ?></td>
                            <td><?= htmlspecialchars($car['total_quantity']) ?></td>
                            <td class="text-danger"><?= htmlspecialchars($car['rented_quantity']) ?></td>
                            <td class="text-success fw-bold">
                                <?= htmlspecialchars($car['total_quantity'] - $car['rented_quantity']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>