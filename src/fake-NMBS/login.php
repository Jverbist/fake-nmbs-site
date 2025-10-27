<?php
// login.php Ñ intentionally minimal handler so we can test SQLi on myaccount.php
// IMPORTANT: Do not echo anything before we send headers.

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Keep the raw value so payloads like `' OR 1=1 -- ` pass through untouched.
    $_SESSION['userName'] = $_POST['userName'] ?? '';
    // (Password is ignored on purpose for the exercise.)
    header('Location: myaccount.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Fake NMBS Ð Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font:16px/1.4 system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:40px}
    .card{max-width:480px;margin:0 auto;border:1px solid #eee;border-radius:12px;padding:24px}
    .row{margin-bottom:12px}
    label{display:block;margin-bottom:6px;color:#444}
    input{width:100%;padding:10px;border:1px solid #ccc;border-radius:8px}
    button{padding:10px 16px;border:0;border-radius:8px;cursor:pointer}
  </style>
</head>
<body>
  <div class="card">
    <h1>My NMBS Ð Login</h1>
    <form method="post" action="login.php">
      <div class="row">
        <label for="userName">E-mail / Username</label>
        <input id="userName" name="userName" type="text" autofocus>
      </div>
      <div class="row">
        <label for="password">Password (ignored)</label>
        <input id="password" name="password" type="password">
      </div>
      <button type="submit">Sign in</button>
    </form>
    <p style="color:#666;margin-top:12px">
      Tip: try <code>'</code>, then <code>' OR 1=1 -- </code>, then the UNION payloads.
    </p>
  </div>
</body>
</html>

