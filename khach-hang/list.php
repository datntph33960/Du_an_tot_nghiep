<?php
    $list_users = $CustomerModel->select_all_users();

    if(isset($_GET['xoa'])) {
        $user_id = $_GET['xoa'];

        $check_comment = $CommentModel->check_remove_user($user_id);
        $check_order = $OrderModel->check_remove_user($user_id);

        // Xóa giỏ hàng trước khi xóa user
        $CustomerModel->delete_cart($user_id);
        

        if($check_order > 0 || $check_comment > 0) {
            echo "<script>alert('Tài khoản đang có đơn hàng và bình luận không thể xóa')</script>";
        }
        else {
            $CustomerModel->delete_user($user_id);

            header("Location: index.php?quanli=danh-sach-khach-hang");
        }

    }

?>



<!-- LIST PRODUCTS -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Danh sách tài khoản</h6>
            <a href="them-tai-khoan" class="btn btn-custom"><i class="fa fa-plus"></i> Thêm tài khoản</a>

        </div>

        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0" id="users-list">
                <thead>
                    <tr class="text-dark">

                        <th scope="col">#</th>
                        <th scope="col">Ảnh</th> 
                        <th scope="col">Họ tên</th> 
                        <th scope="col">Email</th> 
                        <th scope="col">Số điện thoại</th>   
                        <th scope="col">Vai trò</th>      
                        <th scope="col">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($list_users as $value) {
                        extract($value);
                        $i++;
                    
                    ?>
                    <tr>
                        <td><?=$i?></td>
                        <td>
                            <img style="max-width: 50px;" src="../upload/<?=$image?>" alt="">
                        </td>
                        <td><?=$full_name?></td>
                        <td>
                            <?=$email?>
                        </td>
                        <td> <?=$phone?> </td>
                        <td> <?php
                            if($role == 0) {
                                echo "Khách hàng";
                            }elseif($role == 1) {
                                echo "Nhân viên";
                            }
                            ?> 
                        </td>
                        <td>
                            <a 
                            href="capnhat-tai-khoan&id=<?=$user_id ?>" class="btn-sm btn-warning">Sửa</a>

                            <a onclick="return confirm('Bạn có chắc chắn muốn xóa')"  
                            href="danh-sach-khach-hang&xoa=<?=$user_id ?>" class="btn-sm btn-danger">Xóa</a>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    
                    
                </tbody>
            </table>
            
        </div>
    </div>
</div>
<!-- LIST PRODUCTS END -->
<style>
    td {
        height: 50px;
    }
</style>