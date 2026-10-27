<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../config/database.php';

include("../config/database.php");

$result = mysqli_query(
    $conn,
    "SELECT * FROM customers"
);

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <title>View Customers</title>
</head>
<body>

<h2>Customer Records</h2>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Contact</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>
    <td><?php echo $row['customer_id']; ?></td>
    <td><?php echo $row['customer_name']; ?></td>
    <td><?php echo $row['contact_number']; ?></td>
</tr>

<?php } ?>

</table>

</body>
</html>
