<?php

$success = '';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
    $product_id = $_POST["product_id"];
    $user_id = $_POST["user_id"];
    $product_name = $_POST["name"];
    $product_price = $_POST["price"];
    $product_quantity = $_POST["product_quantity"];
    $product_image = $_POST["image"];
    $product_size = $_POST["size"]; // Lấy kích thước được chọn
    $product_color = $_POST["color"]; // Lấy màu sắc được chọn

    // Đếm số lượng sản phẩm trong giỏ hàng
    $product = $CartModel->select_cart_by_id($product_id, $user_id);
    
    // Kiểm tra xem có sản phẩm trong giỏ hàng hay không
    if ($product && is_array($product)) {
        // Số lượng mới = số lượng hiện tại + số lượng vừa thêm
        $current_quantity = $product['product_quantity'];
        $new_quantity = $current_quantity + $product_quantity;

        // Cập nhật số lượng
        $CartModel->update_cart($new_quantity, $product_id, $user_id, $product_size, $product_color);
        $success .= 'Đã cập nhật số lượng cho sản phẩm: ' . $product_name;
    } else {
        // Chèn sản phẩm vào giỏ hàng
        $CartModel->insert_cart($product_id, $user_id, $product_name, $product_price, $product_quantity, $product_image, $product_size, $product_color);
        $success = "Đã thêm sản phẩm vào giỏ hàng";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_cart"]) && isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $product_id = $_POST["product_id"];
    $new_quantity = $_POST["quantity"];
    $index = 0; // Đếm số sản phẩm xóa

    for ($i = 0; $i < count($product_id); $i++) {
        $id = $product_id[$i];
        $quantity = $new_quantity[$i];
        
        if ($quantity <= 0) {
            // Nếu số lượng >=0 xóa sản phẩm trong giỏ hàng     
            $CartModel->delete_product_in_cart($id, $user_id);
            $index += 1;
        } elseif ($quantity > 0) {
            $CartModel->update_cart($quantity, $id, $user_id);
        }
    }
    
    if ($index > 0) {
        $success = 'Đã xóa ' . $index . ' sản phẩm ra khỏi giỏ hàng';
    } else {
        $success = 'Cập nhật thành công';
    }
}

if (isset($_GET['xoa'])) {
    $cart_id = $_GET['xoa'];
    $result = $CartModel->delete_cart_by_id($cart_id);
    $success = 'Đã xóa 1 sản phẩm';
}
?>

<?php
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $list_carts = $CartModel->select_all_carts($user_id);
    $count_carts = count($CartModel->count_cart($user_id));
}
?>

<?php if (isset($_SESSION['user'])) { ?>
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                        <a href="index.php?url=cua-hang"> Cửa hàng</a>
                        <span>Giỏ hàng</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Kiểm tra giỏ hàng có sản phẩm không -->
    <?php if (count($list_carts) > 0) { ?>
    <!-- Shop Cart Section Begin -->
    <section class="shop-cart spad">
        <div class="container">
            <form action="" method="post">
            <div class="row">
                                                  <div class="col-lg-12">
                    <div class="shop__cart__table">
                        <?=$alert = $BaseModel->alert_error_success($error, $success)?>
                        <table>
                            <thead>
                                <tr>
                                    <th>SẢN PHẨM</th>
                                    <th>GIÁ</th>
                                    <th>SỐ LƯỢNG</th>
                                    <th>KÍCH THƯỚC</th>
                                    <th>MÀU SẮC</th>
                                    <th>TỔNG</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalPayment = 0;
                                foreach ($list_carts as $value) {
                                    extract($value);
                                    $totalPrice = ($product_price * $product_quantity);
                                    // Tổng thanh toán
                                    $totalPayment += $totalPrice;
                                    // Lấy id danh mục của sản phẩm để hiện thị đường dẫn sang trang ctsp
                                    $product = $ProductModel->select_cate_in_product($product_id);
                                ?>
                                <tr>
                                    <td class="cart__product__item">
                                        <a href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$product['category_id']?>">
                                            <img src="upload/<?=$product_image?>" alt="">
                                        </a>
                                        <div class="cart__product__item__title">
                                            <h6 class="text-truncate-1">
                                                <a href="chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$product['category_id']?>" class="text-dark">
                                                    <?=$product_name?>
                                                </a>
                                            </h6>
                                            <div class="rating">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cart__price"><?=number_format($product_price)?>đ</td>
                                    <input type="hidden" name="product_id[]" value="<?=$product_id?>">
                                    <td class="cart__quantity">
                                        <div class="input-group float-left">
                                            <div class="input-next-cart d-flex "> 
                                                <input type="button" value="-" class="button-minus" data-field="quantity">
                                                <input type="number" step="1" min="0" value="<?=$product_quantity?>" name="quantity[]" class="quantity-field-cart" readonly>
                                                <input type="button" value="+" class="button-plus" data-field="quantity">
                                            </div>                                           
                                        </div>
                                    </td>
                                    <td class="cart__size">
                                        <p><?=$size?></p> <!-- Hiển thị kích thước -->
                                    </td>
                                    <td class="cart__color">
                                        <p><?=$color?></p> <!-- Hiển thị màu sắc -->
                                    </td>
                                    <td class="cart__total"><?=number_format($totalPrice)?>đ</td>
                                    <td class="cart__close">
                                        <a href="index.php?url=gio-hang&xoa=<?=$cart_id?>">
                                            <span class="icon_close"></span>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="cart__btn">
                        <a href="index.php?url=cua-hang">Tiếp tục mua sắm</a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="cart__btn update__btn">
                        <button name="update_cart" type="submit"><span class="icon_loading"></span>Cập nhật giỏ hàng</button>
                    </div>
                </div>
            </div>
            </form>
            <div class="row">
                <div class="col-lg-6">
                    <!-- Có thể thêm mục nhập giảm giá ở đây nếu cần -->
                </div>
                <div class="col-lg-4 offset-lg-2">
                    <div class="cart__total__procced">
                        <h6>Tổng tiền</h6>
                        <ul>

                            <li>Tổng số tiền <span><?=number_format($totalPayment)?>đ</span></li>
                        </ul>
                        <a href="index.php?url=checkout" class="primary-btn">Thanh toán</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop Cart Section End -->
    <?php } else { ?>
        <section class="shop-cart spad">
            <div class="container">
                <h3>Giỏ hàng của bạn trống</h3>
                <a href="index.php?url=cua-hang" class="primary-btn">Tiếp tục mua sắm</a>
            </div>
        </section>
    <?php } ?>
<?php } else {
    // Nếu chưa đăng nhập, hiển thị thông báo
    echo '<div class="alert alert-warning">Vui lòng đăng nhập để xem giỏ hàng.</div>';
} ?>
