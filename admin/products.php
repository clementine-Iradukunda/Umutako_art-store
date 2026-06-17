<?php
$pageTitle  = 'Products';
$activePage = 'products';
require_once '_header.php';
require_once '../php/db_connect.php';

// DELETE
if (isset($_GET['delete'])) {
    $id  = (int)$_GET['delete'];
    $row = $conn->query("SELECT image FROM products WHERE id=$id")->fetch_assoc();
    if (!empty($row['image'])) {
        $file = '../images/products/' . basename($row['image']);
        if (file_exists($file)) unlink($file);
    }
    $conn->query("DELETE FROM order_items WHERE product_id=$id");
    $conn->query("DELETE FROM products WHERE id=$id");
    header('Location: products.php?msg=deleted'); exit;
}

// ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (int)($_POST['id']           ?? 0);
    $name  = trim($_POST['name']          ?? '');
    $desc  = trim($_POST['description']   ?? '');
    $price = (float)($_POST['price']      ?? 0);
    $stock = (int)($_POST['stock']        ?? 0);
    $catid = (int)($_POST['category_id']  ?? 0);

    // Handle image upload
    $imagePath = trim($_POST['existing_image'] ?? '');

    if (!empty($_FILES['image']['name'])) {
        $allowed   = ['jpg','jpeg','png','gif','webp'];
        $ext       = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $filename  = 'product_' . time() . '_' . mt_rand(100,999) . '.' . $ext;
            $uploadDir = '../images/products/';
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                // Delete old image if editing
                if (!empty($imagePath)) {
                    $old = $uploadDir . basename($imagePath);
                    if (file_exists($old)) unlink($old);
                }
                $imagePath = 'images/products/' . $filename;
            }
        }
    }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE products SET name=?,description=?,price=?,stock=?,category_id=?,image=? WHERE id=?");
        $stmt->bind_param('ssdiisi', $name, $desc, $price, $stock, $catid, $imagePath, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name,description,price,stock,category_id,image) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('ssdiis', $name, $desc, $price, $stock, $catid, $imagePath);
    }
    $stmt->execute();
    header('Location: products.php?msg=saved'); exit;
}

$products   = $conn->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY c.id, p.id")->fetch_all(MYSQLI_ASSOC);
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    foreach ($products as $p) { if ($p['id']==$eid) { $editing=$p; break; } }
}
$conn->close();

$catIcons = ['Baskets & Weaving'=>'🧺','Pottery & Ceramics'=>'🏺','Wood Carvings'=>'🪵','Jewelry & Accessories'=>'📿','Textiles & Fabrics'=>'🎨'];
$baseUrl  = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/umutako_art_store/';
?>

<div class="admin-page-header">
    <h2>🛍️ Products</h2>
    <?php if (isset($_GET['msg'])): ?>
        <span class="success-flash">✅ <?= $_GET['msg']==='saved' ? 'Product saved!' : 'Product deleted!' ?></span>
    <?php endif; ?>
</div>

<!-- ADD / EDIT FORM -->
<div class="admin-card" style="margin-bottom:28px">
    <h4 style="margin-bottom:18px"><?= $editing ? '✏️ Edit Product' : '➕ Add New Product' ?></h4>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= $editing['id'] ?>"/>
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editing['image'] ?? '') ?>"/>
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($editing['name'] ?? '') ?>"/>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" class="admin-select" required>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($editing['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
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

        <!-- IMAGE UPLOAD -->
        <div class="form-group">
            <label>Product Image</label>
            <div class="img-upload-wrap">
                <!-- Preview -->
                <div class="img-preview" id="img-preview">
                    <?php if (!empty($editing['image'])): ?>
                        <img src="../<?= htmlspecialchars($editing['image']) ?>" alt="Current image"/>
                    <?php else: ?>
                        <span class="img-placeholder">📷<br/><small>No image</small></span>
                    <?php endif; ?>
                </div>
                <div class="img-upload-controls">
                    <label class="upload-btn" for="image-input">
                        📁 Choose Image
                        <input type="file" id="image-input" name="image" accept="image/*" onchange="previewImage(this)"/>
                    </label>
                    <small style="color:var(--text-400);display:block;margin-top:6px">
                        JPG, PNG, GIF, WEBP — max 5MB
                    </small>
                    <?php if (!empty($editing['image'])): ?>
                        <div class="img-url-box">
                            <label>Image URL</label>
                            <div class="img-url-row">
                                <input type="text" readonly value="<?= $baseUrl . htmlspecialchars($editing['image']) ?>" id="img-url-field"/>
                                <button type="button" class="copy-btn" onclick="copyImgUrl()">📋 Copy</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-top:6px">
            <button type="submit" class="admin-btn"><?= $editing ? '💾 Save Changes' : '➕ Add Product' ?></button>
            <?php if ($editing): ?><a href="products.php" class="admin-btn-secondary">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<!-- PRODUCTS TABLE -->
<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Image URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td>
                <?php if (!empty($p['image'])): ?>
                    <img src="../<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"
                         style="width:56px;height:56px;object-fit:cover;border-radius:8px;border:1px solid var(--cream-d)"/>
                <?php else: ?>
                    <span style="font-size:2rem"><?= $catIcons[$p['category_name']] ?? '🎁' ?></span>
                <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
            <td><?= htmlspecialchars($p['category_name']) ?></td>
            <td>RWF <?= number_format($p['price']) ?></td>
            <td><?= $p['stock'] <= 5
                    ? "<span style='color:#C0392B;font-weight:700'>{$p['stock']}</span>"
                    : $p['stock'] ?>
            </td>
            <td>
                <?php if (!empty($p['image'])): ?>
                    <div class="url-cell">
                        <input type="text" readonly value="<?= $baseUrl . htmlspecialchars($p['image']) ?>"
                               class="url-input" onclick="this.select()"/>
                        <button class="copy-btn-sm" onclick="copyText(this)" title="Copy URL">📋</button>
                    </div>
                <?php else: ?>
                    <span style="color:var(--text-400);font-size:.8rem">No image</span>
                <?php endif; ?>
            </td>
            <td style="white-space:nowrap">
                <a href="products.php?edit=<?= $p['id'] ?>" class="view-link">Edit</a> &nbsp;
                <a href="products.php?delete=<?= $p['id'] ?>" class="view-link" style="color:#C0392B"
                   onclick="return confirm('Delete this product?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview"/>`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function copyImgUrl() {
    const field = document.getElementById('img-url-field');
    field.select();
    document.execCommand('copy');
    const btn = document.querySelector('.copy-btn');
    btn.textContent = '✅ Copied!';
    setTimeout(() => btn.textContent = '📋 Copy', 2000);
}

function copyText(btn) {
    const input = btn.previousElementSibling;
    input.select();
    document.execCommand('copy');
    btn.textContent = '✅';
    setTimeout(() => btn.textContent = '📋', 2000);
}
</script>

<?php require_once '_footer.php'; ?>
