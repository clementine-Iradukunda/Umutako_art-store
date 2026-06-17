<?php
$pageTitle  = 'Products';
$activePage = 'products';
require_once '_header.php';
require_once '../php/db_connect.php';

$msg = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM order_items WHERE product_id=$id");
    $conn->query("DELETE FROM products WHERE id=$id");
    header('Location: products.php?msg=deleted'); exit;
}

// ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (int)($_POST['id'] ?? 0);
    $name  = trim($_POST['name']        ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price']    ?? 0);
    $stock = (int)($_POST['stock']      ?? 0);
    $catid = (int)($_POST['category_id']?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE products SET name=?,description=?,price=?,stock=?,category_id=? WHERE id=?");
        $stmt->bind_param('ssdiii', $name, $desc, $price, $stock, $catid, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name,description,price,stock,category_id) VALUES (?,?,?,?,?)");
        $stmt->bind_param('ssdii', $name, $desc, $price, $stock, $catid);
    }
    $stmt->execute();
    header('Location: products.php?msg=saved'); exit;
}

$products   = $conn->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY c.id, p.id")->fetch_all(MYSQLI_ASSOC);
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Edit mode
$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    foreach ($products as $p) { if ($p['id']==$eid) { $editing=$p; break; } }
}
$conn->close();

$catIcons = ['Baskets & Weaving'=>'🧺','Pottery & Ceramics'=>'🏺','Wood Carvings'=>'🪵','Jewelry & Accessories'=>'📿','Textiles & Fabrics'=>'🎨'];
?>

<div class="admin-page-header">
    <h2>🛍️ Products</h2>
    <?php if (isset($_GET['msg'])): ?>
        <span class="success-flash">✅ <?= $_GET['msg']==='saved'?'Product saved!':'Product deleted!' ?></span>
    <?php endif; ?>
</div>

<!-- ADD / EDIT FORM -->
<div class="admin-card" style="margin-bottom:28px">
    <h4 style="margin-bottom:18px"><?= $editing ? '✏️ Edit Product' : '➕ Add New Product' ?></h4>
    <form method="POST">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"/><?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($editing['name'] ?? '') ?>"/>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" class="admin-select" required>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?=$c['id']?>" <?= ($editing['category_id']??0)==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Price (RWF) *</label>
                <input type="number" name="price" min="0" required value="<?= $editing['price'] ?? '' ?>"/>
            </div>
            <div class="form-group">
                <label>Stock *</label>
                <input type="number" name="stock" min="0" required value="<?= $editing['stock'] ?? 0 ?>"/>
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($editing['description'] ?? '') ?></textarea>
        </div>
        <div style="display:flex;gap:12px">
            <button type="submit" class="admin-btn"><?= $editing ? '💾 Save Changes' : '➕ Add Product' ?></button>
            <?php if ($editing): ?><a href="products.php" class="admin-btn-secondary">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<!-- PRODUCTS TABLE -->
<div class="table-wrap">
    <table class="admin-table">
        <thead><tr><th>Icon</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td style="font-size:1.6rem"><?= $catIcons[$p['category_name']] ?? '🎁' ?></td>
            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
            <td><?= htmlspecialchars($p['category_name']) ?></td>
            <td>RWF <?= number_format($p['price']) ?></td>
            <td><?= $p['stock'] <= 5 ? "<span style='color:#C0392B;font-weight:700'>{$p['stock']}</span>" : $p['stock'] ?></td>
            <td>
                <a href="products.php?edit=<?= $p['id'] ?>" class="view-link">Edit</a> &nbsp;
                <a href="products.php?delete=<?= $p['id'] ?>" class="view-link" style="color:#C0392B"
                   onclick="return confirm('Delete this product?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '_footer.php'; ?>
