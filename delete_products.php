<?php
$host = 'localhost';
$db = 'product_db';
$user = 'root';
$pass = '';

try{
    $pdo = new PDO ("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $s){
    die("Connection failed: " . $s->getMessage());
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['selected'])) {
    $ids = $_POST['selected'];

    $selected = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("DELETE FROM products WHERE sku in ($selected)");
    $stmt->execute($ids);

    header("Location: view_products.php");
    exit;
}
?>
