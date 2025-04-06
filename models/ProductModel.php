<?php
class ProductModel {
    public function select_products_limit($limit) {
        $sql = "SELECT * FROM products WHERE status = 1 ORDER BY product_id DESC LIMIT $limit";
        return pdo_query($sql);
    }

    public function select_products_by_id($id) {
        $sql = "SELECT * FROM products WHERE product_id = ?";
        return pdo_query_one($sql, $id);
    }

    public function select_products_order_by($limit, $order_by) {
        $sql = "SELECT * FROM products WHERE status = 1 ORDER BY product_id $order_by LIMIT $limit";
        return pdo_query($sql);
    }

    public function select_cate_in_product($product_id) {
        $sql = "SELECT category_id FROM products WHERE product_id = ?";
        return pdo_query_one($sql, $product_id);
    }

    public function select_products_similar($id) {
        $sql = "SELECT * FROM products WHERE category_id = ? ORDER BY product_id LIMIT 4";
        return pdo_query($sql, $id);
    }

    public function search_products($query) {
        $sql = "SELECT * FROM products WHERE name LIKE '%$query%'";
        return pdo_query($sql);
    }

    public function search_products_by_price($from_price, $to_price) {
        $sql = "SELECT * FROM products WHERE sale_price BETWEEN '$from_price' AND '$to_price'";
        return pdo_query($sql);
    }

    public function get_min_max_prices() {
        $sql = "SELECT MIN(sale_price) AS min_price, MAX(sale_price) AS max_price FROM products WHERE status = 1";
        return pdo_query_one($sql);
    }

    public function select_all_products() {
        $sql = "SELECT * FROM products WHERE status = 1 ORDER BY product_id DESC";
        return pdo_query($sql);
    }

    public function select_products_by_cate($category_id) {
        $sql = "SELECT * FROM products WHERE category_id = ?";
        return pdo_query($sql, $category_id);
    }

    public function select_list_products($page, $perPage) {
        $start = ($page - 1) * $perPage;
        $sql = "SELECT * FROM products WHERE status = 1 ORDER BY product_id DESC LIMIT " . $start . ", " . $perPage;
        return pdo_query($sql);
    }

    public function count_products() {
        $sql = "SELECT COUNT(product_id) AS total FROM products WHERE status = 1";
        return pdo_query_one($sql);
    }

    public function discount_percentage($price, $sale_price) {
        $discount_percentage = ($price - $sale_price) / $price * 100;
        $round_percentage = round($discount_percentage, 0) . "%";
        return $round_percentage;
    }

    public function formatted_price($price) {
        $format = number_format($price, 0, ',', '.') . 'đ';
        return $format;
    }

    public function update_views($product_id) {
        $sql = "UPDATE products SET views = views + 1 WHERE product_id = ?";
        pdo_execute($sql, $product_id);
    }

    // Thêm sản phẩm mới với size và color
    public function insert_product($name, $price, $size, $color, $image) {
        $sql = "INSERT INTO products (name, sale_price, size, color, image) VALUES (?, ?, ?, ?, ?)";
        pdo_execute($sql, $name, $price, $size, $color, $image);
    }

    // Cập nhật thông tin sản phẩm
    public function update_product($id, $name, $price, $size, $color, $image) {
        $sql = "UPDATE products SET name = ?, sale_price = ?, size = ?, color = ?, image = ? WHERE product_id = ?";
        pdo_execute($sql, $name, $price, $size, $color, $image, $id);
    }

    // Xóa sản phẩm
    public function delete_product($id) {
        $sql = "DELETE FROM products WHERE product_id = ?";
        pdo_execute($sql, $id);
    }


    // Lấy sản phẩm theo id có kèm theo thông tin kích thước và màu sắc
    public function select_product_with_size_color_by_id($id) {
        $sql = "SELECT product_id, name, sale_price, size, color, image FROM products WHERE product_id = ?";
        return pdo_query_one($sql, $id);
    }
}

// Khởi tạo đối tượng ProductModel
$ProductModel = new ProductModel();
?>
