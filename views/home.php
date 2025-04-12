<?php
    $base_url = 'http://localhost/Du_an_tot_nghiep-minhngoc/';
    $listProducts = $ProductModel->select_products_limit(8);
    $listCategories = $CategoryModel->select_categories_limit(8);
    $product_limit_3 = $ProductModel->select_products_limit(3);
    $product_order_by = $ProductModel->select_products_order_by(3, 'ASC');
?>

<!-- Banner Section Begin -->
<section class="container-fluid my-3 px-0">
    <div id="header-carousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner" style="border-radius: 0;">
            <div class="carousel-item active">
                <img class="img-fluid w-100" style="height: 80vh; object-fit: cover;" src="<?=$base_url?>upload/sachbanner1.png" alt="Image">
            </div>
            <div class="carousel-item">
                <img class="img-fluid w-100" style="height: 80vh; object-fit: cover;" src="<?=$base_url?>upload/sachbanner2.png" alt="Image">
            </div>
            <div class="carousel-item">
                <img class="img-fluid w-100" style="height: 80vh; object-fit: cover;" src="<?=$base_url?>upload/sachbanner3.png" alt="Image">
            </div>
        </div>
        <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
            <div class="btn btn-dark" style="width: 45px; height: 45px;">
                <span class="carousel-control-prev-icon mb-n2"></span>
            </div>
        </a>
        <a class="carousel-control-next" href="#header-carousel" data-slide="next">
            <div class="btn btn-dark" style="width: 45px; height: 45px;">
                <span class="carousel-control-next-icon mb-n2"></span>
            </div>
        </a>
    </div>
</section>

<!-- Product Section Begin -->
<section class="product spad" style="background-color: #F4F4F9;">
    <section class="container cate-home" style="background-color: #ffffff; border-radius: 10px;">
        <div class="section-title pt-2" style="margin-bottom: 30px;">
            <h4>Danh mục quần áo thể thao</h4>
        </div>
        
        <div class="row g-1 mb-4 mt-2 pb-4">
            <?php foreach ($listCategories as $value) {
                extract($value);
                $link = 'index.php?url=danh-muc-san-pham&id=' .$category_id;
            ?>
            <div class="col-lg-2 col-md-3 col-sm-6 text-center p-1 cate-gory">
                <a href="<?=$link?>"><img style="width: 50%;" src="<?=$base_url?>upload/<?=$image?>" alt=""></a>
                <div class="mt-2">
                    <a class="cate-name text-dark" href="<?=$link?>"><?=$name?></a>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>

    <div class="container" style="background-color: #ffffff; border-radius: 10px;">
        <div class="row pt-3">
            <div class="col-lg-4 col-md-4">
                <div class="section-title">
                    <h4>Sản phẩm quần áo thể thao</h4>
                </div>
            </div>
        </div>
        <div class="row property__gallery">
            <?php foreach ($listProducts as $product) {
                extract($product);
                $image_arr = explode(',', $image);
                $first_image = $image_arr[0];
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mix sach-1">
                <div class="product__item sale">
                    <div class="product__item__pic">
                        <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>">
                            <img src="<?=$base_url?>upload/<?=$first_image?>" alt="<?=$name?>" class="img-fluid" style="width: 100%; height: 270px; object-fit: cover; border-radius: 8px;">
                        </a>
                        <ul class="product__hover">
                            <li><a href="<?=$base_url?>upload/<?=$first_image?>" class="image-popup"><span class="arrow_expand"></span></a></li>
                            <li>
                                <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>">
                                <span class="icon_search_alt"></span>
                                </a>
                            </li>
                            <li>
                                <?php if(isset($_SESSION['user'])) { ?>
                                <form action="index.php?url=gio-hang" method="post">
                                    <input value="<?=$product_id?>" type="hidden" name="product_id">
                                    <input value="<?=$_SESSION['user']['id']?>" type="hidden" name="user_id">
                                    <input value="<?=$name?>" type="hidden" name="name">
                                    <input value="<?=$first_image?>" type="hidden" name="image">
                                    <input value="<?=$price?>" type="hidden" name="price">
                                    <input value="<?=$size?>" type="hidden" name="size">
                                    <input value="<?=$color?>" type="hidden" name="color">
                                    <input value="1" type="hidden" name="product_quantity">
                                </form>
                                <?php } else { ?>
                                    <button type="submit" onclick="alert('Vui lòng đăng nhập để thực hiện chức năng');" name="add_to_cart" id="toastr-success-top-right">
                                        <a href="dang-nhap"><span class="icon_bag_alt"></span></a>
                                    </button>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6 class="text-truncate-1"><a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>"><?=$name?></a></h6>
                        <div class="rating">
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                            <i class="fa fa-star"></i><i class="fa fa-star"></i>
                        </div>
                        <div class="product__price"><?=number_format($price) ."₫"?></div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="col-lg-12 text-center mb-4">
                <a href="index.php?url=cua-hang" class="btn btn-outline-primary">Xem tất cả</a>
            </div>
        </div>
    </div>
</section>

<!-- Banner Section Begin -->
<section class="banner set-bg" data-setbg="<?=$base_url?>public/img/banner/banner-sport.jpg">
    <div class="container">
        <div class="row">
            <div class="col-xl-7 col-lg-8 m-auto">
                <div class="banner__slider owl-carousel">
                    <div class="banner__item">
                        <div class="banner__text">
                            <span>Bộ sưu tập</span>
                            <h1>Quần áo thể thao</h1>
                            <a href="cua-hang">Mua ngay</a>
                        </div>
                    </div>
                    <div class="banner__item">
                        <div class="banner__text">
                            <span>Bộ sưu tập</span>
                            <h1>Giảm giá lớn</h1>
                            <a href="cua-hang">Mua ngay</a>
                        </div>
                    </div>
                    <div class="banner__item">
                        <div class="banner__text">
                            <span>Bộ sưu tập</span>
                            <h1>Thời trang thể thao</h1>
                            <a href="cua-hang">Mua ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trend Section Begin -->
<section class="trend spad">
    <div class="container">
        <div class="row">
            <?php 
            $trend_titles = ['Xu hướng', 'BÁN CHẠY', 'Hot sale'];
            $trend_lists = [$product_limit_3, $product_order_by, $product_limit_3];
            foreach ($trend_lists as $i => $trend_list) { ?>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="trend__content">
                    <div class="section-title">
                        <h4><?=$trend_titles[$i]?></h4>
                    </div>
                    <?php foreach ($trend_list as $value) {
                        extract($value);
                        $image_arr = explode(',', $image);
                        $first_image = $image_arr[0];
                    ?>
                    <div class="trend__item">
                        <div class="trend__item__pic">
                            <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>">
                                <img src="<?=$base_url?>upload/<?=$first_image?>" style="width: 90px;" alt="">
                            </a>
                        </div>
                        <div class="trend__item__text">
                            <h6>
                                <a href="index.php?url=chitietsanpham&id_sp=<?=$product_id?>&id_dm=<?=$category_id?>" class="text-dark"><?=$name?></a>
                            </h6>
                            <div class="rating">
                                <i class="fa fa-star"></i><i class="fa fa-star"></i>
                                <i class="fa fa-star"></i><i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="product__price"><?=number_format($sale_price ?? $price)?>₫</div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Discount Section Begin -->
<section class="discount">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-6 p-0">
                <div class="discount__text text-center">
                    <div class="discount__text__title">
                        <span>Khuyến mãi</span>
                        <h2>Giảm giá mùa thể thao</h2>
                        <h5><span>Sale</span> 50%</h5>
                    </div>
                    <div class="discount__countdown" id="countdown-time">
                        <div class="countdown__item"><span>22</span><p>Ngày</p></div>
                        <div class="countdown__item"><span>18</span><p>Giờ</p></div>
                        <div class="countdown__item"><span>46</span><p>Phút</p></div>
                        <div class="countdown__item"><span>05</span><p>Giây</p></div>
                    </div>
                    <a href="#">Mua ngay</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section Begin -->
<section class="services spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6"><div class="services__item"><i class="fa fa-car"></i><h6>Miễn phí vận chuyển</h6><p>Cho tất cả các đơn hàng trên 200.000đ</p></div></div>
            <div class="col-lg-3 col-md-4 col-sm-6"><div class="services__item"><i class="fa fa-money"></i><h6>Đảm bảo hoàn tiền</h6><p>Nếu sản phẩm có vấn đề</p></div></div>
            <div class="col-lg-3 col-md-4 col-sm-6"><div class="services__item"><i class="fa fa-support"></i><h6>Hỗ trợ trực tuyến 24/7</h6><p>Hỗ trợ chuyên dụng</p></div></div>
            <div class="col-lg-3 col-md-4 col-sm-6"><div class="services__item"><i class="fa fa-headphones"></i><h6>Thanh toán an toàn</h6><p>Thanh toán an toàn 100%</p></div></div>
        </div>
    </div>
</section>
