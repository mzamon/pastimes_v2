<?php
$pageTitle = 'Edit Listing';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireSellerOrAdmin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect(BASE_URL . 'products/index.php');

$stmt = mysqli_prepare($conn, "SELECT * FROM tblProducts WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$product || ($product['seller_id'] != ($_SESSION['user_id'] ?? 0) && !isAdmin())) {
    redirect(BASE_URL . 'products/index.php');
}

$cats   = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM categories ORDER BY name"), MYSQLI_ASSOC);
$errors = [];
$post   = $product;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = [
        'title'       => sanitize($_POST['title']       ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'price'       => floatval($_POST['price']        ?? 0),
        'category_id' => intval($_POST['category_id']     ?? 0),
        'condition'   => sanitize($_POST['condition']    ?? 'Good'),
        'quantity'    => max(0, intval($_POST['quantity']  ?? 0)),
    ];

    if (empty($post['title']))       $errors[] = 'Title is required.';
    if (empty($post['description']))  $errors[] = 'Description is required.';
    if ($post['price'] <= 0)         $errors[] = 'Price must be greater than zero.';
    if ($post['category_id'] <= 0)   $errors[] = 'Please select a category.';

    $image_path = $product['image'];
    if (!empty($_FILES['image']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $errors[] = 'Invalid image format.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image too large (max 2 MB).';
        } else {
            $filename = uniqid('img_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $filename)) {
                $image_path = 'uploads/' . $filename;
            } else {
                $errors[] = 'Failed to upload image.';
            }
        }
    }

    if (empty($errors)) {
        $status = $post['quantity'] > 0 ? 'active' : 'sold';
        $upd = mysqli_prepare($conn,
            "UPDATE tblProducts
             SET category_id=?, title=?, description=?, price=?, `condition`=?, image=?, quantity=?, status=?, updated_at=NOW()
             WHERE id=?");
        mysqli_stmt_bind_param($upd, 'issdssisi',
            $post['category_id'], $post['title'], $post['description'], $post['price'],
            $post['condition'], $image_path, $post['quantity'], $status, $id);
        if (mysqli_stmt_execute($upd)) {
            redirect(BASE_URL . 'products/view.php?id=' . $id);
        } else {
            $errors[] = 'Failed to update listing.';
        }
        mysqli_stmt_close($upd);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">Edit Listing</h1>
<div class="form-wrap">
    <?php foreach ($errors as $e) echo displayError($e); ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" required value="<?php echo h($post['title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">Select category…</option>
                <?php foreach ($cats as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($post['category_id'] ?? 0) == $cat['id'] ? 'selected' : ''; ?>><?php echo h($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row cols-2">
            <div class="form-group">
                <label for="price">Price (R)</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" min="0.01" required value="<?php echo h($post['price'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="condition">Condition</label>
                <select id="condition" name="condition" class="form-control" required>
                    <?php foreach (['New','Like New','Good','Fair','Poor'] as $cond): ?>
                        <option value="<?php echo $cond; ?>" <?php echo ($post['condition'] ?? '') === $cond ? 'selected' : ''; ?>><?php echo $cond; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="0" value="<?php echo h($post['quantity'] ?? 1); ?>">
            <small class="text-muted">Set to 0 to mark as sold.</small>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" required><?php echo h($post['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Change Image (leave empty to keep current)</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>
        <div style="display:flex;gap:0.75rem;">
            <button type="submit" class="btn btn-primary" style="flex:1;">Save Changes</button>
            <a href="<?php echo BASE_URL; ?>products/view.php?id=<?php echo $id; ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
