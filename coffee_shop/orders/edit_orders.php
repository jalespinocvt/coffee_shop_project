<?php

include("../config/database.php");

$message = "";

if(isset($_POST['update']))
{
    $id = $_POST['order_id'];
    $product = $_POST['product_name'];

    $sql = "UPDATE orders
            SET product_name='$product'
            WHERE order_id='$id'";

    if(mysqli_query($conn, $sql))
    {
        $message = "Order Updated Successfully!";
    }
    else
    {
        $message = "Error Updating Order!";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Order</title>
</head>
<body>

<h2>Update Order</h2>

<form method="POST">

    Order ID:
    <br>
    <input type="number" name="order_id" required>

    <br><br>

    New Product Name:
    <br>
    <input type="text" name="product_name" required>

    <br><br>

    <button type="submit" name="update">
        Update Order
    </button>

</form>

<br>

<?php echo $message; ?>

</body>
</html>
