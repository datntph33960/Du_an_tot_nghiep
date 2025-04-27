<?php
require_once __DIR__ . '/../models/VoucherModel.php';

$VoucherModel = new VoucherModel();

// 1. XỬ LÝ NHẬN VOUCHER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receive_voucher'])) {
    if (!isset($_SESSION['user']['id'])) {
        header('Location: index.php?url=dang-nhap');
        exit;
    }

    $user_id    = $_SESSION['user']['id'];
    $voucher_id = intval($_POST['voucher_id']);
    $granted    = $VoucherModel->give_voucher_to_user($user_id, $voucher_id);

    if ($granted) {
        $_SESSION['flash_success'] = 'Bạn đã nhận voucher thành công!';
    } else {
        if ($VoucherModel->user_received_voucher($user_id, $voucher_id)) {
            $_SESSION['flash_info'] = 'Bạn đã nhận voucher này rồi.';
        } else {
            $_SESSION['flash_error'] = 'Không thể nhận voucher. Có thể đã hết số lượng hoặc đã xảy ra lỗi.';
        }
    }

    header('Location: index.php?url=voucher');
    exit;
}

// 2. FLASH MESSAGE
foreach (['flash_success' => 'success', 'flash_info' => 'info', 'flash_error' => 'danger'] as $key => $type) {
    if (isset($_SESSION[$key])) {
        echo "<div class='alert alert-{$type}'>" . $_SESSION[$key] . "</div>";
        unset($_SESSION[$key]);
    }
}

// 3. LẤY DANH SÁCH VOUCHER
$all_vouchers = $VoucherModel->get_all_vouchers();
$received_voucher_ids = [];

if (isset($_SESSION['user']['id'])) {
    $received_voucher_ids = $VoucherModel->get_received_voucher_ids($_SESSION['user']['id']);
}
?>

<div class="container mt-5">
    <h2 class="mb-4">Danh sách Voucher</h2>
    <div class="row">
        <?php foreach ($all_vouchers as $voucher): ?>
            <div class="col-md-4">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($voucher['code']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($voucher['description']) ?></p>
                        <p class="card-text">
                            <strong>Giảm giá:</strong> <?= (int)$voucher['discount_percent'] ?>%<br>
                            <strong>Số lượng còn lại:</strong> <?= (int)$voucher['quantity'] ?><br>
                            <strong>Hiệu lực:</strong>
                            <?= $voucher['start_date'] ?> đến <?= $voucher['end_date'] ?>
                        </p>
                        <?php
                        $today = date('Y-m-d');
                        $is_valid = $today >= $voucher['start_date'] && $today <= $voucher['end_date'] && $voucher['quantity'] > 0;
                        $status_label = $is_valid
                            ? '<span class="badge badge-success">Còn hiệu lực</span>'
                            : '<span class="badge badge-secondary">Hết hạn</span>';
                        echo $status_label;
                        ?>

                        <hr>
                        <?php if (isset($_SESSION['user']['id'])): ?>
                            <?php $has_received = in_array($voucher['voucher_id'], $received_voucher_ids); ?>
                            <?php if ($has_received): ?>
                                <button class="btn btn-secondary" disabled>Đã nhận</button>
                            <?php elseif ($is_valid): ?>
                                <form method="post" action="">
                                    <input type="hidden" name="voucher_id" value="<?= $voucher['voucher_id'] ?>">
                                    <button type="submit" name="receive_voucher" class="btn btn-success">Nhận voucher</button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>Không thể nhận</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="index.php?url=dang-nhap" class="btn btn-warning">Đăng nhập để nhận</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
