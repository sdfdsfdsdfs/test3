<?php
include 'menu.php';
$blocksDir = __DIR__.'/blocks';
$historyDir = __DIR__.'/history/blocks';
$settings = json_decode(file_get_contents(__DIR__.'/history/settings.json'), true);
$maxVersions = isset($settings['blocks']) ? (int)$settings['blocks'] : 10;
$name = $_GET['edit'] ?? '';
$action = $_GET['action'] ?? '';

function save_version($name, $content, $historyDir) {
    $dir = "$historyDir/$name";
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $file = $dir.'/'.time().'.html';
    file_put_contents($file, $content);
}

function prune_versions($name, $historyDir, $maxVersions) {
    $dir = "$historyDir/$name";
    if (!is_dir($dir)) return;
    $fav = [];
    $favFile = "$dir/favorites.json";
    if (file_exists($favFile)) $fav = json_decode(file_get_contents($favFile), true);
    $files = glob("$dir/*.html");
    usort($files, function($a,$b){ return filemtime($b)-filemtime($a); });
    $count = 0;
    foreach ($files as $f) {
        $base = basename($f);
        if (in_array($base, $fav)) continue;
        if ($count >= $maxVersions) unlink($f); else $count++;
    }
}

if ($action === 'save' && $name && isset($_POST['content'])) {
    $path = "$blocksDir/$name.html";
    $old = file_exists($path) ? file_get_contents($path) : '';
    if ($old) save_version($name, $old, $historyDir);
    file_put_contents($path, $_POST['content']);
    prune_versions($name, $historyDir, $maxVersions);
    echo "<p class='notice'>Сохранено</p>";
}

if ($action === 'restore' && $name && isset($_GET['ver'])) {
    $ver = basename($_GET['ver']);
    $file = "$historyDir/$name/$ver";
    if (file_exists($file)) {
        $content = file_get_contents($file);
        file_put_contents("$blocksDir/$name.html", $content);
        echo "<p class='notice'>Версия восстановлена</p>";
    }
}

if ($action === 'delete' && $name && isset($_GET['ver'])) {
    $ver = basename($_GET['ver']);
    $file = "$historyDir/$name/$ver";
    if (file_exists($file)) unlink($file);
}

if ($action === 'fav' && $name && isset($_GET['ver'])) {
    $ver = basename($_GET['ver']);
    $dir = "$historyDir/$name";
    $favFile = "$dir/favorites.json";
    $fav = file_exists($favFile) ? json_decode(file_get_contents($favFile), true) : [];
    if (!in_array($ver, $fav)) $fav[] = $ver; else $fav = array_diff($fav, [$ver]);
    file_put_contents($favFile, json_encode(array_values($fav)));
}

$editing = false;
if ($name) {
    $path = "$blocksDir/$name.html";
    $content = file_exists($path) ? file_get_contents($path) : '';
    $editing = true;
    echo "<h2>Редактирование блока: $name</h2>";
    echo "<form method='post' action='?action=save&edit=$name'>";
    echo "<textarea id='editor' name='content' style='width:100%;height:300px;'>".htmlspecialchars($content)."</textarea><br>";
    echo "<button class='button' type='submit'>Сохранить</button>";
    echo "</form>";
    echo "<script src='https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js' referrerpolicy='origin'></script>";
    echo "<script>tinymce.init({selector:'#editor',height:300,skin:'oxide-dark',content_css:'dark',plugins:'image code',toolbar:'undo redo | bold italic | alignleft aligncenter alignright | code image',images_upload_url:'upload.php'});</script>";
    // history
    $dir = "$historyDir/$name";
    if (is_dir($dir)) {
        $files = glob("$dir/*.html");
        if ($files) {
            echo "<h3>История</h3>";
            echo "<ul>";
            $fav = [];
            $favFile = "$dir/favorites.json";
            if (file_exists($favFile)) $fav = json_decode(file_get_contents($favFile), true);
            foreach ($files as $f) {
                $base = basename($f);
                $time = date('Y-m-d H:i:s', filemtime($f));
                $star = in_array($base, $fav) ? '★' : '☆';
                echo "<li>$time <a href='?action=restore&edit=$name&ver=$base'>[восстановить]</a> <a href='?action=delete&edit=$name&ver=$base'>[удалить]</a> <a href='?action=fav&edit=$name&ver=$base'>$star</a></li>";
            }
            echo "</ul>";
        }
    }
    echo "<hr>";
}

$files = glob("$blocksDir/*.html");
echo "<h2>Блоки</h2>";
if ($files) {
    foreach ($files as $f) {
        $bname = basename($f, '.html');
        $snippet = '{{block:'.$bname.'}}';
        echo "<div class='block'><strong>$bname</strong> - <span class='snippet' onclick=\"copySnippet('$snippet')\">$snippet</span> <a class='button' href='?edit=$bname'>Редактировать</a></div>";
    }
}

echo "<form method='get'><input type='text' name='edit' placeholder='Новый блок'><button class='button' type='submit'>Создать</button></form>";

include 'footer.php';
?>
