<?php
    if (isset($_GET['id']) && $_GET['id'] > 0) {
        $order_id = $_GET['id'];
    }

    // Lấy thông tin đơn hàng, bao gồm product_name từ orderdetails.name
    $order_details = $OrderModel->getFullOrderInformation($order_id);
    foreach ($order_details as $value) {
        extract($value);
    }

    // Trạng thái đơn hàng
    $order_status = 'Chưa xác nhận';
    if ($status == 0) {
        $order_status = 'Đã hủy';
    } elseif ($status == 2) {
        $order_status = 'Đã xác nhận';
    } elseif ($status == 3) {
        $order_status = 'Đang giao';
    } elseif ($status == 4) {
        $order_status = 'Giao thành công';
    }

    $date_formated = $BaseModel->date_format($order_date, '');

    // Hàm getStatusName: Dùng cho select dropdown trạng thái
    function getStatusName($statusValue) {
        switch ($statusValue) {
            case 0:
                return 'Đã hủy';
            case 1:
                return 'Chờ xác nhận';
            case 2:
                return 'Đã xác nhận';
            case 3:
                return 'Đang giao';
            case 4:
                return 'Giao thành công';
            default:
                return 'Không xác định';
        }
    }

    // Lấy phương thức thanh toán
    $payment_method_id = $OrderModel->get_payment_method($order_id);
    $sql = "SELECT method_name FROM payment_methods WHERE id = ?";
    $payment_method = pdo_query_one($sql, $payment_method_id);
    $method_name = $payment_method['method_name'];

    // Cập nhật trạng thái đơn hàng
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status_order"])) {
        $new_status = (int)$_POST["status"];
        $order_id = $_POST["order_id"];
    
        $current_order = $OrderModel->getFullOrderInformation($order_id);
        $current_status = (int)$current_order[0]['status'];
        
        if ($current_status == 0) {
            echo "<script>alert('Đơn hàng đã bị huỷ, không thể cập nhật trạng thái!');</script>";
        } elseif ($new_status == $current_status + 1) {
            $OrderModel->update_status_order($new_status, $order_id);
            echo "<script>alert('Cập nhật trạng thái thành công!'); window.location.href = 'index.php?quanli=cap-nhat-don-hang&id=$order_id';</script>";
            exit();
        } elseif ($new_status == $current_status) {
            echo "<script>alert('Đơn hàng đã ở trạng thái này!');</script>";
        } elseif ($new_status < $current_status) {
            echo "<script>alert('Không thể cập nhật lùi trạng thái!');</script>";
        } else {
            echo "<script>alert('Chỉ được phép cập nhật từng bước một!');</script>";
        }
    }
    $imageArray = explode(',', $product_image);
    $firstImage = $imageArray[0];
?>


<div class="container pt-4">
    <article class="card">
        <header class="card-header text-dark">
            <h6>
                <a href="index.php?quanli=danh-sach-don-hang" class="link-not-hover">Đơn hàng</a>
                / Chi tiết đơn hàng
            </h6>
        </header>
        <div class="card-body mt-2">
            <ul class="row">
                <?php foreach ($order_details as $value): extract($value); ?>
                <li class="col-md-4">
                    <figure class="itemside mb-3">
                        <div class="aside"><img src="/Du_an_tot_nghiep/upload/<?=$firstImage?>" class="img-sm border"></div>
                        <figcaption class="info align-self-center">
                            <!-- product_name là từ orderdetails.name -->
                            <p class="title"><?=$product_name?><br></p>
                            <small class="text-muted">Phân loại: Size <strong><?=$sizes?></strong>, Màu <strong><?=$colors?></strong></small><br>
                            <span class="text-danger"><?=number_format($price)?>₫</span> <span>x<?=$quantity?></span>
                        </figcaption>
                    </figure>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="row">
                <div class="col-lg-6">
                    <div class="bg-custom rounded border" style="background-color: #ffff;">
                        <div class="p-4">
                            <h6 class="mb-4">
                                Trạng thái đơn hàng: <span class="text-danger"><?=$order_status?></span>
                            </h6>
                            <form action="" method="post">
                                <div class="form-floating mb-3">
                                    <select name="status" class="form-select" id="floatingSelect">
                                        <?php foreach ([0, 1, 2, 3, 4] as $option_value): ?>
                                            <option value="<?=$option_value?>" <?=($option_value == $status) ? 'selected' : ''?>>
                                                <?=getStatusName($option_value)?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingSelect">Trạng thái</label>
                                </div>
                                <input type="hidden" name="order_id" value="<?=$order_id?>">
                                <h6 class="mb-4">
                                    <input type="submit" name="update_status_order" value="Cập nhật" class="btn btn-custom">
                                </h6>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card mb-4 bg-custom" style="background-color: #ffff;">
                        <div class="card-body text-dark">
                            <?php
                            $rows = [
                                "Tên người đặt" => $full_name,
                                "SĐT người nhận" => $order_phone,
                                "Tên người nhận" => $receiver_name,
                                "SĐT người nhận" => $receiver_phone,
                                "Địa chỉ giao hàng" => $receiver_address,
                                "Phương thức thanh toán" => $method_name,
                                "Thời gian" => $date_formated,
                                "Tổng tiền hàng" => number_format($total) . '₫',
                                "Phí vận chuyển" => "Miễn phí",
                                "Ghi chú" => $note
                            ];
                            foreach ($rows as $label => $val): ?>
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <p class="mb-0 text-right"><?=$label?></p>
                                    </div>
                                    <div class="col-sm-8">
                                        <p class="mb-0 text-right"><?=$val?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <p class="mb-0 text-right">Thành tiền</p>
                                </div>
                                <div class="col-sm-8">
                                    <p style="font-size: 1.5rem;" class="mb-0 text-right text-danger fw-500"><?=number_format($total)?>₫</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>