<?php
    class OrderModel {
    // Lấy ID đơn hàng mới nhất
    public function select_order_id() {
        $sql = "SELECT order_id FROM orders ORDER BY date DESC LIMIT 1";
        return pdo_query_one($sql);
    }

    // Lấy danh sách đơn hàng theo user_id
    public function select_list_orders($user_id) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_id DESC";
        return pdo_query($sql, $user_id);
    }

    // Lấy thông tin chi tiết đơn hàng và sản phẩm
    public function select_orderdetails_and_products($order_id) {
        $sql = "
            SELECT
                products.product_id,
                orderdetails.name AS product_name,
                products.image,
                orderdetails.quantity,
                orderdetails.price AS product_price,
                products.sizes,
                products.colors
            FROM
                products
            JOIN
                orderdetails ON products.product_id = orderdetails.product_id
            WHERE
                orderdetails.order_id = ?
        ";
        return pdo_query($sql, $order_id);
    }

    // Lấy thông tin đầy đủ của đơn hàng (bao gồm thông tin người dùng, sản phẩm, ...)
    public function getFullOrderInformation($order_id) {
        $sql = "
            SELECT
                orders.order_id,
                orders.user_id,
                orders.date AS order_date,
                orders.total,
                orders.address AS order_address,
                orders.phone AS order_phone,
                orders.note,
                orders.status,
                orders.receiver_name,
                orders.receiver_phone,
                users.full_name,
                users.email,
                users.phone AS user_phone,
                orderdetails.product_id,
                orderdetails.quantity,
                orderdetails.price,
                orderdetails.sizes AS sizes,
                orderdetails.colors AS colors,
                orderdetails.name AS product_name,
                products.image AS product_image
            FROM
                orders
            JOIN
                users ON orders.user_id = users.user_id
            JOIN
                orderdetails ON orders.order_id = orderdetails.order_id
            JOIN
                products ON orderdetails.product_id = products.product_id
            WHERE
                orders.order_id = ?
        ";
        return pdo_query($sql, $order_id);
    }

    // Cập nhật trạng thái đơn hàng
    public function update_status_order($status, $order_id) {
        $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
        pdo_execute($sql, $status, $order_id);
    }

    // Thêm đơn hàng mới
    public function insert_orders($user_id, $total, $address, $phone, $note, $payment_method_id) {
        $sql = "INSERT INTO orders(user_id, total, address, phone, note, payment_method_id) VALUES(?,?,?,?,?,?)";
        pdo_execute($sql, $user_id, $total, $address, $phone, $note, $payment_method_id);
    }

    // Thêm chi tiết đơn hàng
    public function insert_orderdetails($order_id, $product_id, $quantity, $price, $sizes, $colors, $product_name) {
        $sql = "INSERT INTO orderdetails(order_id, product_id, quantity, price, sizes, colors, name) VALUES(?,?,?,?,?,?,?)";
        pdo_execute($sql, $order_id, $product_id, $quantity, $price, $sizes, $colors, $product_name);
    }

    // Xóa giỏ hàng theo user_id
    public function delete_cart_by_user_id($user_id) {
        $sql = "DELETE FROM carts WHERE user_id = ?";
        pdo_execute($sql, $user_id);
    }

    // Lấy danh sách đơn hàng cho admin
    public function select_list_orders_admin() {
        $sql = "
            SELECT orders.*, users.full_name, payment_methods.method_name
            FROM orders
            JOIN users ON orders.user_id = users.user_id
            JOIN payment_methods ON orders.payment_method_id = payment_methods.id
            ORDER BY orders.order_id DESC
        ";
        return pdo_query($sql);
    }

    // Lấy phương thức thanh toán của đơn hàng
    public function get_payment_method($order_id) {
        $sql = "SELECT payment_method_id FROM orders WHERE order_id = ?";
        $row = pdo_query_one($sql, $order_id);
        return $row['payment_method_id'];
    }

    // Lấy thống kê số lượng đơn hàng theo ngày
    public function get_order_sold_by_day() {
        $sql = "
            SELECT 
                DATE(date) AS order_day,
                COUNT(*) AS orders_count,
                SUM(total) AS total_revenue
            FROM orders
            GROUP BY order_day
            ORDER BY order_day DESC
        ";
        return pdo_query($sql);
    }

    // Thống kê số lượng bán theo sản phẩm
   public function get_order_product_statistics() {
    $sql = "
        SELECT 
            categories.name AS cate_name,  -- Tên danh mục
            products.product_id,
            products.name AS product_name,
            COUNT(orderdetails.order_id) AS count_orders,  -- Số đơn hàng
            SUM(orderdetails.quantity) AS total_quantity_sold  -- Tổng số lượng đã bán
        FROM orderdetails
        JOIN products ON orderdetails.product_id = products.product_id
        JOIN categories ON products.category_id = categories.category_id
        GROUP BY categories.category_id, categories.name, products.product_id, products.name
        ORDER BY total_quantity_sold DESC
    ";
    return pdo_query($sql);
}


    // Thống kê tổng số đơn hàng và doanh thu
    public function get_statistics() {
        $sql = "
            SELECT 
                COUNT(*) AS total_orders, 
                SUM(total) AS total_revenue 
            FROM orders
        ";
        return pdo_query_one($sql);
    }

    // Lấy các đơn hàng mới nhất với giới hạn
    public function get_order_top_limit($limit) {
        $sql = "SELECT * FROM orders ORDER BY date DESC LIMIT ?";
        return pdo_query($sql, $limit);
    }

    // Thống kê sản phẩm theo danh mục
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
            GROUP BY categories.category_id, categories.name
        ";
        return pdo_query($sql);
    }
}

// Khởi tạo đối tượng OrderModel
$OrderModel = new OrderModel();

?>
