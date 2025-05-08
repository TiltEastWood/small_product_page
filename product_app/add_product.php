<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db = 'product_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $product_type = $_POST['product_type'];

    $attribute_type = '';
    $attribute_value = '';
    
    if ($product_type == 'dvd') {
        $attribute_type = 'size';
        $attribute_value = $_POST['size'];
    } elseif ($product_type == 'book') {
        $attribute_type = 'weight';
        $attribute_value = $_POST['weight'];
    } elseif ($product_type == 'furniture') {
        $attribute_type = "dimensions";
        $height = $_POST['height'];
        $width = $_POST['width'];
        $length = $_POST['length'];
        $attribute_value = "{$height}x{$width}x{$length}";
    }
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE sku = ?");
    $checkStmt->execute([$sku]);

    if ($checkStmt->fetchColumn()>0) {
        echo json_encode(["error" => "Item with this SKU already exists."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO products (sku, name, price, attribute_type, attribute_value) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$sku, $name, $price, $attribute_type, $attribute_value]);

    echo json_encode(["redirect" => "view_products.php"]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        .buttons {
            display: flex;
            justify-content: flex-start
        }
    </style>
    
    <script>
        function updateAttributeField() {
        const type = document.getElementById("product_type").value;

        const dvd = document.getElementById("dvd_fields");
        const book = document.getElementById("book_fields");
        const furniture = document.getElementById("furniture_fields");

        dvd.style.display = "none";
        book.style.display = "none";
        furniture.style.display = "none";

        document.getElementById("size").required = false;
        document.getElementById("weight").required = false;
        document.getElementById("height").required = false;
        document.getElementById("width").required = false;
        document.getElementById("length").required = false;

        if (type === "dvd") {
            dvd.style.display = "block";
            document.getElementById("size").required = true;
        } else if (type === "book") {
            book.style.display = "block";
            document.getElementById("weight").required = true;
        } else if (type === "furniture") {
            furniture.style.display = "block";
            document.getElementById("height").required = true;
            document.getElementById("width").required = true;
            document.getElementById("length").required = true;
        }
    }    
    </script>
</head>
<body>
    <h1>Add a New Product</h1>
    <div class=buttons>
        <input type="submit" value="Save">
        <input type="button" value="Cancel" onclick="window.location.href='view_products.php'">
    </div>
    <form id="productForm" method="POST">

        <label for="sku">Product SKU:</label>
        <input type="text" name="sku" id="sku" required><br><br>

        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" required><br><br>

        <label for="price">Product Price($):</label>
        <input type="number" name="price" id="price" required step="0.01"><br><br>
        
        <label for="product_type">Product Type:</label>
        <select name="product_type" id="product_type" onchange="updateAttributeField()" required>
            <option value="">Select Product Type</option>
            <option value="dvd">DVD</option>
            <option value="book">Book</option>
            <option value="furniture">Furniture</option>
</select><br><br>
        <div id="dvd_fields" style="display:none;">
            <label for="size">Size (MB):</label>
            <input type="number" name="size" id="size"
            oninvalid="this.setCustomValidity('Please, provide size (MB)')"
            oninput="this.setCustomValidity('')"><br><br>
        </div>

        <div id="book_fields" style="display:none;">
            <label for="weight">Weight (KG):</label>
            <input type="number" name="weight" id="weight"
            oninvalid="this.setCustomValidity('Please, provide weight')"
            oninput="this.setCustomValidity('')"><br><br>
        </div>
        
        <div id="furniture_fields" style="display:none;">
            <label for="height">Height (cm):</label>
            <input type="number" name="height" id="height" required
            oninvalid="this.setCustomValidity('Please, provide size')"
            oninput="this.setCustomValidity('')"><br><br>

            <label for="width">Width (cm):</label>
            <input type="number" name="width" id="width" required
            oninvalid="this.setCustomValidity('Please, provide size')"
            oninput="this.setCustomValidity('')"><br><br>

            <label for="length">Length (cm):</label>
            <input type="number" name="length" id="length" required
            oninvalid="this.setCustomValidity('Please, provide size')"
            oninput="this.setCustomValidity('')"><br><br>
        </div>
        <p id="message"></p>
    </form>
</body>
</html>
<script>
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('add_product.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
        } else if (data.error) {
            const msgBox = document.getElementById('message');
            msgBox.style.color = 'red';
            msgBox.textContent = data.error;
        }
    })
    .catch(err => console.error('Error:', err)); 
});
</script>