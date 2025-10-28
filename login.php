<?php
  session_start();
  require('utils.php');
  $onlysql = new mysqli("localhost", "root", "", "only");
  $onlysql->query("SET NAMES 'utf8'");

  $errors = array();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enterlogin = trim($_POST['enterlogin']);
    $password = trim($_POST['password']);

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
        header('Location: profile.php');
        exit;
        } else {
        $errors['password'] = 'Неправильный пароль';
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
  <title>Вход</title>
</head>
<body>
  <div class="container">
    <h1 class="h1">Тестовое задание Онли</h1>
    <main class="main">
      <h2 class="h2">Вход в систему</h2>
      
      <div class="form-container">
        
        <form method="POST">
          <div class="form-group">
            <label for="enterlogin">Email или телефон</label>
            <input type="text" id="enterlogin" name="enterlogin" class="<?=array_key_exists('enterlogin', $errors) ? 'input-error' : ''?>" required value="<?=$enterlogin?>">
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