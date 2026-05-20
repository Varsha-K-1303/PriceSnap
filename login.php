<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = htmlspecialchars($_POST['username']);
    $_SESSION['history'] = [];
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PriceSnap | Login</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<style>
body {
  background: linear-gradient(135deg, #0f0f0f, #1a1a1a);
  color: #f8f9fa;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: 'Segoe UI', sans-serif;
}
.login-box {
  background: #2b2b2b;
  padding: 40px;
  border-radius: 15px;
  width: 350px;
  box-shadow: 0 0 15px rgba(0,0,0,0.5);
}
h2 { text-align:center; color:#00bcd4; margin-bottom:25px; }
.btn-login {
  background-color:#00bcd4; border:none; border-radius:25px;
}
.btn-login:hover { background-color:#0097a7; }
</style>
</head>
<body>
  <div class="login-box">
    <h2>Welcome to PriceSnap!</h2>
    <form method="post">
      <div class="form-group">
        <label>Name</label>
        <input type="text" name="username" class="form-control" required placeholder="Enter your name">
      </div>
      <div class="form-group">
        <label>Password (any value)</label>
        <input type="password" name="password" class="form-control" required placeholder="Enter any password">
      </div>
      <button type="submit" class="btn btn-login btn-block">Enter</button>
    </form>
  </div>
</body>
</html>
