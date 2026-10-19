<?php

include("../config/database.php");

if(isset($_POST['update']))
{
    $id = $_POST['customer_id'];
    $name = $_POST['customer_name'];
    $contact = $_POST['contact_number'];

    mysqli_query(
        $conn,
        "UPDATE customers
         SET customer_name='$name',
             contact_number='$contact'
         WHERE customer_id=$id"
    );

    echo "Customer Updated Successfully!";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Customer</title>
</head>
<body>

<h2>Update Customer</h2>

<form method="POST">

    Customer ID:
    <br>
    <input type="number"
           name="customer_id"
           required>

    <br><br>

    New Name:
    <br>
    <input type="text"
           name="customer_name"
           required>

    <br><br>

    New Contact:
    <br>
    <input type="text"
           name="contact_number"
           required>

    <br><br>

    <button type="submit"
            name="update">
        Update
    </button>

</form>

</body>
</html>