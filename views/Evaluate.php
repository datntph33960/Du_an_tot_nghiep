<?php
$order_id = $_GET['order_id'];
$user_id = $_SESSION['user']['id'];
$list_products = $OrderModel->select_orderdetails_and_products($order_id);
?>

<div class="container mt-5 mb-5">
    <h3>Đánh giá sản phẩm</h3>
    <form action="index.php?url=luu-danh-gia&order_id=<?= $order_id ?>" method="POST">
        <input type="hidden" name="order_id" value="<?=$order_id?>">
        
        <?php foreach($list_products as $product): ?>
            <hr>
            <div class="mb-3">
                <strong><?=$product['product_name']?></strong>
                <input type="hidden" name="product_ids[]" value="<?=$product['product_id']?>">

                <div class="mt-2">
                    <label>Đánh giá sao:</label>
                    <select name="ratings[]" class="form-select" required>
                        <option value="">--Chọn số sao--</option>
                        <option value="1">1 sao</option>
                        <option value="2">2 sao</option>
                        <option value="3">3 sao</option>
                        <option value="4">4 sao</option>
                        <option value="5">5 sao</option>
                    </select>
                </div>

                <div class="mt-2">
                    <label>Nội dung đánh giá:</label>
                    <textarea name="comments[]" rows="3" class="form-control" required></textarea>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
    </form>
</div>
