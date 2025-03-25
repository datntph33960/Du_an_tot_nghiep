<?php
    $list_posts = $PostModel->select_all_posts();

    $success = '';
    if (isset($_GET['xoa']) && intval($_GET['xoa']) > 0) {
        $post_id = intval($_GET['xoa']);
        $PostModel->delete_post($post_id);
        setcookie('success_delete', 'Đã xóa thành công 1 bài viết', time() + 5, '/');
        header("Location: index.php?quanli=danh-sach-bai-viet");
        exit();
    }

    if (!empty($_COOKIE['success_delete'])) {
        $success = $_COOKIE['success_delete'];
    }
    $html_alert = $BaseModel->alert_error_success('', $success);
?>

<!-- LIST PRODUCTS -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Danh sách sản phẩm</h6>
            <a href="them-bai-viet" class="btn btn-custom"><i class="fa fa-plus"></i> Thêm bài viết</a>
        </div>

        <div class="table-responsive">
            <?= $html_alert ?>
            <table class="table text-start align-middle table-bordered table-hover mb-0" id="post-list">
                <thead>
                    <tr class="text-dark">
                        <th scope="col">#</th>
                        <th scope="col">Tiêu đề</th> 
                        <th scope="col">Sản phẩm</th> 
                        <th scope="col">Chuyên mục</th>
                        <th scope="col">Giá</th>
                        <th scope="col">Màu sắc</th>
                        <th scope="col">Ngày đăng</th>           
                        <th scope="col">Chỉnh sửa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    <?php foreach ($list_posts as $value): ?>
                        <?php extract($value); $i++; ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td style="min-width: 180px;"><?= htmlspecialchars($title) ?></td>
                            <td style="min-width: 120px;"><?= htmlspecialchars($author) ?></td>
                            <td style="min-width: 180px;"><?= htmlspecialchars($category_name) ?></td>
                            <td style="min-width: 100px;"><?= number_format($price, 0, ',', '.') ?> VNĐ</td>
                            <td style="min-width: 120px;"><?= htmlspecialchars($color) ?></td>
                            <td style="min-width: 180px;"><?= htmlspecialchars($created_at) ?></td>
                            <td style="min-width: 180px;">
                                <a href="cap-nhat-bai-viet&id=<?= $post_id ?>" class="btn-sm btn-success">Xem</a>
                                <a href="cap-nhat-bai-viet&id=<?= $post_id ?>" class="btn-sm btn-secondary">Sửa</a>
                                <a href="#" class="btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= $post_id ?>">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL XÁC NHẬN XÓA -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa bài viết này không? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Xóa</a>
            </div>
        </div>
    </div>
</div>

<style>
    td {
        height: 50px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const deleteModal = document.getElementById("confirmDeleteModal");
        deleteModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const postId = button.getAttribute("data-id");
            document.getElementById("confirmDeleteBtn").href = "danh-sach-bai-viet&xoa=" + postId;
        });
    });
</script>
