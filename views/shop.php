<?php
    if (isset($_GET['page'])) {
        $page = intval($_GET['page']);
    } else {
        $page = 1;
    }

    $min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : null;
    $max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : null;

    $list_products = $ProductModel->select_list_products($page, 9, $min_price, $max_price);
    $list_catgories = $CategoryModel->select_all_categories();
?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                    <span>Sản Phẩm</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Shop Section Begin -->
<section class="shop spad">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-3">
                <div class="shop__sidebar">
                    <div class="sidebar__categories">
                        <div class="section-title">
                            <h4>DANH MỤC</h4>
                        </div>
                        <div class="categories__accordion">
                            <div class="accordion" id="accordionExample">
                                <?php foreach ($list_catgories as $value) {
                                    extract($value); ?>
                                    <div class="card">
                                        <div class="card-heading active">
                                            <a href="index.php?url=danh-muc-san-pham&id=<?= $category_id ?>"><?= $name ?></a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar__filter">
                        <div class="section-title">
                            <h4>TÌM THEO GIÁ</h4>
                        </div>
                        <div class="filter-range-wrap">
                            <div class="price-range ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content"
                                data-min="100000" data-max="5000000"></div>
                            <div class="range-slider">
                                <form action="index.php" method="get">
                                    <input type="hidden" name="url" value="cua-hang">
                                    <div class="price-input">
                                        <p>Giá từ:</p> <br>
                                        <input type="text" id="minamount" name="min_price" value="<?= $min_price ?>"> 
                                        <p>đến</p>
                                        <input type="text" id="maxamount" name="max_price" value="<?= $max_price ?>"> <br>
                                        <input type="submit" class="filter-price" value="LỌC GIÁ">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product list -->
            <div class="col-lg-9 col-md-9">
    <div class="row">
        <?php foreach ($list_products as $value) {
            extract($value);

            // Xử lý ảnh giống trang chủ
            if (is_array($image)) {
                $images = explode(',', $image[0]);
            } else {
                $images = explode(',', $image);
            }
            $first_image = $images[0];

            $discount_percentage = $ProductModel->discount_percentage($price, $sale_price);
        ?>
        <div class="col-lg-4 col-md-6 col-6-rp-mobile">
            <div class="product__item sale">
                <div class="product__item__pic set-bg" data-setbg="upload/<?= $first_image ?>">
                    <ul class="product__hover">
                        <li><a href="upload/<?= $first_image ?>" class="image-popup"><span class="arrow_expand"></span></a></li>
                        <li>
                            <a href="index.php?url=chitietsanpham&id_sp=<?= $product_id ?>&id_dm=<?= $category_id ?>"><span class="icon_search_alt"></span></a>
                        </li>
                        <li>
                        <?php if (isset($_SESSION['user'])) { ?>
                            <form action="index.php?url=gio-hang" method="post">
                                <input value="<?= $product_id ?>" type="hidden" name="product_id">
                                <input value="<?= $_SESSION['user']['id'] ?>" type="hidden" name="user_id">
                                <input value="<?= $name ?>" type="hidden" name="name">
                                <input value="<?= $first_image ?>" type="hidden" name="image">
                                <input value="<?= $price ?>" type="hidden" name="price">
                                <input value="1" type="hidden" name="product_quantity">
                                <button type="submit" name="add_to_cart"><span class="icon_bag_alt"></span></button>
                            </form>
                        <?php } else { ?>
                            <button type="submit" onclick="alert('Vui lòng đăng nhập để thực hiện chức năng');" name="add_to_cart">
                                <a href="dang-nhap"><span class="icon_bag_alt"></span></a>
                            </button>
                        <?php } ?>
                        </li>
                    </ul>
                </div>
                <div class="product__item__text">
                    <h6 class="text-truncate-1">
                        <a href="index.php?url=chitietsanpham&id_sp=<?= $product_id ?>&id_dm=<?= $category_id ?>"><?= $name ?></a>
                    </h6>
                    <div class="rating">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                    </div>
                    <div class="product__price"><?= number_format($price) . "₫" ?> </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- Pagination -->
        <?php
            $qty_product = $ProductModel->count_products();
            $totalProducts = count($qty_product);
            $productsPerPage = 9;
            $numberOfPages = ceil($totalProducts / $productsPerPage);
            $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

            $html_pagination = '';
            $pagination_next = '';
            $pagination_prev = '';

            for ($i = 1; $i <= $numberOfPages; $i++) {
                $active = ($i === $currentPage) ? 'active' : '';
                $html_pagination .= '<a class="' . $active . '" href="index.php?url=cua-hang&page=' . $i . '">' . $i . '</a>';
            }

            if ($currentPage < $numberOfPages) {
                $pagination_next = '<a href="index.php?url=cua-hang&page=' . ($currentPage + 1) . '"><i class="fa fa-angle-right"></i></a>';
            }

            if ($currentPage > 1) {
                $pagination_prev = '<a href="index.php?url=cua-hang&page=' . ($currentPage - 1) . '"><i class="fa fa-angle-left"></i></a>';
            }
        ?>

        <div class="col-lg-12 text-center">
            <div class="pagination__option">
                <?= $pagination_prev ?>
                <?= $html_pagination ?>
                <?= $pagination_next ?>
            </div>
        </div>
    </div>
</div>

</section>
<!-- Shop Section End -->
