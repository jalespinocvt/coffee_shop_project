<?php

include("../config/database.php");

$message = "";

if(isset($_POST['save']))
{
    $product = $_POST['product_name'];
    $date = $_POST['order_date'];
    $customer = $_POST['customer_id'];

    $sql = "INSERT INTO orders
            (product_name, order_date, customer_id)
            VALUES
            ('$product','$date','$customer')";

    if(mysqli_query($conn, $sql))
    {
        $message = "Order Added Successfully!";
    }
    else
    {
        $message = "Error Adding Order!";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <title>Add Order</title>
</head>
<body>

<h2>Add Order</h2>

<form method="POST">

    Product Name:
    <br>
    <input type="text" name="product_name" required>

    <br><br>

    Order Date:
    <br>
    <input type="date" name="order_date" required>

    <br><br>

    Customer ID:
    <br>
    <input type="number" name="customer_id" required>

    <br><br>

    <button type="submit" name="save">
        Add Order
    </button>

</form>

<br>

<?php echo $message; ?>

</body>
</html>
