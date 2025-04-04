<?php
    class CustomerModel {

        public function select_users() {
            $sql = "SELECT username, full_name, email, phone FROM users";

            return pdo_query($sql);
        }

        public function select_all_users() {
            $sql = "SELECT * FROM users ORDER BY user_id DESC";

            return pdo_query($sql);
        }

        public function select_user_by_id($user_id) {
            $sql = "SELECT * FROM users WHERE user_id = ?";

            return pdo_query_one($sql, $user_id);
        }

        public function user_insert($username, $password, $full_name, $image, $email, $phone, $address, $role) {
            $sql = "INSERT INTO users(username, password, full_name, image, email, phone, address, role) VALUES(?,?,?,?,?,?,?,?)";

            pdo_execute($sql, $username, $password, $full_name, $image, $email, $phone, $address, $role);
        }

        public function get_user_admin($username) {
            $sql = "SELECT * FROM users WHERE username = ? AND role = 1";

            return pdo_query($sql, $username);
        }

        public function update_user($email, $full_name, $username, $password, $phone, $address, $role, $user_id) {
            $sql = "UPDATE users SET 
            email = '".$email."' ";

            $sql .= ", full_name = '".$full_name."'";

            $sql .= ", username = '".$username."'";

            $sql .= ", password = '".$password."'";

            $sql .= ", phone = '".$phone."'";

            $sql .= ", address = '".$address."'";

            $sql .= ", role = '".$role."'
                    WHERE user_id = ".$user_id;

            pdo_execute($sql);
        }

        public function delete_cart($user_id) {
            $sql = "DELETE FROM carts WHERE user_id = ?";
            pdo_execute($sql, $user_id);
        }

        public function delete_user($user_id) {
            $sql = "DELETE FROM users WHERE user_id = ?";
            pdo_execute($sql, $user_id);
        }

    }

    $CustomerModel = new CustomerModel();
?>
