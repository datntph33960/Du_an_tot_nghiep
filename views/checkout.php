<?php
$success = '';
$error = '';

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["checkout"])) {
        require_once __DIR__ . '/../config/config.php';
        $pdo = pdo_get_connection();

        // Lấy dữ liệu người dùng từ session
        if (!isset($_SESSION['user'])) throw new Exception("Bạn chưa đăng nhập.");
        $user_id = $_SESSION['user']['id'];

        // Dữ liệu đơn hàng
        $address = $_POST["address"];
        $phone = $_POST["phone"];
        $note = $_POST["note"];
        $voucher_discount = isset($_POST['voucher_discount']) ? (int)$_POST['voucher_discount'] : 0;

        // Dữ liệu sản phẩm
        $arr_product_id = $_POST["product_id"];
        $arr_quantity = $_POST["quantity"];
        $arr_price = $_POST["price"];
        $arr_sizes = $_POST["sizes"];
        $arr_colors = $_POST["colors"];

        // Tính tổng
        $total = 0;
        for ($i = 0; $i < count($arr_product_id); $i++) {
            $total += ($arr_price[$i] * $arr_quantity[$i]);
        }
        $total = max(0, $total - $voucher_discount);

        // Bắt đầu transaction
        $pdo->beginTransaction();

        // Bước 1: Insert vào orders
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, address, phone, note) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $total, $address, $phone, $note]);

        $order_id = $pdo->lastInsertId();
        if (!$order_id) throw new Exception("Không thể tạo đơn hàng.");

        // Bước 2: Insert từng chi tiết đơn hàng
        $stmtDetail = $pdo->prepare("INSERT INTO orderdetails (order_id, product_id, quantity, price, sizes, colors) VALUES (?, ?, ?, ?, ?, ?)");
        for ($i = 0; $i < count($arr_product_id); $i++) {
            $stmtDetail->execute([
                $order_id,
                $arr_product_id[$i],
                $arr_quantity[$i],
                $arr_price[$i],
                $arr_sizes[$i],
                $arr_colors[$i]
            ]);
        }

        // Xóa giỏ hàng
        $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        header("Location: index.php?url=cam-on");
        exit;
    }
} catch (Exception $e) {
    if (isset($pdo)) $pdo->rollBack();
    $error = "Đã xảy ra lỗi khi xử lý đơn hàng: " . htmlspecialchars($e->getMessage());
}
?>

<!-- Giao diện thanh toán -->
<?php if(isset($_SESSION['user'])):
    $user_id = $_SESSION['user']['id'];
    $list_carts = $CartModel->select_all_carts($user_id);
    $count_cart = count($CartModel->count_cart($user_id));
    $totalPayment = 0;
    foreach ($list_carts as $item) {
        $totalPayment += $item['product_price'] * $item['product_quantity'];
    }
?>
<div class="breadcrumb-option">
    <div class="container"><div class="row"><div class="col-lg-12">
        <div class="breadcrumb__links">
            <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
            <span>Thanh toán</span>
        </div>
    </div></div></div>
</div>

<section class="checkout spad">
    <div class="container">
        <form method="post" class="checkout__form">
            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <div class="row">
                <div class="col-lg-8">
                    <h5>CHI TIẾT THANH TOÁN</h5>
                    <div class="row">
                        <!-- Họ tên -->
                        <div class="col-lg-6">
                            <div class="checkout__form__input">
                                <p>Họ tên <span>*</span></p>
                                <input type="text" disabled value="<?= $_SESSION['user']['full_name'] ?>">
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="col-lg-6">
                            <div class="checkout__form__input">
                                <p>Email <span>*</span></p>
                                <input type="text" disabled value="<?= $_SESSION['user']['email'] ?>">
                            </div>
                        </div>
                        <!-- Địa chỉ -->
                        <div class="col-lg-12">
                            <div class="checkout__form__input">
                                <p>Địa chỉ <span>*</span></p>
                                <input type="text" name="address" value="<?= $_SESSION['user']['address'] ?>">
                            </div>
                        </div>
                        <!-- Số điện thoại -->
                        <div class="col-lg-12">
                            <div class="checkout__form__input">
                                <p>Số điện thoại <span>*</span></p>
                                <input type="text" name="phone" value="<?= $_SESSION['user']['phone'] ?>">
                            </div>
                        </div>
                        <!-- Ghi chú -->
                        <div class="col-lg-12">
                            <div class="checkout__form__input">
                                <p>Ghi chú</p>
                                <input type="text" name="note">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bảng đơn hàng -->
                <div class="col-lg-4">
                    <div class="checkout__order">
                        <h5>ĐƠN HÀNG</h5>
                        <div class="checkout__order__product">
                            <ul>
                                <li><span class="top__text">Sản phẩm</span><span class="top__text__right">Tổng</span></li>
                                <?php foreach($list_carts as $i => $item): ?>
                                    <li>
                                        <input type="hidden" name="product_id[]" value="<?= $item['product_id'] ?>">
                                        <input type="hidden" name="quantity[]" value="<?= $item['product_quantity'] ?>">
                                        <input type="hidden" name="price[]" value="<?= $item['product_price'] ?>">
                                        <input type="hidden" name="sizes[]" value="<?= htmlspecialchars($item['product_size']) ?>">
                                        <input type="hidden" name="colors[]" value="<?= htmlspecialchars($item['product_color']) ?>">

                                        <?= $i + 1 ?>. <?= $item['product_name'] ?>
                                        <a class="text-primary">x<?= $item['product_quantity'] ?></a>
                                        <span><?= number_format($item['product_price'] * $item['product_quantity']) ?>đ</span>
                                        <span class="text-muted">(Kích thước: <?= htmlspecialchars($item['product_size']) ?>, Màu sắc: <?= htmlspecialchars($item['product_color']) ?>)</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Mã giảm giá -->
                        <div class="checkout__form__input">
                            <p>Mã giảm giá</p>
                            <div class="d-flex">
                                <input type="text" id="voucher_code" class="form-control mr-2" placeholder="Nhập mã">
                                <button type="button" class="btn btn-sm btn-primary" onclick="applyVoucher()">Áp dụng</button>
                            </div>
                            <small id="voucher_message" class="text-success mt-1"></small>
                            <input type="hidden" name="voucher_discount" id="voucher_discount" value="0">
                        </div>

                        <div class="checkout__order__total">
                            <ul>
                                <li>Giảm giá <span id="discount_amount">0đ</span></li>
                                <li>Thành tiền <span id="final_amount"><?= number_format($totalPayment) ?>đ</span></li>
                                <input type="hidden" name="total_checkout" value="<?= $totalPayment ?>">
                            </ul>
                        </div>

                        <!-- Nút đặt hàng -->
                        <div class="checkout__order__widget text-center text-dark mb-2">Thanh toán khi nhận hàng</div>
                        <?php if($count_cart > 0): ?>
                            <button type="submit" name="checkout" class="site-btn">ĐẶT HÀNG</button>
                        <?php else: ?>
                            <div class="text-primary text-center mb-2">Chưa có sản phẩm trong giỏ</div>
                            <a href="index.php?url=cua-hang" class="site-btn">Xem sản phẩm</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function applyVoucher() {
    const code = document.getElementById('voucher_code').value.trim().toUpperCase();
    const total = <?= $totalPayment ?>;
    let discount = 0;
    const vouchers = {
        'GIAM10': { type: 'percent', value: 10 },
        'GIAM50K': { type: 'amount', value: 50000 },
    };

    let message = '';
    if (vouchers[code]) {
        const v = vouchers[code];
        discount = (v.type === 'percent') ? Math.floor(total * v.value / 100) : v.value;
        message = `Áp dụng mã ${code} - Giảm ${discount.toLocaleString()}đ`;
    } else {
        message = 'Mã không hợp lệ hoặc đã hết hạn.';
    }

    const finalTotal = Math.max(0, total - discount);
    document.getElementById('voucher_discount').value = discount;
    document.querySelector('input[name="total_checkout"]').value = finalTotal;
    document.getElementById('discount_amount').textContent = '-' + discount.toLocaleString() + 'đ';
    document.getElementById('final_amount').textContent = finalTotal.toLocaleString() + 'đ';
    document.getElementById('voucher_message').textContent = message;
}
</script>

<?php else: ?>
<!-- Nếu chưa đăng nhập -->
<div class="container mt-5">
    <div class="text-center">
        <h4>Bạn cần đăng nhập để thanh toán</h4>
        <a href="index.php?url=dang-nhap" class="btn btn-primary">Đăng nhập</a>
        <a href="index.php" class="btn btn-secondary">Trang chủ</a>
    </div>
</div>
<?php endif; ?>
