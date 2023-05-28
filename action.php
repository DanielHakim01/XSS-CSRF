<?php
session_start();

$database_host = 'localhost';
$database_user = 'root';
$database_password = '';
$database_name = 'studentdatabase';

// Connect to the database
$conn = mysqli_connect($database_host, $database_user, $database_password, $database_name);

// require_once 'csrf_token_validation.php';
// validateCSRFToken($_POST['csrf_token']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the form data
  $name = htmlspecialchars($_POST['name']);
  $matric_no = mysqli_real_escape_string($conn, $_POST['matricNum']);
  $current_address = htmlspecialchars($_POST['currAddress']);
  $home_address = htmlspecialchars($_POST['homeAddress']);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
  $mobile_phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
  $home_phone = filter_var($_POST['homePhone'], FILTER_SANITIZE_STRING);

  // Validate the form data using regular expressions
  $nameReg = '/^(?!.*[<>])[a-zA-Z\s]+$/';
  $matricReg = '/^(?!.*[<>])[a-zA-Z0-9]+$/';
  $currentReg = '/^(?!.*[<>])[a-zA-Z0-9\s\.,]+$/';
  $homeReg = '/^(?!.*[<>])[a-zA-Z0-9\s\.,]+$/';
  $emailReg = '/^(?!.*[<>])[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
  $phoneReg = '/^(?!.*[<>])\+?\d{8,}$/';
  $homePhoneReg = '/^(?!.*[<>])\+?\d{8,}$/';

  // Validate the form data
  if (!preg_match($nameReg, $name)) {
    $errors['name'] = 'Please enter a valid name';
  }

  if (!preg_match($matricReg, $matric_no)) {
    $errors['matricNum'] = 'Please enter a valid matric number';
  }

  if (!preg_match($currentReg, $current_address)) {
    $errors['currAddress'] = 'Please enter a valid address';
  }

  if (!preg_match($homeReg, $home_address)) {
    $errors['homeAddress'] = 'Please enter a valid home address';
  }

  if (!preg_match($emailReg, $email)) {
    $errors['email'] = 'Please enter a valid email address';
  }

  if (!preg_match($phoneReg, $mobile_phone)) {
    $errors['phone'] = 'Please enter a valid phone number';
  }

  if (!preg_match($homePhoneReg, $home_phone)) {
    $errors['homePhone'] = 'Please enter a valid home number';
  }

  // Process the form data if there are no validation errors
  if (empty($errors)) {
    // Insert the form data into the database
    $sql = "INSERT INTO `studentform` (`name`, `matricNum`, `currAddress`, `homeAddress`, `email`, `phone`, `homePhone`) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssis', $name, $matric_no, $current_address, $home_address, $email, $mobile_phone, $home_phone);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
      echo 'Data inserted successfully.';
    } else {
      echo 'Error inserting data.';
    }

    // Set response as success
    $response = ['success' => true];
  } else {
    // Set response with errors
    $response = ['success' => false, 'errors' => $errors];
  }

  // Return the response as JSON
  header('Content-Type: application/json');
  echo json_encode($response);
}
?>
