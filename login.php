<?php
session_start();
require('utils.php');
$onlysql = new mysqli("localhost", "root", "", "only");
$onlysql->query("SET NAMES 'utf8'");

$errors = array();
define('SMARTCAPTCHA_SERVER_KEY', 'ysc2_MkIatfuHyLfJvxZwKYvTIwNH8jEEOsFQCJR3XziNefb25b0f');

function check_captcha($token) {
    $ch = curl_init("https://smartcaptcha.yandexcloud.net/validate");
    $args = [
        "secret" => SMARTCAPTCHA_SERVER_KEY,
        "token" => $token
    ];
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_POST, true);    
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch); 
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        return false;
    }

    $resp = json_decode($server_output);
    return $resp->status === "ok";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enterlogin = trim($_POST['enterlogin']);
    $password = trim($_POST['password']);

    $token = $_POST['smart-token'] ?? ''; 
    
    if (empty($token)) {
        $errors['captcha'] = 'Подтвердите, что вы не робот';
    } elseif (!check_captcha($token)) {
        $errors['captcha'] = 'Подтвердите, что вы не робот';
    }

    if (empty($errors)) {
        $logintype = checkEmailOrTel($enterlogin);

        if($logintype == 'tel') {
            $enterlogin = normalizeTel($enterlogin);
        }
        
        $result = $onlysql->query("SELECT `$logintype`, `password` FROM `only-users` WHERE `$logintype` = '$enterlogin'");
        
        if ($result->num_rows < 1) {
            $errors['enterlogin'] = 'Такого пользователя не существует';
        } else {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION[$logintype] = $enterlogin;
                header('Location: profile.php');
                exit;
            } else {
                $errors['password'] = 'Неправильный пароль';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
  <title>Вход</title>
</head>
<body>
  <div class="container">
    <h1 class="h1">Тестовое задание Онли</h1>
    <main class="main">
      <h2 class="h2">Вход в систему</h2>
      
      <div class="form-container">
        
        <form method="POST" id="auth-form">
          <div class="form-group">
            <label for="enterlogin">Email или телефон</label>
            <input type="text" id="enterlogin" name="enterlogin" class="<?=array_key_exists('enterlogin', $errors) ? 'input-error' : ''?>" required value="<?=($enterlogin ?? '')?>">
            <div class="message-error">
              <?=array_key_exists('enterlogin', $errors) ? $errors['enterlogin'] : ''?>
            </div>
          </div>
          
          <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" class="<?=array_key_exists('password', $errors) ? 'input-error' : ''?>" required>
            <div class="message-error">
              <?=array_key_exists('password', $errors) ? $errors['password'] : ''?>
            </div> 
          </div>

          <div id="captcha-container" class="smart-captcha" data-sitekey="ysc1_MkIatfuHyLfJvxZwKYvTAmW5uUtdVTXjixQXTGeTc01ecd0e"></div>
          <div class="message-error">
              <?=array_key_exists('captcha', $errors) ? $errors['captcha'] : ''?>
          </div> 
          
          <div class="button-container">
            <button type="submit"><span>Войти</span></button>
          </div>
        </form>
        
        <a href="index.php" class="back-link">← На главную</a>
      </div>
    </main>
  </div>
</body>
</html>