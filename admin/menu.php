<?php
session_start();
if(!isset($_SESSION['user'])){header('Location: login.php');exit;}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Админка</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav>
<a href="blocks.php">Блоки</a>
<a href="templates.php">Шаблоны</a>
<a href="pages.php">Страницы</a>
<a href="help.php">Инструкции</a>
<a href="logout.php">Выход</a>
</nav>
<main>
<h1>Админ-панель</h1>
<p>Выберите раздел в меню слева.</p>
</main>
</body>
</html>
