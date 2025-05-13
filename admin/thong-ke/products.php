<?php
// Giả sử bạn đã khởi tạo $ProductModel ở trước đó (ví dụ từ index.php)
$statistics_category = $ProductModel->get_category_product_statistics();
?>

<!-- Thống kê danh mục -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Thống kê sản phẩm theo danh mục</h6>
        </div>

        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-dark">
                        <th scope="col">#</th>
                        <th scope="col">Tên danh mục</th> 
                        <th scope="col">Số lượng</th>   
                        <th scope="col">Giá thấp nhất</th>     
                        <th scope="col">Giá cao nhất</th>          
                        <th scope="col">Giá trung bình</th>     
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($statistics_category as $value) {
                        extract($value);
                        $i++;
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td style="min-width: 120px;"><?= htmlspecialchars($cate_name) ?></td>
                        <td><?= $count_products ?></td>
                        <td><?= number_format($min_price) ?>đ</td>
                        <td><?= number_format($max_price) ?>đ</td>
                        <td><?= number_format($avg_product) ?>đ</td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr class="text-dark">
                        <th>#</th>
                        <th>Tên danh mục</th> 
                        <th>Số lượng</th>   
                        <th>Giá thấp nhất</th>     
                        <th>Giá cao nhất</th>          
                        <th>Giá trung bình</th>     
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
