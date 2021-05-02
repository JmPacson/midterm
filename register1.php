<?php
// Include config file
include_once "config.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";
$time = date("H:i:s");

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate username
  if (empty(trim($_POST['uname']))) {
    $username_err = "Please enter a username.";
  } else {
    // Prepare a select statement
    $sql = "SELECT username FROM users WHERE username = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $param_username);

      // Set parameters
      $param_username = trim($_POST['uname']);

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        /* store result */
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
          $username_err = "This username is already taken.";
        } else {
          $username = trim($_POST['uname']);
        }
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    }
  }

  // Validate password
  $password = $_POST['psw'];
  $uppercase = preg_match('@[A-Z]@', $password);
  $lowercase = preg_match('@[a-z]@', $password);
  $number    = preg_match('@[0-9]@', $password);
  $specialChars = preg_match('@[^\w]@', $password);
  if (empty($password)) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST['psw'])) < 8) {
    $password_err = "Password must have atleast 8 characters.";
  } elseif (!$uppercase) {
    $password_err = "Password should contain 1 upper case.";
  } elseif (!$lowercase) {
    $password_err = "Password should contain 1 lower case.";
  } elseif (!$number) {
    $password_err = "Password should contain 1 number.";
  } elseif (!$specialChars) {
    $password_err = "Password should contain 1 special character.";
  } else {
    $password = trim($_POST['psw']);
  }

  // Validate confirm password
  if (empty(trim($_POST['psw-repeat']))) {
    $confirm_password_err = "Please enter confirm password.";
  } else {
    $confirm_password = trim($_POST['psw-repeat']);
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = "Password did not match.";
    }
  }

  //Validate email
  if (empty(trim($_POST['email']))) {
    $email_err = "Please enter email.";
  } else {
    $email = trim($_POST['email']);
  }

  // Check input errors before inserting in database
  if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

    // Prepare an insert statement
    $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
    $sql1 = "INSERT INTO activity_log (activity, username) VALUES ('Created an Account', $username)";
    if ($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $email);

      // Set parameters
      $param_username = $username;
      $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
      $email = $_REQUEST['email'];

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {

       // Redirect to login page
        header("location: login1.php");

        // prepare and bind
        $stmt1 = $link->prepare("INSERT INTO activity_log (activity, username) VALUES (?, ?)");
        $stmt1->bind_param("ss", $activity, $username);

        // set parameters and execute
        $activity = "Created an Account";
        $username = $username;
        
        $stmt1->execute();

        $stmt1->close();
      } 
      else {
        echo "Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    }
  }

  // Close connection
  mysqli_close($link);
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      background-color: #c7e3f2;
    }

    * {
      box-sizing: border-box;
    }


    .container {
      padding: 16px;
      background-color: white;
    }

    .pswcontainer {
      padding: 16px;
      background-color: #f7eb7e;
    }

    input[type=text],
    input[type=password],
    input[type=email] {
      width: 100%;
      padding: 15px;
      margin: 5px 0 22px 0;
      display: inline-block;
      border: none;
      background: #f1f1f1;
    }

    input[type=text]:focus,
    input[type=password]:focus,
    input[type=email]:focus {
      background-color: #ddd;
      outline: none;
    }


    hr {
      border: 1px solid #f1f1f1;
      margin-bottom: 25px;
    }


    .registerbtn {
      background-color: #2940d6;
      color: white;
      padding: 16px 20px;
      margin: 8px 0;
      border: none;
      cursor: pointer;
      width: 100%;
      opacity: 0.9;
    }

    .registerbtn:hover {
      opacity: 1;
    }


    a {
      color: dodgerblue;
    }


    .signin {
      background-color: #f1f1f1;
      text-align: center;
    }
  </style>
</head>

<body>

  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="container">
      <h1>Register</h1>
      <p>Create an account.</p>
      <hr>
      <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
        <label for="uname"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="uname" id="uname" class="form-control" value="<?php echo $username; ?>">
        <span class="help-block">
          <?php echo $username_err; ?>
        </span>
      </div>

      <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
        <label for="email"><b>Email</b></label>
        <input type="email" placeholder="Enter Email" name="email" id="email">
        <span class="help-block"><?php echo $email_err; ?></span>
      </div>

      <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" id="psw" class="form-control" value="<?php echo $password; ?>">
        <span class="help-block"><?php echo $password_err; ?></span>
      </div>

      <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
        <label for="psw-repeat"><b>Repeat Password</b></label>
        <input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat" class="form-control" value="<?php echo $confirm_password; ?>">
        <span class="help-block"><?php echo $confirm_password_err; ?>

        </span>
      </div>
      <button type="submit" name="submit" class="registerbtn">Register</button>
    </div>
    <div class="container signin">
      <p>Already have an account? <a href="login1.php">Sign in</a>.</p>
    </div>
  </form>

</body>

</html>