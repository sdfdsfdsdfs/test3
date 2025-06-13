<?php
require 'common.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Инструкции</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav>
<a href="menu.php">Назад</a>
<a href="blocks.php">Блоки</a>
<a href="templates.php">Шаблоны</a>
<a href="pages.php">Страницы</a>
<a href="help.php">Инструкции</a>
<a href="logout.php">Выход</a>
</nav>
<main>
<h2>Краткая инструкция</h2>
<ul>
<li>В разделе "Блоки" создавайте и редактируйте части страницы. Используйте снипеты <code>{{block:имя}}</code> в шаблонах и страницах.</li>
<li>В разделе "Шаблоны" хранится разметка сайта. Главный шаблон <code>index.html</code> нельзя удалить.</li>
<li>Раздел "Страницы" предназначен для наполнения сайта контентом.</li>
<li>Каждое сохранение создаёт версию в истории. Количество хранимых версий задаётся в history/settings.json.</li>
<li>Отмеченные звёздочкой версии не удаляются автоматически.</li>
</ul>
</main>
</body>
</html>
