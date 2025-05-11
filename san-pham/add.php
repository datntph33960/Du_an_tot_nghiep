<?php
$list_categories = $CategoryModel->select_all_categories();
$list_products = $ProductModel->select_products();

$error = array(
    'name' => '',
    'image' => '',
    'size' => '',
    'color' => '',
    'quantity' => '',
    'price' => '',
    'sale_price' => '',
);

$temp = array(
    'name' => '',
    'image' => '',
    'size' => '',
    'color' => '',
    'quantity' => '',
    'price' => '',
    'sale_price' => '',
    'details' => '',
    'short_description' => '',
    'sizes' => '',
    'colors' => '',
);

$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["themsanpham"])) {
    $name = trim($_POST["name"]);
    $category_id = $_POST["category_id"];
    $category_exists = false;

    foreach ($list_categories as $category) {
        if ($category['category_id'] == $category_id) {
            $category_exists = true;
            break;
        }
    }

    if (!$category_exists) {
        $error['category'] = 'Danh mục không hợp lệ';
    }

    $sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : '';
    $colors = isset($_POST['colors']) ? implode(',', $_POST['colors']) : '';

    if (empty($sizes)) {
        $error['size'] = 'Vui lòng chọn ít nhất một kích thước';
    }
    if (empty($colors)) {
        $error['color'] = 'Vui lòng chọn ít nhất một màu sắc';
    }

    $quantity = $_POST["quantity"];
    $price = $_POST["price"];
    $sale_price = 0;
    $create_date = date('Y-m-d H:i:s');
    $status = 1;

    $details = isset($_POST["details"]) ? $_POST["details"] : '';
    $short_description = isset($_POST["short_description"]) ? $_POST["short_description"] : '';

    foreach ($list_products as $value) {
        if ($value['name'] == $name) {
            $error['name'] = 'Tên sản phẩm đã tồn tại.<br>';
            break;
        }
    }

    if (empty($name)) {
        $error['name'] = 'Tên sản phẩm không được để trống';
    }

    if (strlen($name) > 255) {
        $error['name'] = 'Tên sản phẩm tối đa 255 ký tự';
    }

    if ($price < 0) {
        $error['price'] = 'Giá bán thường phải lớn hơn 0';
    }
    if ($quantity < 0) {
        $error['quantity'] = 'Số lượng phải lớn hơn 0';
    }

    $uploaded_images = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $name_file) {
            $tmp_name = $_FILES['images']['tmp_name'][$key];
            $target = "../upload/" . basename($name_file);
            if (move_uploaded_file($tmp_name, $target)) {
                $uploaded_images[] = $name_file;
            }
        }
    }

    $image = !empty($uploaded_images) ? implode(',', $uploaded_images) : "default-product.jpg";

    if (empty(array_filter($error))) {
        try {
            $result = $ProductModel->insert_product(
                $category_id,
                $name,
                $image,
                $quantity,
                $price,
                $details,
                $short_description,
                $sizes,
                $colors
            );

            $success = 'Thêm sản phẩm thành công';
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            echo 'Thêm sản phẩm thất bại: ' . $error_message;
            $success = 'Thêm sản phẩm thất bại';
        }
    } else {
        $temp['name'] = $name;
        $temp['price'] = $price;
        $temp['quantity'] = $quantity;
        $temp['short_description'] = $short_description;
        $temp['details'] = $details;
        $temp['sizes'] = $sizes;
        $temp['colors'] = $colors;
    }
}

$html_alert = $BaseModel->alert_error_success('', $success);
?>

<div class="container-fluid pt-4">
    <form class="row g-4" action="" method="post" enctype="multipart/form-data">
        <div class="col-sm-12 col-xl-9">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">
                    <a href="index.php?quanli=danh-sach-san-pham" class="link-not-hover">Sản phẩm</a>
                    / Thêm sản phẩm
                </h6>
                <?= $html_alert ?>

                <label for="floatingInput">Tên sản phẩm</label>
                <div class="form-floating mb-3">
                    <input type="text" name="name" value="<?= $temp['name'] ?>" class="form-control" id="floatingInput" placeholder="Tên sản phẩm">
                    <span class="text-danger"><?= $error['name'] ?></span>
                </div>

                <label for="floatingInput">Giá tiền (đ)</label>
                <div class="form-floating mb-3">
                    <input type="number" name="price" value="<?= $temp['price'] ?>" class="form-control" id="floatingInput" placeholder="Giá bán thường (đ)">
                    <span class="text-danger"><?= $error['price'] ?></span>
                </div>

                <label for="floatingInput">Số lượng (nhập số)</label>
                <div class="form-floating mb-3">
                    <input type="number" value="<?= $temp['quantity'] ?>" name="quantity" class="form-control" id="floatingInput" placeholder="Số lượng">
                    <span class="text-danger"><?= $error['quantity'] ?></span>
                </div>

                <label>Kích thước</label>
                <div class="mb-3" id="size-container">
                    <?php 
                    $selected_sizes = explode(',', $temp['sizes']);
                    $available_sizes = ['S', 'M', 'L', 'XL'];
                    $all_sizes = array_unique(array_merge($available_sizes, $selected_sizes));
                    foreach ($all_sizes as $size) : 
                        $safe_id = preg_replace('/\W+/', '', strtolower($size));
                    ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="sizes[]" value="<?= $size ?>" id="size_<?= $safe_id ?>" <?= in_array($size, $selected_sizes) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="size_<?= $safe_id ?>"><?= $size ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="input-group mb-3">
                    <input type="text" id="newSizeInput" class="form-control" placeholder="Nhập size mới">
                    <button class="btn btn-outline-primary" type="button" onclick="addNewSize()">Thêm size</button>
                </div>
                <div><span class="text-danger"><?= $error['size'] ?></span></div>

                <label>Màu sắc</label>
                <div class="mb-3" id="color-container">
                    <?php 
                    $selected_colors = explode(',', $temp['colors']);
                    $available_colors = ['Đỏ', 'Xanh', 'Vàng', 'Đen', 'Trắng', 'Hồng'];
                    $all_colors = array_unique(array_merge($available_colors, $selected_colors));
                    foreach ($all_colors as $color) : 
                        $color_slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $color));
                    ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="colors[]" value="<?= $color ?>" id="color_<?= $color_slug ?>" <?= in_array($color, $selected_colors) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="color_<?= $color_slug ?>"><?= $color ?></label>
                    </div>
                    <?php endforeach; ?>
                    <div><span class="text-danger"><?= $error['color'] ?></span></div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" id="newColorInput" class="form-control" placeholder="Nhập màu mới">
                    <button class="btn btn-outline-primary" type="button" onclick="addNewColor()">Thêm màu</button>
                </div>

                <label for="text-dark">Mô tả ngắn</label>
                <div class="form-floating mb-3">
                    <textarea name="short_description" class="form-control" placeholder="Mô tả ngắn" id="short_description"><?= $temp['short_description'] ?></textarea>
                </div>

                <label for="floatingTextarea">Chi tiết sản phẩm</label>
                <div class="form-floating">
                    <textarea name="details" class="form-control" placeholder="Mô tả" id="product_details" style="height: 300px;"><?= $temp['details'] ?></textarea>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-xl-3">
            <div class="bg-light rounded h-100 p-4">
                <div class="mb-3">
                    <label for="formFileSm" class="form-label">Hình ảnh (JPG, PNG, ...)</label>
                    <input style="background-color: #fff" class="form-control form-control-sm" name="images[]" id="formFileSm" type="file" multiple>

                    <div id="previewCarousel" class="carousel slide my-2" data-bs-ride="carousel" style="display: none;">
                        <div class="carousel-inner" id="carouselInner"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#previewCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#previewCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <select name="category_id" class="form-select" id="floatingSelect" required>
                        <?php foreach ($list_categories as $value) : ?>
                        <option value="<?= $value['category_id'] ?>" <?= (isset($category_id) && $category_id == $value['category_id']) ? 'selected' : '' ?> >
                            <?= $value['name'] ?>
                        </option>
                        <?php endforeach ?>
                    </select>
                    <label for="floatingSelect">Chọn danh mục</label>
                </div>

                <h6 class="mb-4">
                    <input name="themsanpham" type="submit" value="Đăng" class="btn btn-custom">
                </h6>
            </div>
        </div>
    </form>
</div>

<script>
    function addNewSize() {
        const input = document.getElementById('newSizeInput');
        const value = input.value.trim();
        const container = document.getElementById('size-container');

        if (value === '') return;

        const safeId = value.replace(/\W+/g, '').toLowerCase();

        if (document.getElementById('size_' + safeId)) {
            alert('Size này đã tồn tại!');
            return;
        }

        const div = document.createElement('div');
        div.className = 'form-check form-check-inline';
        div.innerHTML = `
            <input class="form-check-input" type="checkbox" name="sizes[]" value="${value}" id="size_${safeId}" checked>
            <label class="form-check-label" for="size_${safeId}">${value}</label>
        `;
        container.appendChild(div);
        input.value = '';
    }

    function addNewColor() {
        const input = document.getElementById('newColorInput');
        const value = input.value.trim();
        const container = document.getElementById('color-container');

        if (value === '') return;

        const safeId = value.replace(/\W+/g, '').toLowerCase();

        if (document.getElementById('color_' + safeId)) {
            alert('Màu này đã tồn tại!');
            return;
        }

        const div = document.createElement('div');
        div.className = 'form-check form-check-inline';
        div.innerHTML = `
            <input class="form-check-input" type="checkbox" name="colors[]" value="${value}" id="color_${safeId}" checked>
            <label class="form-check-label" for="color_${safeId}">${value}</label>
        `;
        container.appendChild(div);
        input.value = '';
    }

    document.getElementById("formFileSm").addEventListener("change", function (event) {
        const files = event.target.files;
        const carouselInner = document.getElementById("carouselInner");
        const carousel = document.getElementById("previewCarousel");

        if (files.length === 0) {
            carousel.style.display = "none";
            return;
        }

        carouselInner.innerHTML = "";

        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement("div");
                div.className = "carousel-item" + (index === 0 ? " active" : "");
                div.innerHTML = `<img src="${e.target.result}" class="d-block w-100" alt="Ảnh ${index + 1}">`;
                carouselInner.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        carousel.style.display = "block";
    });
</script>

<style>
    .ck-editor__editable[role="textbox"]:first-child {
        min-height: 300px;
    }
    .ck-content .image {
        max-width: 80%;
        margin: 20px auto;
    }
</style>