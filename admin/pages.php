<?php
require 'common.php';
$dir='pages';
$name=$_GET['edit']??'';
$action=$_GET['action']??'';
if($action==='save'&&$name){
    save_file($dir,$name,$_POST['content']);
    header("Location: pages.php?edit=$name");exit;
}
if($action==='restore'&&$name&&isset($_GET['file'])){
    $file=basename($_GET['file']);
    $content=file_get_contents(__DIR__.'/../history/'.$dir.'/'.$file);
    save_file($dir,$name,$content);
    header("Location: pages.php?edit=$name");exit;
}
if($action==='favorite'&&isset($_GET['file'])){
    toggle_favorite($dir,basename($_GET['file']));
    header("Location: pages.php?edit=$name");exit;
}
$items=list_items($dir);
$templates=list_items('templates');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Страницы</title>
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
<h2>Управление страницами</h2>
<ul>
<?php foreach($items as $item): ?>
<li><a href="?edit=<?=$item?>"><?=$item?></a></li>
<?php endforeach; ?>
</ul>
<?php if($name): $file=__DIR__.'/../'.$dir."/$name.html"; $content=file_exists($file)?file_get_contents($file):''; ?>
<h3>Редактировать страницу "<?=$name?>"</h3>
<form method="post" action="?action=save&edit=<?=$name?>">
<textarea name="content" rows="10"><?=htmlspecialchars($content)?></textarea><br>
<button type="submit">Сохранить</button>
</form>
<p>Страницы можно использовать с разными шаблонами. Снипеты блоков: {{block:имя}}</p>
<h4>История</h4>
<ul>
<?php foreach(get_history_list($dir,$name) as $hist): $b=basename($hist); ?>
<li><?=$b?>
 <a href="?edit=<?=$name?>&action=restore&file=<?=$b?>">[Восстановить]</a>
 <a href="?edit=<?=$name?>&action=favorite&file=<?=$b?>">[⭐]</a>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</main>
</body>
</html>
