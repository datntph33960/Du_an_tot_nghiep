<?php
class ReviewModel extends BaseModel {
    public function insert_review($user_id, $product_id, $order_id, $rating, $comment ) {
        $sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, comment, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        return pdo_execute($sql, $user_id, $product_id, $order_id, $rating, $comment);
    }

    public function check_review_exists($user_id, $product_id, $order_id) {
        $sql = "SELECT * FROM reviews WHERE user_id = ? AND product_id = ? AND order_id = ?";
        return pdo_query_one($sql, $user_id, $product_id, $order_id);
    }

    public function get_reviews_by_product($product_id) {
        $sql = "SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC";
        return pdo_query($sql, $product_id);
    }
}