
<?php
  session_start();
  $onlysql = new mysqli("localhost", "root", "", "only");
  $onlysql->query("SET NAMES 'utf8'");

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = array();

    $userlogin = trim($_POST['userlogin']);
    $tel = trim($_POST['tel']);
    $email = trim($_POST['email']);
    $password1 = trim($_POST['password']);
    $password2 = trim($_POST['password_confirm']);

    $result = $onlysql->query("SELECT `userlogin` FROM `only-users` WHERE `userlogin` = '$userlogin'");
    if ($result->num_rows > 0) {
        $errors['userlogin'] = 'Пользователь с таким логином уже существует';
    }

    $result = $onlysql->query("SELECT `tel` FROM `only-users` WHERE `tel` = '$tel'");
    if ($result->num_rows > 0) {
      $errors['tel'] = 'Пользователь с таким номером телефона уже существует';
    }

    $result = $onlysql->query("SELECT `email` FROM `only-users` WHERE `email` = '$email'");
    if ($result->num_rows > 0) {
      $errors['email'] = 'Пользователь с таким email уже существует';
    }

    if ($password1 != $password2) {
      $errors['password'] = 'Пароли не совпадают';
    }

    if (empty($errors)) {
      $password = password_hash($password1, PASSWORD_DEFAULT);
      $onlysql->query("INSERT INTO `only-users`(`userlogin`, `tel`, `email`, `password`) VALUES ('$userlogin','$tel','$email','$password')");
      header('Location: profile.php');
    }
  }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Регистрация</title>
</head>
<body>
  <div class="container">
    <h1 class="h1">Тестовое задание Онли</h1>
    <main class="main">
      <h2 class="h2">Регистрация</h2>
      
      <div class="form-container">
        <form method="POST">
          <div class="form-group">
            <label for="userlogin">Логин</label>
            <input type="text" id="userlogin" name="userlogin" class="<?=array_key_exists('userlogin', $errors) ? 'input-error' : ''?>" required value="<?=$userlogin?>">
            <div class="message-error">
              <?=array_key_exists('userlogin', $errors) ? $errors['userlogin'] : ''?>
            </div>
          </div>
          
          <div class="form-group">
            <label for="tel">Телефон</label>
            <input type="tel" id="tel" name="tel" class="<?=array_key_exists('tel', $errors) ? 'input-error' : ''?>" required value="<?=$tel?>">
            <div class="message-error">
              <?=array_key_exists('tel', $errors) ? $errors['tel'] : ''?>
            </div>
          </div>
          
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="<?=array_key_exists('email', $errors) ? 'input-error' : ''?>" required value="<?=$email?>">
            <div class="message-error">
              <?=array_key_exists('email', $errors) ? $errors['email'] : ''?>
            </div>
          </div>
          
          <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" class="<?=array_key_exists('password', $errors) ? 'input-error' : ''?>" required value="<?=$password1?>">
            <div class="message-error">
            </div>
          </div>
          
          <div class="form-group">
            <label for="password_confirm">Повторите пароль</label>
            <input type="password" id="password_confirm" name="password_confirm" class="<?=array_key_exists('password', $errors) ? 'input-error' : ''?>" required value="<?=$password2?>">
            <div class="message-error">
              <?=array_key_exists('password', $errors) ? $errors['password'] : ''?>
            </div> 
          </div>
          
          <div class="button-container">
            <button type="submit"><span>Зарегистрироваться</span></button>
          </div>
        </form>
        
        <a href="index.php" class="back-link">← На главную</a>
      </div>
    </main>
  </div>
</body>
</html>