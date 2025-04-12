<?php
session_start();
// Database Connection
$servername = "localhost";
$username = "root";
$password = "lokiee";
$dbname = "car_service";
$port = 3307;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
    $vehicle_type = htmlspecialchars(trim($_POST['vehicle_type']));
    $vehicle_model = htmlspecialchars(trim($_POST['vehicle_model']));
    $license_plate = htmlspecialchars(trim($_POST['license_plate']));
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $address = htmlspecialchars(trim($_POST['address']));
    $services = isset($_POST['services']) ? implode(", ", array_map('htmlspecialchars', $_POST['services'])) : "No service selected";
    $service_package = isset($_POST['service_package']) ? $_POST['service_package'] : 'None';

    // Retrieve additional checkboxes (array)
    $add_ons = isset($_POST['add_ons']) ? $_POST['add_ons'] : [];
    // Validate required fields
    if (empty($fullname) || empty($phone) || empty($vehicle_model) || empty($license_plate)) {
        die("<script>alert('All fields are required!'); window.history.back();</script>");
    }

    // Validate email
    if (!$email) {
        die("<script>alert('Invalid email address!'); window.history.back();</script>");
    }

    // Validate appointment date (not in past)
    $today = date("Y-m-d");
    if ($appointment_date < $today) {
        die("<script>alert('Appointment date cannot be in the past!'); window.history.back();</script>");
    }

    // Prepare and execute SQL query
    $query = "INSERT INTO appointments (
                fullname, 
                phone, 
                email, 
                vehicle_type, 
                vehicle_model, 
                license_plate, 
                services, 
                appointment_date, 
                appointment_time, 
                address
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("<script>alert('Database error! Please try again.'); window.history.back();</script>");
    }

    $stmt->bind_param(
        "ssssssssss",
        $fullname,
        $phone,
        $email,
        $vehicle_type,
        $vehicle_model,
        $license_plate,
        $services,
        $appointment_date,
        $appointment_time,
        $address
    );

    if ($stmt->execute()) {

        $_SESSION['appointment'] = [
            'id' => $conn->insert_id,
            'fullname' => $fullname,
            'phone' => $phone,
            'email' => $email,
            'vehicle_type' => $vehicle_type,
            'vehicle_model' => $vehicle_model,
            'license_plate' => $license_plate,
            'services' => $services,
            'appointment_date' => $appointment_date,
            'appointment_time' => $appointment_time,
            'address' => $address,
            'service_package' => $service_package
        ];

        echo "<script>
                alert('Appointment booked successfully!');
                window.location.href = 'confirmation.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . addslashes($stmt->error) . "');
                window.history.back();
              </script>";
    }
    $stmt->close();
    $conn->close();
} else {
    // If someone tries to access this page directly
    header("Location: book_app");
    exit();
}
