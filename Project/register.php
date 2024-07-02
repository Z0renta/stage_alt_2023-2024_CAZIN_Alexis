<?php
session_start(); 

include_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['signup-email'];
    $password = $_POST['signup-password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 
    $username = $_POST['signup-username']; 

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? OR username = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $email, $username); 
        $stmt->execute(); 
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "Cet email ou ce nom d'utilisateur est déjà utilisé. Veuillez en choisir un autre.";
        } else {
            $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $username, $email, $hashedPassword);
                if ($stmt->execute()) {
                    $_SESSION['inscription_reussie'] = true;
                    header("Location: login.php");
                    exit();
                } else {
                    echo "Erreur lors de l'inscription: " . $stmt->error;
                }
            } else {
                echo "Erreur lors de la préparation de la requête : " . $conn->error;
            }
        }
        
        $stmt->close();
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error;
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login/Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <link rel="stylesheet" href="Front/css/style-Login_Register.css">
</head>
<body>
<section class="forms-section">
  <div class="forms">
    <div class="form-wrapper">
      <button type="button" class="switcher switcher-login" onclick="redirectToLogin()">
        Login
        <span class="underline"></span>
      </button>
      <form class="form form-login" action="login.php" method="post">
        <fieldset>
          <legend>Please, enter your email and password for login.</legend>
          <div class="input-block">
            <label>Email</label>
            <input id="login-email" type="email" required>
          </div>
          <div class="input-block">
            <label>Password</label>
            <input id="login-password" type="password" required>
          </div>
        </fieldset>
        <button type="submit" class="btn">
          <span>Login</span>
        </button>
      </form>
    </div>
    <div class="form-wrapper is-active">
      <button type="button" class="switcher switcher-signup" onclick="redirectToRegister()">
        Sign Up
        <span class="underline"></span>
      </button>
      <form class="form form-signup" action="register.php" method="post">
        <fieldset>
          <legend>Please, enter your email, password and password confirmation for sign up.</legend>
          <div class="input-block">
            <label>Email</label>
            <input name="signup-email" id="signup-email" type="email" required>
          </div>
          <div class="input-block">
            <label>Password</label>
            <input name="signup-password" id="signup-password" type="password" required>
          </div>
          <div class="input-block">
            <label for="signup-password-confirm">Confirm password</label>
            <input name="signup-password-confirm" id="signup-password-confirm" type="password" required>
          </div>
          <div class="input-block">
            <label>Username</label> 
            <input name="signup-username" id="signup-username" type="text" required>
          </div>
        </fieldset>
        <button type="submit" class="btn">
          <span>Continue</span>
        </button>
      </form>
    </div>
  </div>
</section>

<script>
  function redirectToLogin() {
    window.location.href = "login.php";
  }

  function redirectToRegister() {
    window.location.href = "register.php";
  }
</script>

</body>
</html>