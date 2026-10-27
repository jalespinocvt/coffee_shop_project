<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../config/database.php';

include("../config/database.php");

$message = "";

if(isset($_POST['delete']))
{
    $id = $_POST['customer_id'];

    $sql = "DELETE FROM customers WHERE customer_id='$id'";

    if(mysqli_query($conn, $sql))
    {
        $message = "Customer Deleted Successfully!";
    }
    else
    {
        $message = "Error deleting customer.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <title>Delete Customer</title>
</head>
<body>

<h2>Delete Customer</h2>

<form method="POST">

    <label>Customer ID:</label>
    <br>
    <input type="number" name="customer_id" required>

    <br><br>

    <button type="submit" name="delete">
        Delete Customer
    </button>

</form>

<br>

<?php echo $message; ?>

</body>
</html>
