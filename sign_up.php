<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $alt_phone = $_POST['alt_phone'];
    $address = $_POST['address'];

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        echo "<script type='text/javascript'>alert('Passwords do not match!');</script>";
        echo "<script type='text/javascript'>window.location='signup.htm';</script>";
        exit();
    }
    $port = 3307;
    // Database connection
    $con = new mysqli("localhost", "root", "lokiee", "car_service", 3307);

    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // Insert data into the database
    $sql = "INSERT INTO info (fullname, age, dob, username, email, password, phone, alt_phone, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(
            'sisssssss',
            $fullname,
            $age,
            $dob,
            $username,
            $email,
            $password,
            $phone,
            $alt_phone,
            $address
        );

        if ($stmt->execute()) {
            echo "<script type='text/javascript'>alert('Successfully submitted');</script>";
            echo "<script type='text/javascript'>window.location='login.htm';</script>";
        } else {
            echo "<script type='text/javascript'>alert('Submission failed!');</script>";
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }

    $con->close();
}
