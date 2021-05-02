<?php
include_once 'config.php';

session_start();
//Logout log
$username = $_SESSION['username'];
if ($_SERVER["REQUEST_METHOD"] == "POST"){
  
  mysqli_query($link,"INSERT INTO activity_log (activity,username) VALUES('Logged out','$username')");
    

    
  header('location: login1.php');
}

?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
<style>
@import url('https://fonts.googleapis.com/css?family=Numans');
body {
  background-image: url('https://i.pinimg.com/originals/b8/2f/28/b82f28a7e9c8fcb3868d3d94652c107c.gif');
  background-size: cover;
  background-repeat: no-repeat;
  background-attachment: fixed;
  height: 100%;
  font-family: 'Numans', sans-serif;
  padding-top: 20px;
  padding-bottom: 20px;
}

.topleft {
  position: absolute;
  top: 0;
  left: 16px;
}

.bottomleft {
  position: absolute;
  bottom: 0;
  left: 16px;
}

.middle {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}

hr {
  margin: auto;
  width: 40%;
}
</style>
</head>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<body>
  <div class="topleft">
    <strong><p><b>Abandon all hope, ye who enter here.</b></p></strong>
  </div>
  <div class="middle">
    <strong><h1>WELCOME <?php echo $username ?></h1></strong>
    <hr>
    <p>Thanks for logging in</p>
   <button type="submit" name="submit" id="submit" class="btn btn-danger">Logout</button>
  </div>
</form>
</body>
</html>
