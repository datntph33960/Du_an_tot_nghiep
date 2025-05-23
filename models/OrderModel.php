<?php
class OrderModel {
    public function select_order_id() {
        $sql = "SELECT order_id FROM orders ORDER BY date DESC LIMIT 1";
        return pdo_query_one($sql);
    }

    // Lấy danh sách đơn hàng theo user
    public function select_list_orders($user_id) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_id DESC";
        return pdo_query($sql, $user_id);
    }

    // Lấy chi tiết đơn hàng + sản phẩm
    public function select_orderdetails_and_products($order_id) {
        $sql = "
            SELECT
                products.product_id,
                products.name AS product_name,
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

    // Lấy đầy đủ thông tin đơn hàng + người dùng + chi tiết sản phẩm
    public function getFullOrderInformation($user_id, $order_id) {
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
                orders.receiver_address,
                users.full_name,
                users.email,
                users.phone AS user_phone,
                orderdetails.product_id,
                orderdetails.quantity,
                orderdetails.price,
                orderdetails.sizes AS size,
                orderdetails.colors AS color,
                products.name AS product_name,
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
                orders.user_id = ? AND orders.order_id = ?
        ";
        return pdo_query($sql, $user_id, $order_id);
    }

    // Thêm đơn hàng mới
    public function insert_orders($user_id, $order_code, $total, $address, $phone, $note, $payment_method_id, $receiver_name, $receiver_phone, $receiver_address) {
        $sql = "INSERT INTO orders(order_code, user_id, total, address, phone, note, payment_method_id, receiver_name, receiver_phone, receiver_address)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        pdo_execute($sql, $order_code, $user_id, $total, $address, $phone, $note, $payment_method_id, $receiver_name, $receiver_phone, $receiver_address);
    }

    // Thêm chi tiết đơn hàng
    public function insert_orderdetails($order_id, $product_id, $quantity, $price, $sizes, $colors, $name) {
        $sql = "INSERT INTO orderdetails(order_id, product_id, quantity, price, sizes, colors, name) VALUES(?,?,?,?,?,?,?)";
        pdo_execute($sql, $order_id, $product_id, $quantity, $price, $sizes, $colors, $name);
        return true;
    }

    // Xóa giỏ hàng theo user_id
    public function delete_cart_by_ids($user_id, $cart_ids = []) {
        if (empty($cart_ids)) return;
    
        $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
        $sql = "DELETE FROM cart WHERE user_id = ? AND cart_id IN ($placeholders)";
        $params = array_merge([$user_id], $cart_ids);
        pdo_execute($sql, $user_id);
    }
    public function get_order_by_id_and_user($order_id, $user_id) {
        $sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
        return pdo_query_one($sql, $order_id, $user_id);
    }
    public function update_status_order($new_status, $order_id) {
        $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
        pdo_execute($sql, $new_status, $order_id);
    }
    public function delete_cart_item($user_id, $product_id) {
        $sql = "DELETE FROM carts WHERE user_id = ? AND product_id = ?";
        pdo_execute($sql, $user_id, $product_id);
    }
    
}

$OrderModel = new OrderModel();
?>
