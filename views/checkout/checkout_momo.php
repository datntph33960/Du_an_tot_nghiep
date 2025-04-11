<!-- Breadcrumb Begin -->
<?php

$error = array(
    'email' => '',
    'fullname' => '',
    'username' => '',
    'password' => '',
    'password_confirm' => '',
    'phone' => '',
    'address' => '',     
);
$temp = array(
    'address' => '',
    'phone' => '',
    'note' => '',
);
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["payUrl"])) {
        // Table orders
        $user_id = $_POST["user_id"];
        $total = $_POST["total_checkout"];
        $address = $_POST["address"];
        $phone = $_POST["phone"];
        $note = $_POST["note"];

        // Validate các trường
        if (empty($_SESSION['user']['full_name'])) {
            $error['fullname'] = 'Họ tên không được để trống';
        }

        if (empty($_SESSION['user']['email'])) {
            $error['email'] = 'Email không được để trống';
        } elseif (!filter_var($_SESSION['user']['email'], FILTER_VALIDATE_EMAIL)) {
            $error['email'] = 'Email không đúng định dạng';
        }

        if (empty($address)) {
            $error['address'] = 'Địa chỉ không được để trống';
        } elseif (strlen($address) > 255) {
            $error['address'] = 'Địa chỉ tối đa 255 ký tự';
        }

        if (empty($phone)) {
            $error['phone'] = 'Số điện thoại không được để trống';
        } elseif (!preg_match('/^(03|05|07|08|09)\d{8}$/', $phone)) {
            $error['phone'] = 'Số điện thoại không đúng định dạng.';
        }

        // Table orderdetails
        $arr_product_id = $_POST["product_id"];
        $arr_quantity = $_POST["quantity"];
        $arr_price = $_POST["price"];
        $arr_size = $_POST["size"];
        $arr_color = $_POST["color"];

        if (empty(array_filter($error))) {
            // Thanh toán MOMO
            include_once "views/checkout/momo.php";

            // Sau khi thanh toán momo thành công
            $OrderModel->insert_orders($user_id, $total, $address, $phone, $note);
            $result_select = $OrderModel->select_order_id();
            $order_id = $result_select['order_id'];

            include_once "views/checkout/send-mail-order.php";

            if (!empty($order_id)) {
                for ($i = 0; $i < count($arr_product_id); $i++) {
                    $product_id = $arr_product_id[$i];
                    $quantity = $arr_quantity[$i];
                    $price = $arr_price[$i];
                    $size = $arr_size[$i];
                    $color = $arr_color[$i];

                    $OrderModel->insert_orderdetails($order_id, $product_id, $quantity, $price, $size, $color);
                }
                $OrderModel->delete_cart_by_user_id($user_id);
            }
        } else {
            $temp['address'] = $address;
            $temp['phone'] = $phone;
            $temp['note'] = $note;
        }
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
    echo $error_message;
}
?>

<?php 
if (isset($_SESSION['user'])) { 
    $user_id = $_SESSION['user']['id'];
    $list_carts = $CartModel->select_all_carts($user_id);
    $count_cart = count($CartModel->count_cart($user_id));
?>
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                        <span>Thanh toán</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Checkout Section Begin -->
    <section class="checkout spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h6 class="coupon__link"><span class="icon_tag_alt mr-1"></span>Tiến hành thanh toán đơn hàng <a class="text-primary" href="gio-hang">Trở lại giỏ hàng</a> </h6>
                </div>
            </div>
            <form action="" method="post" class="checkout__form">
                <div class="row">
                    <div class="col-lg-8">
                        <h5>CHI TIẾT THANH TOÁN</h5>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Họ tên <span>*</span></p>
                                    <input type="text" disabled name="full_name" value="<?= $_SESSION['user']['full_name'] ?>">
                                    <span class="text-danger error"><?=$error['fullname']?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Email <span>*</span></p>
                                    <input disabled type="text" name="email" value="<?= $_SESSION['user']['email'] ?>">
                                    <span class="text-danger error"><?=$error['email']?></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="checkout__form__input">
                                    <p>Địa chỉ <span>*</span></p>
                                    <input class="mb-0" type="text" name="address" value="<?=$temp['address']?>">
                                    <span class="text-danger error"><?=$error['address']?></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="checkout__form__input">
                                    <p>Số điện thoại <span>*</span></p>
                                    <input class="mb-0" type="text" name="phone" value="<?=$temp['phone']?>">
                                    <span class="text-danger error"><?=$error['phone']?></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="checkout__form__input">
                                    <p>Ghi chú<span></span></p>
                                    <input type="text" value="<?=$temp['note']?>" name="note">
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="checkout__order">
                            <h5>ĐƠN HÀNG</h5>
                            <div class="checkout__order__product">
                                <ul>
                                    <li>
                                        <span class="top__text">Sản phẩm</span>
                                        <span class="top__text__right">Tổng</span>
                                    </li>
                                    <?php
                                        $i = 0;
                                        $totalPayment = 0;
                                        foreach ($list_carts as $value) {
                                            extract($value);
                                            $totalPrice = ($product_price * $product_quantity);
                                            $totalPayment += $totalPrice;
                                            $i++;
                                    ?>
                                    <li>
                                        <input type="hidden" name="user_id" value="<?=$user_id?>">
                                        <input type="hidden" name="total_checkout" value="<?=$totalPayment?>">
                                        <input type="hidden" name="product_id[]" value="<?=$product_id?>">
                                        <input type="hidden" name="quantity[]" value="<?=$product_quantity?>">
                                        <input type="hidden" name="price[]" value="<?=$product_price?>">    
                                        <input type="hidden" name="size[]" value="<?=$product_size?>"> 
                                        <input type="hidden" name="color[]" value="<?=$product_color?>">

                                        <?=$i?>.
                                        <?=$product_name?>
                                        <a class="text-primary">x<?=$product_quantity?></a>
                                        <span><?=number_format($totalPrice)?>đ</span>
                                        <br>
                                        <small>Size: <?=$product_size?></small>
                                        <small class="ml-2">Màu: <?=$product_color?></small>
                                    </li>
                                    <?php
                                        }
                                    ?>
                                </ul>
                            </div>
                            <div class="checkout__order__total">
                                <ul>
                                    <li>Tổng <span><?=number_format($totalPayment)?>đ</span></li>
                                </ul>
                            </div>
                            
                            <?php if ($count_cart > 0) { ?>
                            <div class="checkout__order__widget text-center mb-2" style="color: #111;">                        
                                Hình thức: Thanh toán MOMO
                            </div>   
                            <button type="button" class="site-btn btn-momo" data-toggle="modal" data-target="#thanhtoan">
                                THANH TOÁN MOMO
                            </button>
                            <div class="modal fade" id="thanhtoan" tabindex="-1" role="dialog" aria-labelledby="thanhtoan" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4>Xác nhận đặt hàng</h4>
                                        </div>
                                        <div class="modal-body text-dark">
                                            Bạn có muốn tiếp tục đặt hàng ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                            <button type="submit" name="payUrl" class="btn btn-primary">Xác nhận</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="checkout__order__widget text-center text-primary mb-2">                        
                                Chưa có sản phẩm trong giỏ hàng
                            </div> 
                            <a href="cua-hang" class="site-btn btn">Xem sản phẩm</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- Checkout Section End -->
<?php } else { ?>
    <div class="row" style="margin-bottom: 400px;">
        <div class="col-lg-12 col-md-12">
            <div class="container-fluid mt-5">
                <div class="row rounded justify-content-center mx-0 pt-5">
                    <div class="col-md-6 text-center">
                        <h4 class="mb-4">Vui lòng đăng nhập để có thể thanh toán</h4>
                        <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?url=dang-nhap">Đăng nhập</a>
                        <a class="btn btn-secondary rounded-pill py-3 px-5" href="index.php">Trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<style>
    .cart__btn a:hover {
        background-color: #0A68FF;
        color: #fff;
        transition: 0.2s;
    }

    .checkout__form .checkout__form__input input {
        color: #000000;
    }

    .checkout__form .checkout__form__input input:focus {
        border: 1px solid #999999;
    }

    .error {
        display: inline-block;
        height: 20px;
        font-size: 15px;
    }

    .btn-momo {
        background-color: #D82D8B;
        color: #fff;
    }

    .btn-momo:hover {
        opacity: 0.8;
        color: #fff;
    }

    .checkout__order__product small {
        font-size: 12px;
        color: #555;
    }
</style>
