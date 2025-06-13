<?php
session_start();
$USER='admin';
$PASS='password';
if(isset($_SESSION['user'])){header('Location: menu.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
    if($_POST['username']===$USER && $_POST['password']===$PASS){
        $_SESSION['user']=$USER;
        header('Location: menu.php');exit;
    } else {
        $error='Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Вход</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<main style="margin-left:0">
<h2>Вход в админку</h2>
<?php if(!empty($error)) echo "<p>$error</p>"; ?>
<form method="post">
<input type="text" name="username" placeholder="Логин"><br><br>
<input type="password" name="password" placeholder="Пароль"><br><br>
<button type="submit">Войти</button>
</form>
</main>
</body>
</html>
