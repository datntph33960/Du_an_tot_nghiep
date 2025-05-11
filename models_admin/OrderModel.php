<?php
    class OrderModel{
        public function select_order_id() {
            $sql = "SELECT order_id FROM orders ORDER BY date DESC LIMIT 1";

            return pdo_query_one($sql);
        }


        // Select thông tin đon hàng
        public function select_list_orders($user_id) {
            $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_id DESC";

            return pdo_query($sql, $user_id);
        }

        public function select_orderdetails_and_products($order_id) {
            $sql = "
    SELECT
        products.product_id,
     orderdetails.name AS product_name
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
                    users.full_name,
                    users.email,
                    users.phone AS user_phone,
                    orderdetails.product_id,
                    orderdetails.quantity,
                    orderdetails.price,
                    orderdetails.sizes AS sizes,
                    orderdetails.colors AS colors,
                    orderdetails.name AS product_name,  -- Lấy tên sản phẩm từ orderdetails
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
        
        
        public function update_status_order($status, $order_id) {
            $sql = "UPDATE orders SET status = ? WHERE order_id = ?";

            pdo_execute($sql, $status, $order_id);
        }

        public function insert_orders($user_id, $total, $address, $phone, $note, $payment_method_id) {
            $sql = "INSERT INTO orders(user_id, total, address, phone, note, payment_method_id) VALUES(?,?,?,?,?,?)";
            pdo_execute($sql, $user_id, $total, $address, $phone, $note, $payment_method_id);
        }
        

        public function insert_orderdetails($order_id, $product_id, $quantity, $price, $sizes, $colors, $product_name) {
            $sql = "INSERT INTO orderdetails(order_id, product_id, quantity, price, sizes, colors, name) VALUES(?,?,?,?,?,?,?)";
            pdo_execute($sql, $order_id, $product_id, $quantity, $price, $sizes, $colors, $product_name);
        }
        

        public function delete_cart_by_user_id($user_id) {
            $sql = "DELETE FROM carts WHERE user_id = ?";
            pdo_execute($sql, $user_id);
        }
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
        
        public function get_payment_method($order_id) {
            $sql = "SELECT payment_method_id FROM orders WHERE order_id = ?";
            $row = pdo_query_one($sql, $order_id);
            return $row['payment_method_id'];
        }        
        
    }

    $OrderModel = new OrderModel();
?>
