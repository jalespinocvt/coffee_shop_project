<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "coffee_shop"
);

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

?>