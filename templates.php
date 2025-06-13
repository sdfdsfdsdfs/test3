<?php
include 'menu.php';
$tplDir = __DIR__.'/templates';
$historyDir = __DIR__.'/history/templates';
$settings = json_decode(file_get_contents(__DIR__.'/history/settings.json'), true);
$maxVersions = isset($settings['templates']) ? (int)$settings['templates'] : 10;
$name = $_GET['edit'] ?? '';
$action = $_GET['action'] ?? '';

function save_version_tpl($name, $content, $historyDir) {
    $dir = "$historyDir/$name";
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $file = $dir.'/'.time().'.html';
    file_put_contents($file, $content);
}

function prune_versions_tpl($name, $historyDir, $maxVersions) {
    $dir = "$historyDir/$name";
    if (!is_dir($dir)) return;
    $favFile = "$dir/favorites.json";
    $fav = file_exists($favFile) ? json_decode(file_get_contents($favFile), true) : [];
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
    if ($name === 'index') {
        $path = "$tplDir/$name.html";
    } else {
        $path = "$tplDir/$name.html";
    }
    $old = file_exists($path) ? file_get_contents($path) : '';
    if ($old) save_version_tpl($name, $old, $historyDir);
    file_put_contents($path, $_POST['content']);
    prune_versions_tpl($name, $historyDir, $maxVersions);
    echo "<p class='notice'>Сохранено</p>";
}

if ($action === 'restore' && $name && isset($_GET['ver'])) {
    $ver = basename($_GET['ver']);
    $file = "$historyDir/$name/$ver";
    if (file_exists($file)) {
        $content = file_get_contents($file);
        file_put_contents("$tplDir/$name.html", $content);
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

if ($action === 'delete_tpl' && $name && $name !== 'index') {
    $path = "$tplDir/$name.html";
    if (file_exists($path)) unlink($path);
    $name = '';
}

if ($name) {
    $path = "$tplDir/$name.html";
    $content = file_exists($path) ? file_get_contents($path) : '';
    echo "<h2>Редактирование шаблона: $name</h2>";
    echo "<form method='post' action='?action=save&edit=$name'>";
    echo "<textarea id='editor' name='content' style='width:100%;height:300px;'>".htmlspecialchars($content)."</textarea><br>";
    echo "<button class='button' type='submit'>Сохранить</button>";
    if ($name !== 'index') echo " <a class='button' href='?action=delete_tpl&edit=$name'>Удалить шаблон</a>";
    echo "</form>";
    echo "<script src='https://cdn.jsdelivr.net/npm/ace-builds@1.23.1/src-min-noconflict/ace.js'></script>";
    echo "<script>var editor = ace.edit('editor');editor.setTheme('ace/theme/monokai');editor.session.setMode('ace/mode/html');</script>";
    // history
    $dir = "$historyDir/$name";
    if (is_dir($dir)) {
        $files = glob("$dir/*.html");
        if ($files) {
            echo "<h3>История</h3><ul>";
            $favFile = "$dir/favorites.json";
            $fav = file_exists($favFile) ? json_decode(file_get_contents($favFile), true) : [];
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

$files = glob("$tplDir/*.html");
if ($files) {
    echo "<h2>Шаблоны</h2>";
    foreach ($files as $f) {
        $tname = basename($f, '.html');
        $snippet = '{{template:'.$tname.'}}';
        $del = $tname !== 'index' ? "<a href='?edit=$tname&action=delete_tpl'>[удалить]</a>" : '';
        echo "<div class='block'><strong>$tname</strong> - <span class='snippet' onclick=\"copySnippet('$snippet')\">$snippet</span> <a class='button' href='?edit=$tname'>Редактировать</a> $del</div>";
    }
}

echo "<form method='get'><input type='text' name='edit' placeholder='Новый шаблон'><button class='button' type='submit'>Создать</button></form>";

include 'footer.php';
?>
