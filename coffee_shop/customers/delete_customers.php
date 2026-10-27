<?php

include("../config/database.php");

$message = "";

if(isset($_POST['delete']))
{
    $id = $_POST['order_id'];

    $sql = "DELETE FROM orders
            WHERE order_id='$id'";

    if(mysqli_query($conn, $sql))
    {
        $message = "Order Deleted Successfully!";
    }
    else
    {
        $message = "Error Deleting Order!";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <title>Delete Order</title>
</head>
<body>

<h2>Delete Order</h2>

<form method="POST">

    Order ID:
    <br>
    <input type="number" name="order_id" required>

    <br><br>

    <button type="submit" name="delete">
        Delete Order
    </button>

</form>

<br>

<?php echo $message; ?>

</body>
</html>
