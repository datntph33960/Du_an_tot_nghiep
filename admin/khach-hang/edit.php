<?php

    $error = array(
        'email' => '',
        'fullname' => '',
        'username' => '',
        'password' => '',
        'password_confirm' => '',
        'phone' => '',
        'address' => '',     
    );

    $temp = array(
        'email' => '',
        'full_name' => '',
        'username' => '',
        'password' => '',
        'password_confirm' => '',
        'phone' => '',
        'address' => '',     
    );
    $success = '';

    if(isset($_GET['id']) && $_GET['id'] > 0) {
        $user_id = $_GET['id'];

        $user_one = $CustomerModel->select_user_by_id($user_id);
        
        extract($user_one);
    }else {
        header("Location: index.php?quanli=danh-sach-khach-hang");
    }


    $list_users = $CustomerModel->select_users();
    
    // Kiểm tra nếu form được submit
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
        
        // Lấy dữ liệu từ form
        $email = trim($_POST["email"]);
        $full_name = trim($_POST["full_name"]);
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        $phone = trim($_POST["phone"]);
        $address = trim($_POST["address"]);
        $role = $_POST["role"];
        $image = "user-default.png";

        //MÃ hóa password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    

        if((strlen($email) > 255)) {
            $error['email'] = 'Email không được quá 255 ký tự';
        }

        if((strlen($full_name) > 255)) {
            $error['fullname'] = 'Họ tên không được quá 255 ký tự';
        }



        if (strlen($password) < 8) {
            $error['password'] = 'Mật khẩu phải chứa ít nhất 8 ký tự.';
        }

        if (!preg_match('/^(03|05|07|08|09)(([0-9]){8})/', $phone)) {
            $error['phone'] = 'Số điện thoại không đúng định dạng.';
        }

        if((strlen($address) > 255)) {
            $error['address'] = 'Địa chỉ không được quá 255 ký tự';
        }

        if(empty(array_filter($error))) {
            //Insert dữ liệu user
            $CustomerModel->update_user($email, $full_name, $username, $hashed_password, $phone, $address, $role, $user_id);
            $success = 'Cập nhật tải khoản thành công';
            
        }else {
            $temp['email'] = $email;
            $temp['full_name'] = $full_name;
            $temp['username'] = $username;
            $temp['password'] = $password;
            $temp['password'] = $password;
            $temp['phone'] = $phone;
            $temp['address'] = $address;
        }
    }
    $html_alert = $BaseModel->alert_error_success('', $success);
    
?>

<div class="container-fluid pt-4" style="margin-bottom: 110px;">

    <form class="row g-4" action="" method="post" enctype="multipart/form-data">

        <div class="col-sm-12 col-xl-9">

            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">
                    <a href="index.php?quanli=danh-sach-khach-hang" class="link-not-hover">Tài khoản</a> 
                    / Cập nhật
                </h6>
                <?=$html_alert?>
                <label for="">Email</label>
                <div class="mb-1">
                    <input name="email" type="email" class="form-control" value="<?=$email?>" required>   
                    <span class="text-danger err" ><?=$error['email']?></span>
                </div>
                <label for="">Họ và tên</label>
                <div class="mb-1">
                    <input name="full_name" type="text" class="form-control" value="<?=$full_name?>" required>   
                    <span class="text-danger err" ><?=$error['fullname']?></span>
                </div>
                <label for="">Tên đăng nhập</label>
                <div class="mb-1">
                    <input name="username" type="text" value="<?=$username?>" class="form-control" required>   
                    <span class="text-danger err" ><?=$error['username']?></span>
                </div>
                <label for="">Mật khẩu</label>
                <div class="mb-1">
                    <input name="password" type="text" value="" class="form-control" required>   
                    <span class="text-danger err" ><?=$error['password']?></span>
                </div>
                
                <label for="">Số điện thoại</label>
                <div class="mb-1">
                    <input name="phone" type="text" value="<?=$phone?>" class="form-control" required>   
                    <span class="text-danger err" ><?=$error['phone']?></span>
                </div>
                <label for="">Địa chỉ</label>
                <div class="mb-1">
                    <input name="address" type="text" value="<?=$address?>" class="form-control" required>   
                    <span class="text-danger err" ><?=$error['address']?></span>
                </div>
                
                <label for="Select">Vai trò</label>
                <div class="mb-3">
                    <select name="role" class="form-select" id="Select"
                        aria-label="Floating label select example">
                        <!-- <option selected value="0">Khách hàng</option>
                        <option value="1">Nhân viên</option> -->


                        <?php if($role == 1) {?>
                            <option selected value="1">Nhân viên</option>
                            <option value="0">Khách hàng</option>
                        <?php 

                        } else{ 

                        ?>
                            <option value="1">Nhân viên</option>
                            <option selected  value="0">Khách hàng</option>
                        <?php }?>
                    </select>
                    
                </div>
                                        
            </div>
        </div>
        <div class="col-sm-12 col-xl-3">
            <div class="bg-light rounded h-100 p-4">
                
                <h6 class="mb-4">
                    <input type="submit" name="add_user" value="Cập nhật tài khoản" class="btn btn-custom">
                    
                </h6>

            </div>
        </div>

    </form>
</div>
<!-- Form End -->
<style>
    .err {
        display: inline-block;
        height: 22px;
    }
</style>