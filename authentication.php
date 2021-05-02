<?php
include_once 'config.php';
// Initialize the session
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
    // Check if the user is not logged in, if yes then redirect him to login page
    header('location: login1.php');
    exit;
} else if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    // Check if the user is authenticated, if yes then redirect him to home page
    header('location: welcome.php');
    exit;
}


// Define variables and initialize
$user_id = $_SESSION['id'];
$authentication_code = "";
$authentication_user = $authentication_err = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

// Prepare a select statement for code
$sql = "SELECT code FROM date_auth WHERE user_id = $user_id AND NOW() >= time_added AND NOW() <= expiration ORDER BY id DESC limit 1";
$result = $link->query($sql);



//result for Code
if ($result->num_rows >= 1) {
    if ($row = $result->fetch_assoc()) {
        $authentication_code = $row['code'];

    //Code check
    if(empty(trim($_POST['codes']))){
        $authentication_err = "Please enter Code";
    }
    else{
        $authentication_user = trim($_POST['codes']);
    }
    if(empty($authentication_err)){
        if($authentication_code === $row['code']){
            $_SESSION['authenticated'] = true;
            
            // Prepare a select statement for username
                        $stmt1 = $link->prepare("INSERT INTO activity_log (activity, username) VALUES (?, ?)");
                           $stmt1->bind_param("ss", $activity, $username);
                          // // set parameters and execute
                           $activity = "Success Log in";
                           $username = $_SESSION['username'];
                           $stmt1->execute();
                           $stmt1->close();
               header('location: welcome.php');
        }
        else{
            $authentication_err = "Incorrect Code!";
        }

    }
}


    else{
        echo "Something went wrong";
    }
}
}
$link->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Code Guard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
            text-align: center;
        }
    </style>
</head>

<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="page-header">
            <label><h1> <b>Please Enter <?php echo $authentication_code ?> </b>. To continue login</h1></label>
            <h3><b>Please click button to Show Code</b></h3>
            <input type="number" name="codes" placeholder="Enter Code" id="codes">
            <span class="help-block"><?php echo $authentication_err; ?></span>
            
                <p>
                    <button class="btn btn-primary" name="submit" id="submit" type="submit">Login</button>
                    <button class="btn btn-primary" name="submit" id="show_code" type="submit">Show Code</button>
                    <a href="login1.php" class="btn btn-danger">Logout</a>
                </p>
        </div>
    </form>
</body>

</html>