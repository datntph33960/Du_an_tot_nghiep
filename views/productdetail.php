<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<?php
// Cấu hình đường dẫn
$upload_dir_server = $_SERVER['DOCUMENT_ROOT'] . "/Du_an_tot_nghiep-minhngoc/upload/";
$upload_dir_url = "http://localhost/Du_an_tot_nghiep-minhngoc/upload/";

if (isset($_GET['id_sp'])) {   
    $id_sp = $_GET['id_sp'];
    $id_danhmuc = $_GET['id_dm'];

    $ProductModel->update_views($id_sp);
    $product_details = $ProductModel->select_products_by_id($id_sp);

    if (!$product_details) {
        echo "<p style='color:red'>❌ Không tìm thấy sản phẩm với ID: $id_sp</p>";
        return;
    }

    $available_sizes = !empty($product_details['sizes']) ? explode(',', $product_details['sizes']) : [];
    $available_colors = !empty($product_details['colors']) ? explode(',', $product_details['colors']) : [];

    $name = $product_details['name'] ?? '';
    $price = $product_details['price'] ?? 0;
    $sale_price = $product_details['sale_price'] ?? 0;
    $short_description = $product_details['short_description'] ?? '';
    $quantity = $product_details['quantity'] ?? 0;
    $image = $product_details['image'] ?? '';
    $size = $available_sizes[0] ?? '';
    $color = $available_colors[0] ?? '';

    $image_data = trim($product_details['image']);
    $separator = strpos($image_data, '|') !== false ? '|' : ',';
    $album_images = array_filter(array_map('trim', explode($separator, $image_data)));

    $similar_product = $ProductModel->select_products_similar($id_danhmuc);
    $name_catgoty = $CategoryModel->select_name_categories();

    $discount_percentage = $ProductModel->discount_percentage($price, $sale_price);

    $product_id = $id_sp;
    $list_comments = $CommentModel->select_comments_by_id($product_id);
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="breadcrumb__links">
            <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
            <a href="index.php?url=cua-hang">Sản phẩm</a>
            <a href="index.php?url=danh-muc-san-pham&id=<?=$id_danhmuc?>">
                <?php foreach ($album_images as $img): ?>
                    <img src="<?= $upload_dir_url . $img ?>" style="width: 50px; margin: 2px;" alt="">
                <?php endforeach; ?>
            </a>
            <span><?= htmlspecialchars($name) ?></span>
        </div>
    </div>
</div>

<!-- Product Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row">
            <!-- Hình ảnh chi tiết sản phẩm (Bootstrap Carousel) -->
            <div class="col-lg-6">
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner border rounded">
                        <?php foreach ($album_images as $index => $img): 
                            $img_url = $upload_dir_url . $img;
                        ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= $img_url ?>" class="d-block w-100 img-fluid" alt="Ảnh <?= $index ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                        <span class="visually-hidden">Trước</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                        <span class="visually-hidden">Tiếp</span>
                    </button>

                    <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
                        <?php foreach ($album_images as $index => $img): 
                            $img_url = $upload_dir_url . $img;
                        ?>
                            <img 
                                src="<?= $img_url ?>" 
                                class="img-thumbnail border <?= $index === 0 ? 'border-primary' : '' ?>" 
                                style="width: 80px; height: 80px; cursor: pointer;" 
                                onclick="bootstrapCarouselTo(<?= $index ?>)">
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Thông tin sản phẩm -->
            <div class="col-lg-6">
                <div class="product__details__text">
                    <h3><?=$name?>
                        <span>
                            Danh mục: <?php foreach ($name_catgoty as $value) {
                                if ($value['category_id'] == $id_danhmuc) {
                                    echo $value['name'];
                                }
                            } ?>
                        </span>
                    </h3>
                    <div class="rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i>
                        <i class="fa fa-star"></i><i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <span>( <?=count($list_comments)?> bình luận )</span>
                    </div>
                    <div class="product__details__price">
                        <?=$ProductModel->formatted_price($price)?>
                        <div class="label_right ml-2"><?=$discount_percentage?></div>
                    </div>
                    <div class="short__description"><?=$short_description?></div>

                    <!-- Kích thước -->
                    <div class="product__details__size">
                        <label for="size">Kích thước:</label>
                        <select id="size" name="size" class="form-control" onchange="updateSelectedSize()">
                            <?php foreach ($available_sizes as $size_option): ?>
                                <option value="<?= htmlspecialchars($size_option) ?>"><?= htmlspecialchars($size_option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Màu sắc -->
                    <div class="product__details__color">
                        <label for="color">Màu sắc:</label>
                        <select id="color" name="color" class="form-control" onchange="updateSelectedColor()">
                            <?php foreach ($available_colors as $color_option): ?>
                                <option value="<?= htmlspecialchars($color_option) ?>"><?= htmlspecialchars($color_option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Thêm vào giỏ -->
                    <div class="product__details__button">
                        <?php if(isset($_SESSION['user'])) { ?>
                            <form action="index.php?url=gio-hang" method="post">
                                <div class="input-group d-flex align-items-center">
                                    <span class="text-dark">Số lượng</span>
                                    <div class="input-next-cart d-flex mx-4"> 
                                        <input type="button" value="-" class="button-minus" data-field="quantity">
                                        <input type="number" step="1" max="50" value="1" name="product_quantity" class="quantity-field-cart">
                                        <input type="button" value="+" class="button-plus" data-field="quantity">
                                    </div> 
                                    <span class="text-dark"><?=$quantity?> sản phẩm có sẵn</span>
                                </div>
                                <input type="hidden" name="product_id" value="<?=$product_id?>">
                                <input type="hidden" name="user_id" value="<?=$_SESSION['user']['id']?>">
                                <input type="hidden" name="name" value="<?=$name?>">
                                <input type="hidden" name="image" value="<?=$image?>">
                                <input type="hidden" name="price" value="<?=$price?>">
                                <input type="hidden" name="size" id="selected_size" value="<?=$size?>">
                                <input type="hidden" name="color" id="selected_color" value="<?=$color?>">
                                <div class="quantity">
                                    <button name="add_to_cart" type="submit" class="cart-btn btn-primary"><span class="icon_bag_alt"></span> Thêm vào giỏ</button>
                                    <button name="add_to_cart" type="submit" style="background-color: #ca1515;" class="cart-btn"><span class="icon_bag_alt"></span> Mua ngay</button>
                                </div>
                            </form>
                        <?php } else { ?>
                            <p>Vui lòng <a href="index.php?url=dang-nhap">đăng nhập</a> để mua hàng.</p>
                        <?php } ?>
                        <ul>
                            <li><a href="#"><span class="icon_heart_alt"></span></a></li>
                            <li><a href="#"><span class="icon_adjust-horiz"></span></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tab mô tả & bình luận -->
            <div class="col-lg-12">
                <div class="product__details__tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabs-1">Mô tả sản phẩm</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabs-2">Bình luận</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabs-1">
                            <div class="product__details__tab__desc">
                                <h6>Mô tả sản phẩm</h6>
                                <p><?= $product_details['details'] ?></p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tabs-2">
                            <div class="product__details__tab__comment">
                                <h6>Bình luận</h6>
                                <?php foreach ($list_comments as $comment): ?>
                                    <div class="comment">
                                        <p><strong><?=$comment['user_name']?></strong>: <?=$comment['content']?></p>
                                        <span class="comment-time"><?=$comment['created_at']?></span>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (isset($_SESSION['user'])) { ?>
                                    <form action="index.php?url=add-comment" method="post">
                                        <textarea name="comment" required></textarea>
                                        <input type="hidden" name="product_id" value="<?=$product_id?>">
                                        <input type="submit" value="Gửi Bình Luận">
                                    </form>
                                <?php } else { ?>
                                    <p>Bạn cần <a href="index.php?url=dang-nhap">đăng nhập</a> để bình luận.</p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Similar Products Section Begin -->
<section class="similar-product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12"><h3>Sản phẩm tương tự</h3></div>
            <?php foreach ($similar_product as $similar): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product__item">
                        <div class="product__item__pic set-bg" data-setbg="upload/<?=$similar['image']?>">
                            <div class="label">New</div>
                            <ul class="product__item__hover">
                                <li><a href="index.php?url=chitietsanpham&id_sp=<?=$similar['product_id']?>&id_dm=<?=$id_danhmuc?>" class="view"><span class="icon_eye_alt"></span></a></li>
                            </ul>
                        </div>
                        <div class="product__item__text">
                            <h6><a href="index.php?url=chitietsanpham&id_sp=<?=$similar['product_id']?>&id_dm=<?=$id_danhmuc?>"><?=$similar['name']?></a></h6>
                            <h5><?=$ProductModel->formatted_price($similar['price'])?></h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Scripts -->
<script>
    function updateSelectedSize() {
        const selected = document.getElementById('size').value;
        document.getElementById('selected_size').value = selected;
    }

    function updateSelectedColor() {
        const selected = document.getElementById('color').value;
        document.getElementById('selected_color').value = selected;
    }

    document.addEventListener("DOMContentLoaded", function () {
        updateSelectedSize();
        updateSelectedColor();
    });

    $(document).ready(function(){
        $(".product__details__pic__slider").owlCarousel({
            items: 1,
            loop: true,
            dots: true,
            nav: true,
            navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"]
        });

        $('.product__thumb a').on('click', function(e){
            e.preventDefault();
            const id = $(this).attr('href');
            $('.product__big__img').removeClass('active');
            $(id).addClass('active');
            $('.product__thumb a').removeClass('active');
            $(this).addClass('active');
        });
    });
    function bootstrapCarouselTo(index) {
        const carousel = bootstrap.Carousel.getInstance(document.getElementById('productCarousel'));
        carousel.to(index);
    }
</script>
