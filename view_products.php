<!DOCTYPE html>
<html lang="en">
<head link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>View Products</title>
    <style>
        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            color: #333;
        }

        .product-card p {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #555;
        }
        .product-grid {
            display: grid;
            justify-content: center;
            align-items: center;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 50px;
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .product-card {
            border: 1px solid #ccc;
            padding: 10px;
            text-align center;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }
        .buttons {
            display: flex;
            gap: 10px;
        }
        input[type="submit"],
        input[type="button"],
        button {
            font-family: 'Poppins', sans-serif;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover,
        input[type="button"]:hover,
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<form action="delete_products.php" method="POST">
    <div class="header-bar">
        <h1>Product List</h1>
        <div class="buttons">
            <button type="button" onclick="window.location.href='add_product.php'">Add Product</button>
            <input type="submit" value="Mass Delete">
        </div>
    </div>

    <div class="product-grid">
        <?php
        $host = 'localhost';
        $db = 'product_db';
        $user = 'root';
        $pass = '';
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

        $stmt = $pdo->query("SELECT * FROM products");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($products) {
            foreach ($products as $product) {
                echo '<div class="product-card">';
                echo '<input type="checkbox" name="selected[]" value="' . htmlspecialchars($product['sku']) . '"><br>';
                echo "<p><strong></strong> " . htmlspecialchars($product['sku']) . "</p>";
                echo "<p><strong></strong> " . htmlspecialchars($product['name']) . "</p>";
                echo "<p><strong></strong> " . htmlspecialchars($product['price']) . " $" . "</p>";

                switch ($product['attribute_type']) {
                    case 'size':
                        echo "<p><strong>Size:</strong> " . htmlspecialchars($product['attribute_value']) . " MB</p>";
                        break;
                    case 'dimensions':
                        echo "<p><strong>Dimensions:</strong> " . htmlspecialchars($product['attribute_value']) . "</p>";
                        break;
                    case 'weight':
                        echo "<p><strong>Weight:</strong> " . htmlspecialchars($product['attribute_value']) . " KG</p>";
                        break;
                }

                echo "</div>";
            }
        } else {
            echo "<p>No products available.</p>";
        }
        ?>
    </div><br>
</form>

</body>
</html>
