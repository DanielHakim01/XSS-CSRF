# XSS-CSRF

## login.php
1. Here existing CSRF token will be validate
2. Any request will need the CSRF token
3. When request is made the token will be validate
```
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    validateCSRFToken($_POST['csrf_token']);
```
5. This function will be called to validate the token
```
Validate CSRF token
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }
}
```

## login.html
1. From this login page a CSRF token will be generated
```
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
```
2. Then it will be set for a same value through all forms that request
```
  <script>
    // Set CSRF token value for all forms
    var csrfToken = "<?php echo $_SESSION['csrf_token']; ?>";
    var forms = document.getElementsByTagName("form");
    for (var i = 0; i < forms.length; i++) {
      var form = forms[i];
      var csrfInput = document.createElement("input");
      csrfInput.type = "hidden";
      csrfInput.name = "csrf_token";
      csrfInput.value = csrfToken;
      form.appendChild(csrfInput);
    }
  </script>
```

## csrf_token_validation.php
1. When user wants to add new student through the studentform this PHP will be called for validation
```
<?php
session_start();

// Validate CSRF token
function validateCSRFToken($token) {
   if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
     http_response_code(403);
     exit('Invalid CSRF token');
   }
 }

// Check if CSRF token is already set
 if (!isset($_SESSION['csrf_token'])) {
   // Generate a new CSRF token
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
 }

 $csrf_token = $_SESSION['csrf_token'];
?>
```
## studentform.html
1. Here the same token will be used, if token is not found or different then it will shows error page

## action.php
1. When user make request through studentform.html
2. Thw function from csrf_token_validation.php will be called for validation
```
require_once 'csrf_token_validation.php';
validateCSRFToken($_POST['csrf_token']);
```
3. If token is not true then message "Invalid CSRF token" will be displayed
4. To protect against XSS every input will be validate
```
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
```
5. Regex is also used to filter any unneeded symbol
```
$nameReg = '/^(?!.*[<>])[a-zA-Z\s]+$/';
  $matricReg = '/^(?!.*[<>])[a-zA-Z0-9]+$/';
  $currentReg = '/^(?!.*[<>])[a-zA-Z0-9\s\.,]+$/';
  $homeReg = '/^(?!.*[<>])[a-zA-Z0-9\s\.,]+$/';
  $emailReg = '/^(?!.*[<>])[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
  $phoneReg = '/^(?!.*[<>])\+?\d{8,}$/';
  $homePhoneReg = '/^(?!.*[<>])\+?\d{8,}$/';

```
6. Other than that htmlspecialchar() is used in some of the code to validate input from user
```
  $name = htmlspecialchars($_POST['name']);
  $matric_no = mysqli_real_escape_string($conn, $_POST['matricNum']);
  $current_address = htmlspecialchars($_POST['currAddress']);
  $home_address = htmlspecialchars($_POST['homeAddress']);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
  $mobile_phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
  $home_phone = filter_var($_POST['homePhone'], FILTER_SANITIZE_STRING);

```
