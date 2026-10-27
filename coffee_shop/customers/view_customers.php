<?php

include("../config/database.php");

$result = mysqli_query(
    $conn,
    "SELECT * FROM orders"
);

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <title>View Orders</title>
</head>
<body>

<h2>Order Records</h2>

<table border="1" cellpadding="10">

<tr>
    <th>Order ID</th>
    <th>Product Name</th>
    <th>Order Date</th>
    <th>Customer ID</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>
    <td><?php echo $row['order_id']; ?></td>
    <td><?php echo $row['product_name']; ?></td>
    <td><?php echo $row['order_date']; ?></td>
    <td><?php echo $row['customer_id']; ?></td>
</tr>

<?php } ?>

</table>

</body>
</html>
