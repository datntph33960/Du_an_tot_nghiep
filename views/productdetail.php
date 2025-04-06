<?php
if (isset($_GET['id_sp'])) {
    $id_sp = $_GET['id_sp'];
    $id_danhmuc = $_GET['id_dm'];

    // Update product view count and get product details
    $ProductModel->update_views($id_sp);
    $product_details = $ProductModel->select_products_by_id($id_sp);
    
    // Parse sizes and colors from the database
    if (!empty($product_details['sizes'])) {
        $available_sizes = explode(',', $product_details['sizes']); // Assuming sizes are stored as comma-separated values
    } else {
        $available_sizes = []; // Default to empty array if no sizes
    }

    if (!empty($product_details['colors'])) {
        $available_colors = explode(',', $product_details['colors']); // Assuming colors are stored as comma-separated values
    } else {
        $available_colors = []; // Default to empty array if no colors
    }

    $similar_product = $ProductModel->select_products_similar($id_danhmuc);
    $name_catgoty = $CategoryModel->select_name_categories();
} 

extract($product_details);
$discount_percentage = $ProductModel->discount_percentage($price, $sale_price);

// Fetch comments
if (isset($_GET['id_sp'])) {
    $product_id = $_GET['id_sp'];
    $list_comments = $CommentModel->select_comments_by_id($product_id);
}
?>
<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                    <a href="index.php?url=cua-hang">Sản phẩm </a>
                    <a href="index.php?url=danh-muc-san-pham&id=<?=$id_danhmuc?>">
                        <?php foreach ($name_catgoty as $value) {
                            if ($value['category_id'] == $id_danhmuc) {
                                echo $value['name'];
                            }
                        } ?>
                    </a>
                    <span><?=$name?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Product Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product__details__pic">
                    <div class="product__details__pic__left product__thumb nice-scroll">
                        <a class="pt active" href="#product-1">
                            <img src="upload/<?=$image?>" alt="">
                        </a>
                    </div>
                    <div class="product__details__slider__content">
                        <div class="product__details__pic__slider owl-carousel">
                            <img data-hash="product-1" class="product__big__img" src="upload/<?=$image?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
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
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <span>( <?=count($list_comments)?> bình luận )</span>
                    </div>
                    <div class="product__details__price">
                        <?=$ProductModel->formatted_price($price); ?> 
                        <div class="label_right ml-2"><?=$discount_percentage?></div>
                    </div>
                    <div class="short__description">
                        <?=$short_description?>
                    </div>

                    <!-- Chọn kích thước -->
<div class="product__details__size">
    <label for="size">Kích thước:</label>
    <select id="size" name="size" class="form-control" onchange="updateSelectedSize()">
        <?php if (!empty($available_sizes)): ?>
            <?php foreach ($available_sizes as $size): ?>
                <option value="<?= htmlspecialchars($size, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($size, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="" disabled>Không có kích thước</option>
        <?php endif; ?>
    </select>
</div>

<!-- Chọn màu sắc -->
<div class="product__details__color">
    <label for="color">Màu sắc:</label>
    <select id="color" name="color" class="form-control" onchange="updateSelectedColor()">
        <?php if (!empty($available_colors)): ?>
            <?php foreach ($available_colors as $color): ?>
                <option value="<?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="" disabled>Không có màu sắc</option>
        <?php endif; ?>
    </select>
</div>


                    <div class="product__details__button">
                        <?php if(isset($_SESSION['user'])) {?>
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
                                
                                <input value="<?=$product_id?>" type="hidden" name="product_id">
                                <input value="<?=$_SESSION['user']['id']?>" type="hidden" name="user_id">
                                <input value="<?=$name?>" type="hidden" name="name">
                                <input value="<?=$image?>" type="hidden" name="image">
                                <input value="<?=$price?>" type="hidden" name="price">

                                <!-- Adding hidden inputs for size and color -->
                                <input type="hidden" name="size" id="selected_size" value="">
                                <input type="hidden" name="color" id="selected_color" value="">
                                
                                <div class="quantity">
                                    <button name="add_to_cart" style="border: none;" type="submit" class="cart-btn btn-primary">
                                        <span class="icon_bag_alt"></span> Thêm vào giỏ
                                    </button>
                                    <button name="add_to_cart" type="submit" style="background-color: #ca1515; border: none;" class="cart-btn">
                                        <span class="icon_bag_alt"></span> Mua ngay
                                    </button>
                                </div>
                            </form>
                        <?php } else { ?>
                            <div class="input-group d-flex align-items-center">
                                <span class="text-dark">Số lượng</span>
                                <div class="input-next-cart d-flex mx-4"> 
                                    <input type="button" value="-" class="button-minus" data-field="quantity">
                                    <input type="number" step="1" max="50" value="1" name="product_quantity" class="quantity-field-cart">
                                    <input type="button" value="+" class="button-plus" data-field="quantity">
                                </div> 
                                <span class="text-dark"><?=$quantity?> sản phẩm có sẵn</span>
                            </div>
                            <div class="quantity">
                                <button name="add_to_cart" onclick="alert('Vui lòng dăng nhập để thực hiện chức năng');" style="border: none;" type="button" class="cart-btn btn-primary">
                                    <span class="icon_bag_alt"></span> <a href="index.php?url=dang-nhap" style="color: #ffffff;">Thêm vào giỏ</a>
                                </button>
                                <button name="add_to_cart" onclick="alert('Vui lòng dăng nhập để thực hiện chức năng');" type="button" style="background-color: #ca1515; border: none;" class="cart-btn">
                                    <span class="icon_bag_alt"></span> <a href="index.php?url=dang-nhap" style="color: #ffffff;">Mua ngay</a>
                                </button>
                            </div>
                        <?php } ?>  
                        <ul>
                            <li><a href="#"><span class="icon_heart_alt"></span></a></li>
                            <li><a href="#"><span class="icon_adjust-horiz"></span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="product__details__tab">
                    <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Mô tả sản phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Bình luận</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabs-1" role="tabpanel">
                            <div class="product__details__tab__desc">
                                <h6>Mô tả sản phẩm</h6>
                                <p><?=$product_details['details']?></p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tabs-2" role="tabpanel">
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
<!-- Product Details Section End -->

<!-- Similar Products Section Begin -->
<section class="similar-product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3>Sản phẩm tương tự</h3>
            </div>
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
<!-- Similar Products Section End -->