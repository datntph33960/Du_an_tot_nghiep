<?php
$error = array(
    'name' => '',
    'image' => '',
    'quantity' => '',
    'price' => '',
    'sale_price' => '',
    'size' => '',
    'color' => '',   
);

$list_categories = $CategoryModel->select_all_categories();
$list_sizes = $ProductModel->select_sizes(); // Assuming there's a method for getting sizes
$list_colors = $ProductModel->select_colors(); // Assuming there's a method for getting colors
$list_products = $ProductModel->select_products();

if(isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $product = $ProductModel->select_product_by_id($product_id);
    extract($product);

    // Assuming that product sizes and colors are stored in separate tables (many-to-many relationships)
    $selected_sizes = $ProductModel->get_product_sizes($product_id);
    $selected_colors = $ProductModel->get_product_colors($product_id);
} else {
    header("Location: index.php?quanli=danh-sach-san-pham");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_product"])) {
    $name = trim($_POST["name"]);
    $category_id = $_POST["category_id"];
    $image = $_FILES["image"]['name'];

    $quantity = $_POST["quantity"];
    $price = $_POST["price"];
    $details = isset($_POST["details"]) ? $_POST["details"] : '';
    $short_description = isset($_POST["short_description"]) ? $_POST["short_description"] : '';
    $sizes = isset($_POST["size"]) ? $_POST["size"] : [];
    $colors = isset($_POST["color"]) ? $_POST["color"] : [];

    if(strlen($name) > 255) {
        $error['name'] = 'Tên sản phẩm tối đa 255 ký tự';
    }

    if($price < 0) {
        $error['price'] = 'Giá bán thường phải lớn hơn 0';
    }

    if($quantity < 0) {
        $error['quantity'] = 'Số lượng phải lớn hơn 0';
    }

    if(empty($sizes)) {
        $error['size'] = 'Vui lòng chọn ít nhất một kích thước';
    }

    if(empty($colors)) {
        $error['color'] = 'Vui lòng chọn ít nhất một màu';
    }

    if(empty(array_filter($error))) {
        // Handle image upload if a new image is provided
        if (!empty($image)) {
            $target_dir = "../upload/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Image uploaded successfully
            }
        } else {
            $image = $product['image']; // Keep the old image if no new one is uploaded
        }

        try {
            $result = $ProductModel->update_product($category_id, $name, $image, $quantity, $price, $details, $short_description, $product_id);

            // Update sizes and colors for the product (many-to-many relation)
            $ProductModel->update_product_sizes($product_id, $sizes);
            $ProductModel->update_product_colors($product_id, $colors);

            setcookie('success_update', 'Cập nhật sản phẩm thành công', time() + 5, '/');
            header("Location: index.php?quanli=cap-nhat-san-pham&id=".$product_id);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            echo 'Cập nhật sản phẩm thất bại: ' . $error_message;
        }
    }
}

$success = '';
if(isset($_COOKIE['success_update']) && !empty($_COOKIE['success_update'])) {
    $success = $_COOKIE['success_update'];
}

$html_alert = $BaseModel->alert_error_success('', $success);
?>

<!-- Form Start -->
<div class="container-fluid pt-4">
    <form class="row g-4" action="" method="post" enctype="multipart/form-data">
        <div class="col-sm-12 col-xl-9">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">
                    <a href="index.php?quanli=danh-sach-san-pham" class="link-not-hover">Sản phẩm</a>
                    / Cập nhật sản phẩm
                </h6>
                <?=$html_alert?>
                <label for="floatingInput">Tên sản phẩm</label>
                <div class="form-floating mb-3">
                    <input type="text" name="name" value="<?=$name?>" class="form-control" id="floatingInput" placeholder="Tên sản phẩm">
                    <span class="text-danger"><?=$error['name']?></span>
                </div>

                <label for="floatingInput">Giá bán thường (đ)</label>
                <div class="form-floating mb-3">
                    <input type="number" name="price" value="<?=$price?>" class="form-control" id="floatingInput" placeholder="Giá bán thường (đ)">
                    <span class="text-danger"><?=$error['price']?></span>
                </div>

                <label for="floatingInput">Số lượng (nhập số)</label>
                <div class="form-floating mb-3">
                    <input type="number" value="<?=$quantity?>" name="quantity" class="form-control" id="floatingInput" placeholder="Số lượng">
                    <span class="text-danger"><?=$error['quantity']?></span>
                </div>

                <label for="text-dark">Mô tả ngắn</label>
                <div class="form-floating mb-3">
                    <textarea name="short_description" class="form-control" placeholder="Mô tả ngắn" id="short_description"><?=$short_description?></textarea>
                </div>

                <label for="floatingTextarea">Chi tiết sản phẩm</label>
                <div class="form-floating">
                    <textarea name="details" class="form-control" placeholder="Mô tả" id="product_details" style="height: 300px;"><?=$details?></textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-xl-3">
            <div class="bg-light rounded h-100 p-4">
                <div class="mb-3">
                    <label for="formFileSm" class="form-label">Hình ảnh (JPG, PNG, ...)</label>
                    <input style="background-color: #fff" class="form-control form-control-sm" name="image" id="formFileSm" type="file">
                    <div class="my-2">
                        <img src="../upload/<?=$image?>" style="width: 100%;" class="img-fluid" alt="">
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <select name="category_id" class="form-select" id="floatingSelect" required>
                        <?php
                            foreach ($list_categories as $cate) {
                                extract($cate);
                                if($cate['category_id'] == $category_id)
                                    echo '<option value="'.$category_id.'" selected>'.$name.'</option>';
                                else
                                    echo '<option value="'.$category_id.'">'.$name.'</option>';
                            }
                        ?>
                    </select>
                    <label for="floatingSelect">Chọn danh mục</label>
                </div>

                <!-- Size Selection -->
                <div class="form-floating mb-3">
                    <select name="size[]" class="form-select" id="sizeSelect" multiple>
                        <?php
                            foreach ($list_sizes as $size) {
                                $selected = in_array($size['size_id'], $selected_sizes) ? 'selected' : '';
                                echo '<option value="'.$size['size_id'].'" '.$selected.'>'.$size['name'].'</option>';
                            }
                        ?>
                    </select>
                    <label for="sizeSelect">Chọn kích thước</label>
                    <span class="text-danger"><?=$error['size']?></span>
                </div>

                <!-- Color Selection -->
                <div class="form-floating mb-3">
                    <select name="color[]" class="form-select" id="colorSelect" multiple>
                        <?php
                            foreach ($list_colors as $color) {
                                $selected = in_array($color['color_id'], $selected_colors) ? 'selected' : '';
                                echo '<option value="'.$color['color_id'].'" '.$selected.'>'.$color['name'].'</option>';
                            }
                        ?>
                    </select>
                    <label for="colorSelect">Chọn màu</label>
                    <span class="text-danger"><?=$error['color']?></span>
                </div>

                <h6 class="mb-4">
                    <input name="update_product" type="submit" value="Cập nhật" class="btn btn-custom">
                    <a href="index.php?quanli=thung-rac-san-pham&xoatam=<?=$product_id?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');" class="btn btn-custom">Xóa tạm</a>
                </h6>            
            </div>
        </div>
    </form>
</div>
<!-- Form End -->

<style>
    .ck-editor__editable[role="textbox"]:first-child {
        min-height: 300px;
    }
    .ck-content .image {
        max-width: 80%;
        margin: 20px auto;
    }
</style>
