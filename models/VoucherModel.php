<?php
class VoucherModel
{
  private $conn;

  public function __construct()
  {
    // Kết nối CSDL (bạn có thể thay thế bằng file connect riêng)
    $this->conn = new PDO("mysql:host=localhost;dbname=sua_websach;charset=utf8", "root", "");
  }

  public function get_all_vouchers()
  {
    $stmt = $this->conn->prepare("SELECT * FROM vouchers ORDER BY start_date DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get_received_voucher_ids($user_id)
  {
    $sql = "SELECT voucher_id FROM user_vouchers WHERE user_id = ?";
    return array_column(pdo_query($sql, $user_id), 'voucher_id');
  }

  public function user_received_voucher($user_id, $voucher_id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM user_vouchers WHERE user_id = ? AND voucher_id = ?");
    $stmt->execute([$user_id, $voucher_id]);
    return $stmt->fetch() ? true : false;
  }
  public function reduce_voucher_quantity($voucher_id)
  {
    $stmt = $this->conn->prepare("UPDATE vouchers SET quantity = quantity - 1 WHERE voucher_id = ? AND quantity > 0");
    return $stmt->execute([$voucher_id]);
  }

  public function give_voucher_to_user($user_id, $voucher_id)
  {
    if (!$this->user_received_voucher($user_id, $voucher_id)) {
      $this->conn->beginTransaction();
      try {
        $stmt = $this->conn->prepare("INSERT INTO user_vouchers (user_id, voucher_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $voucher_id]);

        $this->reduce_voucher_quantity($voucher_id);

        $this->conn->commit();
        return true;
      } catch (Exception $e) {
        $this->conn->rollBack();
        return false;
      }
    }
    return false;
  }
  public function get_valid_vouchers()
  {
    $stmt = $this->conn->query("SELECT * FROM vouchers WHERE status = 1 AND end_date >= CURDATE()");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
