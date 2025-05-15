<?php
require_once './models/ReviewModel.php';
$ReviewModel = new ReviewModel();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user']['id'];
    $order_id = $_POST['order_id'];
    $product_ids = $_POST['product_ids'];
    $ratings = $_POST['ratings'];
    $comments = $_POST['comments'];

    foreach ($product_ids as $index => $product_id) {
        $rating = (int)$ratings[$index];
        $comment = trim($comments[$index]);

        if ($rating > 0 && $comment != "") {
            $exists = $ReviewModel->check_review_exists($user_id, $product_id, $order_id);
            if (!$exists) {
                $ReviewModel->insert_review($user_id, $product_id, $order_id, $rating, $comment);
            }
        }
    }

    echo "<script>alert('Cảm ơn bạn đã đánh giá!'); window.location.href = 'index.php?url=don-hang';</script>";
}
