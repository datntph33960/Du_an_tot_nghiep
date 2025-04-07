<?php
class CartModel {
    public function select_all_carts($user_id) {
        $sql = "SELECT * FROM carts WHERE user_id = $user_id ORDER BY cart_id DESC";

        return pdo_query($sql);
    }

    public function select_cart_by_id($product_id, $user_id, $product_size, $product_color) {
        $sql = "SELECT * FROM carts 
                WHERE product_id = ? AND user_id = ? 
                AND product_size = ? AND product_color = ?";
    
        return pdo_query_one($sql, $product_id, $user_id, $product_size, $product_color);
    }
    

    public function select_mini_carts($user_id, $limit) {
        $sql = "SELECT * FROM carts WHERE user_id = $user_id ORDER BY cart_id DESC LIMIT $limit";

        return pdo_query($sql);
    }

    public function count_cart($user_id) {
        $sql = "SELECT cart_id FROM carts WHERE user_id = $user_id";

        return pdo_query($sql);
    }

    public function insert_cart($product_id, $user_id, $product_name, $product_price, $product_quantity, $product_image, $product_size, $product_color) {
        $sql = "INSERT INTO carts 
           (product_id, user_id, product_name, product_price, product_quantity, product_image, product_size, product_color)
            VALUES (?,?,?,?,?,?,?,?)";

        pdo_execute($sql, $product_id, $user_id, $product_name, $product_price, $product_quantity, $product_image, $product_size, $product_color);
    }

    public function update_cart($product_qty, $product_id, $user_id, $product_size, $product_color) {
        $sql = "UPDATE carts SET 
            product_quantity = ?
            WHERE product_id = ? AND user_id = ? AND product_size = ? AND product_color = ?";
        
        pdo_execute($sql, $product_qty, $product_id, $user_id, $product_size, $product_color);
    }
    

    public function delete_product_in_cart($product_id, $user_id) {
        $sql = "DELETE FROM carts WHERE product_id = ? AND user_id = ?";
        pdo_execute($sql, $product_id, $user_id);
    }

    public function delete_cart_by_id($cart_id) {
        $sql = "DELETE FROM carts WHERE cart_id = ?";
        pdo_execute($sql, $cart_id);
    }
}

$CartModel = new CartModel();
