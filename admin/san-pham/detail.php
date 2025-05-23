<?php
if (!isset($_GET['id'])) {
    header("Location: index.php?quanli=danh-sach-san-pham");
    exit;
}

$product_id = intval($_GET['id']);
$product = $ProductModel->select_product_by_id($product_id);

if (!$product) {
    echo "<div class='alert alert-danger'>Không tìm thấy sản phẩm.</div>";
    exit;
}

$category = $CategoryModel->select_category_by_id($product['category_id']);
$sizes = isset($product['sizes']) ? explode(',', $product['sizes']) : [];
$colors = isset($product['colors']) ? explode(',', $product['colors']) : [];

$image_list = explode(',', $product['image']);
$first_image = trim($image_list[0] ?? '');
$second_image = trim($image_list[1] ?? $first_image);


?>

<div class="container-fluid pt-4 px-4">
  <div class="bg-light rounded p-4">
    <h4 class="mb-4">Chi tiết sản phẩm: <?= htmlspecialchars($product['name']) ?></h4>

    <div class="row">
      <div class="col-md-5">
        <div class="mb-3">
          <img id="main-product-img"
            src="/Du_an_tot_nghiep/upload/<?= htmlspecialchars(trim($image_list[0])) ?>"
            class="img-fluid rounded border"
            style="width: 270px; height: 300px; object-fit: cover;"
            alt="Ảnh sản phẩm">
        </div>
        <div class="d-flex flex-wrap gap-2">
          <?php foreach ($image_list as $img): ?>
            <img src="/Du_an_tot_nghiep/upload/<?= htmlspecialchars(trim($img)) ?>"
                 onclick="changeMainImage(this.src)"
                 class="img-thumbnail"
                 style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Cột phải: thông tin sản phẩm -->
      <div class="col-md-7">
        <table class="table table-bordered">
          <tr>
            <th>Tên sản phẩm</th>
            <td><?= htmlspecialchars($product['name']) ?></td>
          </tr>
          <tr>
            <th>Danh mục</th>
            <td><?= htmlspecialchars($category['name']) ?></td>
          </tr>
          <tr>
            <th>Giá bán</th>
            <td><?= number_format($product['price']) ?>₫</td>
          </tr>
          <tr>
            <th>Số lượng</th>
            <td><?= $product['quantity'] ?></td>
          </tr>
          <tr>
            <th>Kích thước</th>
            <td><?= implode(', ', $sizes) ?></td>
          </tr>
          <tr>
            <th>Màu sắc</th>
            <td><?= implode(', ', $colors) ?></td>
          </tr>
          <tr>
            <th>Mô tả ngắn</th>
            <td><?= nl2br(htmlspecialchars($product['short_description'])) ?></td>
          </tr>
          <tr>
            <th>Chi tiết sản phẩm</th>
            <td><?= nl2br(htmlspecialchars($product['details'])) ?></td>
          </tr>
        </table>

        <a href="index.php?quanli=cap-nhat-san-pham&id=<?= $product['product_id'] ?>" class="btn btn-warning">
          <i class="fa fa-edit"></i> Chỉnh sửa
        </a>
        <a href="index.php?quanli=danh-sach-san-pham" class="btn btn-secondary">Quay lại</a>
      </div>
    </div>
  </div>
</div>


<script>
function changeMainImage(src) {
  document.getElementById('main-product-img').src = src;
}
</script>
