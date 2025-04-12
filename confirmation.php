<?php
// Start session to access stored appointment data
session_start();

// Check if appointment data exists in session
if (!isset($_SESSION['appointment'])) {
    header("Location: book_appointment.php");
    exit();
}

// Get the appointment data from session
$appointment = $_SESSION['appointment'];
$service_package = $appointment['service_package'] ?? 'Not specified';

// Ensure services is treated as an array
$services = isset($appointment['services'])
    ? (is_array($appointment['services']) ? $appointment['services'] : [$appointment['services']])
    : [];

unset($_SESSION['appointment']);  // Clear session data

// Database Connection
$servername = "localhost";
$username = "root";
$password = "lokiee";
$dbname = "car_service";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Initialize mail status
$mail_sent = false;

// Prices for services and packages
$package_prices = [
    'premium' => 3999,
    'basic' => 2499,
    'general' => 1499
];

$service_prices = [
    'Oil Change' => 500,
    'Puncture Repair' => 300,
    'Washing' => 400,
    'Battery Check' => 200
];

// Calculate the total cost
$total_cost = $package_prices[$service_package] ?? 0;


// Display services properly
$additional_services_list = !empty($services) ? implode(', ', $services) : 'None';

$services = !empty($additional_services_list) ? explode(', ', $additional_services_list) : [];

// Calculate the total cost
foreach ($services as $service) {
    if (isset($service_prices[$service])) {
        $total_cost += $service_prices[$service];
    }
}


// Send confirmation email
$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'vtu22202@veltech.edu.in';   // Replace with your email
    $mail->Password = 'focu ewnb fjgt szuc';       // Replace with your password or app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Email sender and recipient
    $mail->setFrom('vtu22202@veltech.edu.in', 'Car Service');
    $mail->addAddress($appointment['email']);
    $mail->addReplyTo('vtu22202@veltech.edu.in', 'Car Service Support');

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Car Service Appointment Confirmation';

    $mail->Body =
        "<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 10px; text-align: center; }
        .details { margin: 20px 0; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #6c757d; }
    </style>
</head>
<body>
<div class='container'>
    <div class='header'>
        <h2>Car Service Appointment Confirmation</h2>
    </div>
    
    <div class='details'>" .
        "<p><strong>Appointment Number:</strong> #" . $appointment['id'] . "</p>" .
        "<p><strong>Full Name:</strong> " . htmlspecialchars($appointment['fullname']) . "</p>" .
        "<p><strong>Phone:</strong> " . htmlspecialchars($appointment['phone']) . "</p>" .
        "<p><strong>Email:</strong> " . htmlspecialchars($appointment['email']) . "</p>" .
        "<p><strong>Vehicle Type:</strong> " . htmlspecialchars($appointment['vehicle_type']) . "</p>" .
        "<p><strong>Vehicle Model:</strong> " . htmlspecialchars($appointment['vehicle_model']) . "</p>" .
        "<p><strong>License Plate:</strong> " . htmlspecialchars($appointment['license_plate']) . "</p>" .
        "<p><strong>Appointment Date:</strong> " . htmlspecialchars($appointment['appointment_date']) . "</p>" .
        "<p><strong>Appointment Time:</strong> " . htmlspecialchars($appointment['appointment_time']) . "</p>" .
        "<p><strong>Service Address:</strong> " . htmlspecialchars($appointment['address']) . "</p>" .
        "<hr>" .
        "<h3>Services Requested</h3>" .
        "<p><strong>Service Package:</strong> " . htmlspecialchars($service_package) . " (₹" . number_format($package_prices[$service_package] ?? 0) . ")</p>" .
        "<p><strong>Additional Services:</strong> " . htmlspecialchars($additional_services_list) . "</p>" .
        "<hr>" .
        "<h3>Total Cost: ₹" . number_format($total_cost) . "</h3>" .
        "</div>" .

        "<div class='footer'>
        <p>Thank you for choosing our service. If you need to reschedule or cancel your appointment, please contact us at least 24 hours in advance.</p>
        <p>Contact us at: support@carservice.com or (123) 456-7890</p>
    </div>
</div>
</body>
</html>";


    $mail_sent = $mail->send();
} catch (Exception $e) {
    $mail_sent = false;
    error_log("Email sending failed: " . $mail->ErrorInfo);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .confirmation-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }

        .confirmation-details {
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            font-weight: bold;
            width: 200px;
            color: #34495e;
        }

        .detail-value {
            flex: 1;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .email-notification {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 10px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .cost-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .cost-summary h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
            }

            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="confirmation-container">
        <h1>Appointment Confirmation</h1>

        <?php if ($mail_sent): ?>
            <div class="success-message">
                <p>Your appointment has been successfully booked! A confirmation email has been sent to <?php echo htmlspecialchars($appointment['email']); ?>.</p>
            </div>
        <?php else: ?>
            <div class="email-notification">
                <p>Your appointment has been successfully booked! However, we couldn't send a confirmation email. Please note down your appointment details.</p>
            </div>
        <?php endif; ?>

        <div class="confirmation-details">
            <div class="detail-row">
                <div class="detail-label">Appointment Number:</div>
                <div class="detail-value">#<?php echo htmlspecialchars($appointment['id']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Full Name:</div>
                <div class="detail-value"><?php echo htmlspecialchars($appointment['fullname']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Phone:</div>
                <div class="detail-value"><?php echo htmlspecialchars($appointment['phone']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div class="detail-value"><?php echo htmlspecialchars($appointment['email']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Vehicle Type:</div>
                <div class="detail-value"><?php echo htmlspecialchars($appointment['vehicle_type']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Vehicle Model:</div>
                <div class="detail-value"><?php echo htmlspecialchars($appointment['vehicle_model']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">License Plate:</div>
                <div class="detail-value"><?php echo htmlspecialchars($appointment['license_plate']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Appointment Date:</div>
                <div class="detail-value"><?php echo htmlspecialchars($appointment['appointment_date']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Appointment Time:</div>
                <div class="detail-value"><?php echo htmlspecialchars($appointment['appointment_time']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Service Address:</div>
                <div class="detail-value"><?php echo htmlspecialchars($appointment['address']); ?></div>
            </div>
        </div>

        <div class="cost-summary">
            <h3>Service Summary</h3>
            <p><strong>Service Package:</strong> <?php echo htmlspecialchars($service_package); ?> (₹<?php echo number_format($package_prices[$service_package] ?? 0); ?>)</p>
            <p><strong>Additional Services:</strong> <?php echo htmlspecialchars($additional_services_list); ?></p>
            <?php if (!empty($additional_services)): ?>
                <ul>
                    <?php foreach ($additional_services as $service): ?>
                        <?php if (isset($service_prices[$service])): ?>
                            <li><?php echo htmlspecialchars($service); ?> (₹<?php echo number_format($service_prices[$service]); ?>)</li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <h3>Total Cost: ₹<?php echo number_format($total_cost); ?></h3>
        </div>

        <div class="actions">
            <a href="index.html" class="btn">Return Home</a>
            <a href="book.html" class="btn">Book Another Appointment</a>
        </div>
    </div>
</body>

</html>