<?php
    class ProductModel {
        public function insert_product($category_id, $name, $image, $quantity, $price, $details, $short_description, $sizes, $colors) {
            $sql = "INSERT INTO products 
                    (category_id, name, image, quantity, price, details, short_description, sizes, colors)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
            // Gọi pdo_execute với các tham số
            pdo_execute($sql, $category_id, $name, $image, $quantity, $price, $details, $short_description, $sizes, $colors);
        }
        

        public function select_products() {
            $sql = "SELECT name FROM products WHERE status = 1";

            return pdo_query($sql);
        }

        public function update_product_not_active($product_id) {
            $sql = "UPDATE products SET status = 0 WHERE product_id = ?";

            pdo_execute($sql, $product_id);
        }

        public function update_product_active($product_id) {
            $sql = "UPDATE products SET status = 1 WHERE product_id = ?";

            pdo_execute($sql, $product_id);
        }

        function select_list_products($keyword, $id_danhmuc, $page, $perPage) {
            // Tính toán vị trí bắt đầu của kết quả trên trang hiện tại
            $start = ($page - 1) * $perPage;
        
            // Bắt đầu câu truy vấn SQL
            $sql = "SELECT * FROM products WHERE 1";
            
            // Thêm điều kiện tìm kiếm theo keyword
            if($keyword != '') {
                $sql .= " AND name LIKE '%" . $keyword . "%'";
            }
        
            // Thêm điều kiện tìm kiếm theo id_danhmuc
            if($id_danhmuc > 0) {
                $sql .= " AND category_id ='" . $id_danhmuc . "'";
            }
        
            // Sắp xếp theo id giảm dần
            $sql .= " AND status = 1 ORDER BY product_id DESC";
        
            // Thêm phần phân trang
            $sql .= " LIMIT " . $start . ", " . $perPage;
        
            return pdo_query($sql);
        }

        public function select_recycle_products() {
            $sql = "SELECT * FROM products WHERE status = 0 ORDER BY product_id DESC";

            return pdo_query($sql);
        }

        public function select_product_by_id($product_id) {
            $sql = "SELECT * FROM products WHERE product_id =?";

            return pdo_query_one($sql, $product_id);
        }

        public function discount_percentage($price, $sale_price) {
            $discount_percentage = ($price - $sale_price) / $price * 100;

            $round__percentage = round($discount_percentage, 0)."%";
            return $round__percentage;
        }

        public function formatted_price($price) {
            $format = number_format($price, 0, ',', '.') . 'đ';
            return $format;
        }

        // Delete
        public function delete_product($product_id) {
            $sql = "DELETE FROM products WHERE product_id = ?";
            pdo_execute($sql, $product_id);
        }

        public function update_product($category_id, $name, $image, $quantity, $price, $details, $short_description, $sizes, $colors, $product_id) {
            $sql = "UPDATE products SET 
                    category_id = ?, 
                    name = ?, 
                    image = ?, 
                    quantity = ?, 
                    price = ?, 
                    details = ?, 
                    short_description = ?, 
                    sizes = ?, 
                    colors = ? 
                    WHERE product_id = ?";
            
            pdo_execute($sql, $category_id, $name, $image, $quantity, $price, $details, $short_description, $sizes, $colors, $product_id);
        }
        public function select_sizes() {
            $sql = "SELECT DISTINCT sizes FROM products WHERE sizes IS NOT NULL AND sizes != ''";
            $result = pdo_query($sql);
            $allSizes = [];
            foreach ($result as $row) {
                $sizes = explode(',', $row['sizes']);
                $allSizes = array_merge($allSizes, $sizes);
            }
            $uniqueSizes = array_unique(array_map('trim', $allSizes));
            sort($uniqueSizes);
            return $uniqueSizes;
        }
        public function select_colors() {
            $sql = "SELECT DISTINCT colors FROM products WHERE colors IS NOT NULL AND colors != ''";
            $result = pdo_query($sql);
            $allColors = [];
            foreach ($result as $row) {
                $colors = explode(',', $row['colors']);
                $allColors = array_merge($allColors, $colors);
            }

            $uniqueColors = array_unique(array_map('trim', $allColors));
            sort($uniqueColors);
            return $uniqueColors;
        }
        public function get_product_sizes($product_id) {
            $sql = "SELECT sizes FROM products WHERE product_id = ?";
            $result = pdo_query_one($sql, $product_id);
            if ($result && !empty($result['sizes'])) {
                return array_map('trim', explode(',', $result['sizes']));
            }
            return [];
        }
        public function get_product_colors($product_id) {
            $sql = "SELECT colors FROM products WHERE product_id = ?";
            $result = pdo_query_one($sql, $product_id);
            if ($result && !empty($result['colors'])) {
                return array_map('trim', explode(',', $result['colors']));
            }
            return [];
        }
// ProductModel.php
public function get_category_product_statistics() {
    $sql = "
        SELECT 
            categories.name AS cate_name,
            COUNT(products.product_id) AS count_products,
            MIN(products.price) AS min_price,
            MAX(products.price) AS max_price,
            ROUND(AVG(products.price)) AS avg_product
        FROM products
        JOIN categories ON products.category_id = categories.category_id
        GROUP BY categories.category_id
    ";
    return pdo_query($sql);
}

    }

    $ProductModel = new ProductModel();
?>