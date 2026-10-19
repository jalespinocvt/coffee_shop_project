<?php

include("../config/database.php");

if(isset($_POST['save']))
{
    $name = $_POST['customer_name'];
    $contact = $_POST['contact_number'];

    $sql = "INSERT INTO customers
            (customer_name, contact_number)
            VALUES
            ('$name','$contact')";

    mysqli_query($conn, $sql);

    echo "Customer Added Successfully!";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Customer</title>
</head>
<body>

<h2>Add Customer</h2>

<form method="POST">

    Customer Name:
    <br>
    <input type="text" name="customer_name" required>

    <br><br>

    Contact Number:
    <br>
    <input type="text" name="contact_number" required>

    <br><br>

    <button type="submit" name="save">
        Save Customer
    </button>

</form>

</body>
</html>