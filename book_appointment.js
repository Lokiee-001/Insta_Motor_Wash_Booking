$(document).ready(function() {
    // Extract Personal Details
    $("form").submit(function(event) {
        event.preventDefault(); // Prevent form submission for testing

        let fullName = $("#fullname").val();
        let phone = $("#phone").val();
        let email = $("#email").val();

        // Extract Vehicle Details
        let vehicleType = $("#vehicle_type").val();
        let vehicleModel = $("#vehicle_model").val();
        let licensePlate = $("#license_plate").val();

        // Extract Selected Services
        let selectedServices = [];
        $("input[name='services[]']:checked").each(function() {
            selectedServices.push($(this).val());
        });

        // Extract Appointment Details
        let appointmentDate = $("#appointment_date").val();
        let appointmentTime = $("#appointment_time").val();
        let address = $("#address").val();

        // Display extracted information in console
        console.log("Full Name:", fullName);
        console.log("Phone:", phone);
        console.log("Email:", email);
        console.log("Vehicle Type:", vehicleType);
        console.log("Vehicle Model:", vehicleModel);
        console.log("License Plate:", licensePlate);
        console.log("Selected Services:", selectedServices.join(", "));
        console.log("Appointment Date:", appointmentDate);
        console.log("Appointment Time:", appointmentTime);
        console.log("Address:", address);
    });
});
