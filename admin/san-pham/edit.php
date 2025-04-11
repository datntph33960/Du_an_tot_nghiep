<?php
$error = [
    'name' => '',
    'image' => '',
    'quantity' => '',
    'price' => '',
    'sale_price' => '',
    'size' => '',
    'color' => '',
];

$list_categories = $CategoryModel->select_all_categories();
$list_sizes = $ProductModel->select_sizes();
$list_colors = $ProductModel->select_colors();

if (!isset($_GET['id'])) {
    header("Location: index.php?quanli=danh-sach-san-pham");
    exit;
}

$product_id = $_GET['id'];
$product = $ProductModel->select_product_by_id($product_id);
extract($product);

$selected_sizes = isset($product['sizes']) ? explode(',', $product['sizes']) : [];
$selected_colors = isset($product['colors']) ? explode(',', $product['colors']) : [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_product"])) {
    $name = trim($_POST["name"]);
    $category_id = intval($_POST["category_id"]);
    $quantity = intval($_POST["quantity"]);
    $price = floatval($_POST["price"]);
    $details = $_POST["details"] ?? '';
    $short_description = $_POST["short_description"] ?? '';
    $sizes = $_POST["size"] ?? [];
    $colors = $_POST["color"] ?? [];

    if (strlen($name) > 255) {
        $error['name'] = 'Tên sản phẩm tối đa 255 ký tự';
    }
    if ($price < 0) {
        $error['price'] = 'Giá bán thường phải lớn hơn 0';
    }
    if ($quantity < 0) {
        $error['quantity'] = 'Số lượng phải lớn hơn 0';
    }
    if (empty($sizes)) {
        $error['size'] = 'Vui lòng chọn ít nhất một kích thước';
    }
    if (empty($colors)) {
        $error['color'] = 'Vui lòng chọn ít nhất một màu';
    }

    if (!array_filter($error)) {
        $image = $product['image'];
        if (!empty($_FILES["image"]["name"])) {
            $target_dir = "../upload/";
            $image_name = basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $image_name;
            } else {
                $error['image'] = 'Tải ảnh thất bại, vui lòng thử lại';
            }
        }

        if (empty($error['image'])) {
            try {
                $size_string = implode(',', $sizes);
                $color_string = implode(',', $colors);
                $ProductModel->update_product($category_id, $name, $image, $quantity, $price, $details, $short_description, $size_string, $color_string, $product_id);


                setcookie('success_update', 'Cập nhật sản phẩm thành công', time() + 5, '/');
                header("Location: index.php?quanli=cap-nhat-san-pham&id=" . $product_id);
                exit;
            } catch (Exception $e) {
                echo 'Cập nhật sản phẩm thất bại: ' . $e->getMessage();
            }
        }
    }
}

$success = $_COOKIE['success_update'] ?? '';
$html_alert = $BaseModel->alert_error_success('', $success);
?>

<!-- Form Start -->
<div class="container-fluid pt-4">
    <form class="row g-4" method="post" enctype="multipart/form-data">
        <div class="col-sm-12 col-xl-9">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">
                    <a href="index.php?quanli=danh-sach-san-pham" class="link-not-hover">Sản phẩm</a> / Cập nhật sản phẩm
                </h6>
                <?=$html_alert?>

                <!-- Tên sản phẩm -->
                <label for="name">Tên sản phẩm</label>
                <div class="form-floating mb-3">
                    <input type="text" name="name" value="<?=htmlspecialchars($name)?>" class="form-control" id="name" placeholder="Tên sản phẩm">
                    <span class="text-danger"><?=$error['name']?></span>
                </div>

                <!-- Giá bán -->
                <label for="price">Giá bán thường (đ)</label>
                <div class="form-floating mb-3">
                    <input type="number" name="price" value="<?=htmlspecialchars($price)?>" class="form-control" id="price" placeholder="Giá bán thường">
                    <span class="text-danger"><?=$error['price']?></span>
                </div>

                <!-- Số lượng -->
                <label for="quantity">Số lượng</label>
                <div class="form-floating mb-3">
                    <input type="number" name="quantity" value="<?=htmlspecialchars($quantity)?>" class="form-control" id="quantity" placeholder="Số lượng">
                    <span class="text-danger"><?=$error['quantity']?></span>
                </div>

                <!-- Mô tả ngắn -->
                <label for="short_description">Mô tả ngắn</label>
                <div class="form-floating mb-3">
                    <textarea name="short_description" class="form-control" id="short_description"><?=htmlspecialchars($short_description)?></textarea>
                </div>

                <!-- Chi tiết sản phẩm -->
                <label for="product_details">Chi tiết sản phẩm</label>
                <div class="form-floating">
                    <textarea name="details" class="form-control" id="product_details" style="height: 300px;"><?=htmlspecialchars($details)?></textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-xl-3">
            <div class="bg-light rounded h-100 p-4">
                <!-- Hình ảnh -->
                <div class="mb-3">
                    <label for="formFileSm" class="form-label">Hình ảnh</label>
                    <input class="form-control form-control-sm" name="image" id="formFileSm" type="file" style="background-color: #fff">
                    <div class="my-2">
                        <img src="../upload/<?=htmlspecialchars($image)?>" alt="Ảnh sản phẩm" class="img-fluid" style="width: 100%;">
                    </div>
                    <span class="text-danger"><?=$error['image']?></span>
                </div>

                <!-- Danh mục -->
                <div class="form-floating mb-3">
                    <select name="category_id" class="form-select" id="categorySelect" required>
                        <?php foreach ($list_categories as $cate): ?>
                            <option value="<?=$cate['category_id']?>" <?=$cate['category_id'] == $category_id ? 'selected' : ''?>><?=$cate['name']?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="categorySelect">Chọn danh mục</label>
                </div>
                <!-- Kích thước -->
                <div class="mb-3">
                    <label class="form-label">Chọn kích thước</label>
                    <div class="form-check">
                        <?php foreach ($list_sizes as $size): ?>
                            <div>
                                <input class="form-check-input" type="checkbox" name="size[]" id="size_<?=$size?>" value="<?=$size?>" <?=in_array($size, $selected_sizes) ? 'checked' : ''?>>
                                <label class="form-check-label" for="size_<?=$size?>"><?=$size?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <span class="text-danger"><?=$error['size']?></span>
                </div>
                <!-- Màu sắc -->
                <div class="mb-3">
                    <label class="form-label">Chọn màu</label>
                    <div class="form-check">
                        <?php foreach ($list_colors as $color): ?>
                            <div>
                                <input class="form-check-input" type="checkbox" name="color[]" id="color_<?=$color?>" value="<?=$color?>" <?=in_array($color, $selected_colors) ? 'checked' : ''?>>
                                <label class="form-check-label" for="color_<?=$color?>"><?=$color?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <span class="text-danger"><?=$error['color']?></span>
                </div>
                <!-- Nút thao tác -->
                <h6 class="mb-4">
                    <input type="submit" name="update_product" value="Cập nhật" class="btn btn-custom">
                    <a href="index.php?quanli=thung-rac-san-pham&xoatam=<?=$product_id?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');" class="btn btn-custom">Xóa tạm</a>
                </h6>
            </div>
        </div>
    </form>
</div>

<style>
    .ck-editor__editable[role="textbox"]:first-child {
        min-height: 300px;
    }
    .ck-content .image {
        max-width: 80%;
        margin: 20px auto;
    }
</style>
