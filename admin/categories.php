<?php
$pageTitle  = 'Categories';
$activePage = 'categories';
require_once '_header.php';
require_once '../php/db_connect.php';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id=$id");
    header('Location: categories.php?msg=deleted'); exit;
}

// ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id']   ?? 0);
    $name = trim($_POST['name']  ?? '');
    $desc = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon']  ?? '🎁');

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE categories SET name=?,description=?,icon=? WHERE id=?");
        $stmt->bind_param('sssi', $name, $desc, $icon, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name,description,icon) VALUES (?,?,?)");
        $stmt->bind_param('sss', $name, $desc, $icon);
    }
    $stmt->execute();
    header('Location: categories.php?msg=saved'); exit;
}

$categories = $conn->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id=p.category_id GROUP BY c.id ORDER BY c.name")->fetch_all(MYSQLI_ASSOC);

$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    foreach ($categories as $c) { if ($c['id']==$eid) { $editing=$c; break; } }
}
$conn->close();
?>

<div class="admin-page-header">
    <h2>🏷️ Categories</h2>
    <?php if (isset($_GET['msg'])): ?>
        <span class="success-flash">✅ <?= $_GET['msg']==='saved'?'Saved!':'Deleted!' ?></span>
    <?php endif; ?>
</div>

<div class="admin-card" style="margin-bottom:28px">
    <h4 style="margin-bottom:18px"><?= $editing ? '✏️ Edit Category' : '➕ Add Category' ?></h4>
    <form method="POST">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"/><?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label>Category Name *</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($editing['name'] ?? '') ?>"/>
            </div>
            <div class="form-group">
                <label>Icon (emoji)</label>
                <input type="text" name="icon" value="<?= htmlspecialchars($editing['icon'] ?? '🎁') ?>" style="width:80px"/>
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="description" value="<?= htmlspecialchars($editing['description'] ?? '') ?>"/>
        </div>
        <div style="display:flex;gap:12px">
            <button type="submit" class="admin-btn"><?= $editing ? '💾 Save' : '➕ Add' ?></button>
            <?php if ($editing): ?><a href="categories.php" class="admin-btn-secondary">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <thead><tr><th>Icon</th><th>Name</th><th>Description</th><th>Products</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $c): ?>
        <tr>
            <td style="font-size:1.8rem"><?= htmlspecialchars($c['icon']) ?></td>
            <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
            <td><?= htmlspecialchars($c['description']) ?></td>
            <td><?= $c['product_count'] ?></td>
            <td>
                <a href="categories.php?edit=<?= $c['id'] ?>" class="view-link">Edit</a> &nbsp;
                <a href="categories.php?delete=<?= $c['id'] ?>" class="view-link" style="color:#C0392B"
                   onclick="return confirm('Delete this category?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '_footer.php'; ?>
