<?php
  session_start();
  require('utils.php');

  $onlysql = new mysqli("localhost", "root", "", "only");
  $onlysql->query("SET NAMES 'utf8'");

  $errors = array();

  if (isset($_SESSION['email']) || isset($_SESSION['tel'])) {
    if (!isset($_SESSION['userlogin']) || !isset($_SESSION['email']) || !isset($_SESSION['tel'])) {
      $searchfield = isset($_SESSION['email']) ? 'email' : 'tel';
      $searchvalue = $_SESSION[$searchfield];

      $fulldata = $onlysql->query("SELECT `userlogin`, `tel`, `email` FROM `only-users` WHERE `$searchfield` = '$searchvalue'");
      
      if ($user = $fulldata->fetch_assoc()) {
        $_SESSION['userlogin'] = $_SESSION['userlogin'] ?? $user['userlogin'];
        $_SESSION['tel'] = $_SESSION['tel'] ?? $user['tel'];
        $_SESSION['email'] = $_SESSION['email'] ?? $user['email'];
      }
    }
  } else {
    header('Location: index.php');
    exit;
  }

  $errors = array();

  if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $userlogin = trim($_POST['userlogin']);
    $tel = normalizeTel($_POST['tel']);
    $email = trim($_POST['email']);
    $password = trim($_POST['new_password']);

    if($_SESSION['userlogin'] != $userlogin) {
      $result = $onlysql->query("SELECT `userlogin` FROM `only-users` WHERE `userlogin` = '$userlogin'");
      if ($result->num_rows > 0) {
          $errors['userlogin'] = 'Пользователь с таким логином уже существует';
      }
    }

    if($_SESSION['tel'] != $tel) {
      $result = $onlysql->query("SELECT `tel` FROM `only-users` WHERE `tel` = '$tel'");
      if ($result->num_rows > 0) {
        $errors['tel'] = 'Пользователь с таким номером телефона уже существует';
      } else if (strlen($tel) != 11) {
        $errors['tel'] = 'Введите корректный номер телефона';
      }
    }

    if($_SESSION['email'] != $email) {
      $result = $onlysql->query("SELECT `email` FROM `only-users` WHERE `email` = '$email'");
      if ($result->num_rows > 0) {
        $errors['email'] = 'Пользователь с таким email уже существует';
      }
    }

    if (empty($errors)) {
      $onlysql->query("UPDATE `only-users` SET `userlogin`='$userlogin',`tel`='$tel',`email`='$email' WHERE `email` = '{$_SESSION['email']}'");
      $_SESSION['userlogin'] = $userlogin;
      $_SESSION['tel'] = $tel;
      $_SESSION['email'] = $email;

      if ($password != '') {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $onlysql->query("UPDATE `only-users` SET `password`='$password' WHERE `email` = '{$_SESSION['email']}'");
      }

      header('Location: profile.php');
      exit;
    }
  }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Личный кабинет</title>
</head>
<body>
  <div class="container">
    <h1 class="h1">Тестовое задание Онли</h1>
    <main class="main">
      <h2 class="h2">Личный кабинет</h2>
      
      <div class="form-container">
        
        <div class="profile-info">
          <p><strong>Логин:</strong> <?=$_SESSION['userlogin']?></p>
          <p><strong>Телефон:</strong> <?=$_SESSION['tel']?></p>
          <p><strong>Email:</strong> <?=$_SESSION['email']?></p>
        </div>

        <h2 class="h2">Изменить</h2>
        
        <form method="POST">
          <div class="form-group">
            <label for="userlogin">Логин</label>
            <input type="text" id="userlogin" name="userlogin" value="<?=$_SESSION['userlogin']?>" required>
          </div>
          
          <div class="form-group">
            <label for="tel">Телефон</label>
            <input type="tel" id="tel" name="tel" value="<?=$_SESSION['tel']?>" required>
          </div>
          
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?=$_SESSION['email']?>" required>
          </div>
          
          <div class="form-group">
            <label for="new_password">Новый пароль</label>
            <input type="password" id="new_password" name="new_password" placeholder="Оставьте пустым, если не меняется">
          </div>
          
          <div class="button-container">
            <button type="submit"><span>Сохранить изменения</span></button>
          </div>
        </form>
        
        <a href="logout.php" class="back-link">Выход</a>
      </div>
    </main>
  </div>
</body>
</html>