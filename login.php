<?php
session_start();
// Connection establishment
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = 'lokiee'; // Update with your password
$DATABASE_NAME = 'car_service'; // Update with your database name

// Create connection
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME,3307);

// Check connection
if (mysqli_connect_errno()) {
    // If connection error
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check if form data is submitted
if (!isset($_POST['username'], $_POST['password'])) {
    exit('Please fill both the username and password fields!');
}

// Prepare the query to fetch user details
if ($stmt = $con->prepare('SELECT id, fullname, username, email, phone, password FROM info WHERE username = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc)
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    // Check if username exists in the database
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $fullname, $username, $email, $phone, $password);
        $stmt->fetch();

        // Verify the password
        if ($password == $_POST['password']) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['user_id'] = $id;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;

            echo "<script type='text/javascript'> alert('Welcome $_SESSION[fullname]!') </script>";
            echo "<script type='text/javascript'> window.location='home_page.htm' </script>"; // Redirect to a dashboard page or another page of your choice
        } else {
            echo 'Incorrect username and/or password!';
        }
    } else {
        // Incorrect username
        echo 'Incorrect username and/or password!';
    }

    $stmt->close();
}
