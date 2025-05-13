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
    $product_size = $_POST["size"]; // Lấy thông tin kích thước
    $product_color = $_POST["color"]; // Lấy thông tin màu sắc

    // Đếm số lượng sản phẩm trong giỏ hàng
    $product = $CartModel->select_cart_by_id($product_id, $user_id, $product_size, $product_color);

    
    if($product && is_array($product)) {
        $current_quantity = $product['product_quantity'];
        $new_quantity = $current_quantity + $product_quantity;
        $CartModel->update_cart($new_quantity, $product_id, $user_id, $product_size, $product_color);
        $success .= 'Đã cập nhật số lượng cho sản phẩm: ' . $product_name;
    } else {
        $CartModel->insert_cart($product_id, $user_id, $product_name, $product_price, $product_quantity, $product_image, $product_size, $product_color);
        $success = "Đã thêm sản phẩm vào giỏ hàng";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_cart"]) && isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $product_id = $_POST["product_id"];
    $new_quantity = $_POST["quantity"];
    $colors = $_POST["color"];
    $sizes = $_POST["size"];
    $index = 0;

    for ($i = 0; $i < count($product_id); $i++) {
        $id = $product_id[$i];
        $quantity = $new_quantity[$i];
        $size = $sizes[$i];
        $color = $colors[$i];
        $current = $CartModel->select_cart_by_id($id, $user_id, $size, $color);
        $old_quantity = $current['product_quantity'];
    
        $change = $quantity - $old_quantity;
    
        if ($quantity <= 0) {
            $CartModel->delete_product_in_cart($id, $user_id, $size, $color);
            $ProductModel->increase_stock($id, $size, $color, $old_quantity);
            $index += 1;
        } else {
            $CartModel->update_cart($quantity, $id, $user_id, $size, $color);
        }
    }
    $success = "Đã cập nhật giỏ hàng thành công!";
}

if(isset($_GET['xoa'])) {
    $cart_id = $_GET['xoa'];
    $result = $CartModel->delete_cart_by_id($cart_id);
    $success = 'Đã xóa 1 sản phẩm';
}

if(isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $list_carts = $CartModel->select_all_carts($user_id);
    $count_carts = count($CartModel->count_cart($user_id));
}

?>

<?php if(isset($_SESSION['user'])) { ?>
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
<?php if(count($list_carts) > 0) { ?>
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
                                <th>CHỌN</th>
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
                                $totalPayment += $totalPrice;

                                // Lấy id danh mục của sản phẩm để hiện thị đường dẫn sang trang ctsp
                                $product = $ProductModel->select_cate_in_product($product_id);
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_products[]" value="<?=$cart_id?>" class="select-product"
                                        data-price="<?=$product_price?>" data-quantity="<?=$product_quantity?>">
                                </td>
                                <td class="cart__product__item">
                                    <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$product['category_id']?>">
                                        <img src="upload/<?=$product_image?>" alt="">
                                    </a>
                                    <div class="cart__product__item__title">
                                        <h6 class="text-truncate-1">
                                            <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$product['category_id']?>" class="text-dark">
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
                                <input type="hidden" name="color[]" value="<?=$product_color?>">
                                <input type="hidden" name="size[]" value="<?=$product_size?>">

                                <td class="cart__quantity">
                                    <div class="input-group float-left">
                                        <div class="input-next-cart d-flex"> 
                                            <input type="button" value="-" class="button-minus" data-field="quantity">
                                            <input type="number" step="1" max="" value="<?=$product_quantity?>" name="quantity[]" class="quantity-field-cart">
                                            <input type="button" value="+" class="button-plus" data-field="quantity">
                                        </div>                                           
                                    </div>
                                </td>
                                <td class="cart__size"><?=$product_size?></td> <!-- Thêm kích thước -->
                                <td class="cart__color"><?=$product_color?></td> <!-- Thêm màu sắc -->
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
                <!-- Mã giảm giá là tùy chọn, bỏ qua nếu không cần -->
            </div>
            <div class="col-lg-4 offset-lg-2">
                <div class="cart__total__procced">
                    <h6>Tổng tiền</h6>
                    <ul>
                        <li>Số lượng đã chọn: <span id="total-selected">0 sản phẩm</span></li>
                        <li>Tổng tiền: <span id="total-price">0đ</span></li>
                    </ul>
                    <form action="index.php?url=thanh-toan" method="post" id="checkout-form">
                        <input type="hidden" name="selected_ids" id="selected_ids">
                        <button type="submit" class="btn primary-btn" style="width: 100%; padding: 15px">THANH TOÁN COD</button>
                    </form>
                    <form action="index.php?url=thanh-toan-momo" method="post" id="checkout-form-momo">
                        <input type="hidden" name="selected_ids" id="selected_ids_momo">
                        <button type="submit" class="btn btn-momo primary-btn mt-3" style="width: 100%; padding: 15px">THANH TOÁN MOMO</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop Cart Section End -->
<?php } else { ?>
    <div class="row" style="margin-bottom: 400px;">
        <div class="col-lg-12 col-md-12">
            <div class="container-fluid mt-5">
                <div class="row rounded justify-content-center mx-0 pt-5">
                    <div class="col-md-6 text-center">
                        <h4 class="mb-4">Chưa có sản phẩm nào trong giỏ hàng</h4>
                        <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?url=cua-hang">Xem sản phẩm</a>
                        <a class="btn btn-secondary rounded-pill py-3 px-5" href="index.php">Trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php } else { ?>
    <div class="row" style="margin-bottom: 400px;">
        <div class="col-lg-12 col-md-12">
            <div class="container-fluid mt-5">
                <div class="row rounded justify-content-center mx-0 pt-5">
                    <div class="col-md-6 text-center">
                        <h4 class="mb-4">Vui lòng đăng nhập để có thể mua hàng</h4>
                        <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?url=dang-nhap">Đăng nhập</a>
                        <a class="btn btn-secondary rounded-pill py-3 px-5" href="index.php">Trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.select-product');
    const totalSelectedElement = document.getElementById('total-selected');
    const totalPriceElement = document.getElementById('total-price');
    const codForm = document.getElementById('checkout-form');
    const momoForm = document.getElementById('checkout-form-momo');
    const hiddenInput = document.getElementById('selected_ids');
    const hiddenInputMomo = document.getElementById('selected_ids_momo');

    function updateTotals() {
        let totalQty = 0;
        let totalPrice = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                const qty = parseInt(cb.dataset.quantity);
                const price = parseFloat(cb.dataset.price);
                totalQty += qty;
                totalPrice += qty * price;
            }
        });
        totalSelectedElement.textContent = totalQty + ' sản phẩm';
        totalPriceElement.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + 'đ';
    }

    function collectSelectedIds() {
        const selected = [];
        checkboxes.forEach(cb => {
            if (cb.checked) {
                selected.push(cb.value);
            }
        });
        return selected.join(',');
    }

    if (codForm) {
        codForm.addEventListener('submit', function(e) {
            const selectedIds = collectSelectedIds();
            if (!selectedIds) {
                alert("Vui lòng chọn sản phẩm để thanh toán.");
                e.preventDefault();
                return;
            }
            hiddenInput.value = selectedIds;
        });
    }

    if (momoForm) {
        momoForm.addEventListener('submit', function(e) {
            const selectedIds = collectSelectedIds();
            if (!selectedIds) {
                alert("Vui lòng chọn sản phẩm để thanh toán.");
                e.preventDefault();
                return;
            }
            hiddenInputMomo.value = selectedIds;
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateTotals);
    });

    updateTotals();
});
</script>



<style>
    .cart__btn a:hover {
        background-color: #0A68FF;
        color: #fff;
        transition: 0.2s;
    }

    .cart__btn button:hover {
        background-color: #0A68FF;
        color: #fff;
        transition: 0.2s;
    }

    .btn-momo {
        background-color: #D82D8B;
        color: #fff;
    }

    .btn-momo:hover {
        opacity: 0.8;
        color: #fff;
    }
</style>