<?php
// ================================================
//  get_categories.php — Return all categories
// ================================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

$sql    = "SELECT * FROM categories ORDER BY name";
$result = $conn->query($sql);

$categories = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

echo json_encode($categories);
$conn->close();
?>
