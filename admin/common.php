<?php
session_start();
if(!isset($_SESSION['user'])){header('Location: login.php');exit;}

function get_setting($key, $default){
    $settings = json_decode(file_get_contents(__DIR__.'/../history/settings.json'), true);
    return $settings[$key] ?? $default;
}

function save_file($dir, $name, $content){
    $path = __DIR__.'/..';
    $file = "$path/$dir/$name.html";
    if(file_exists($file)){
        add_history($dir, $name, file_get_contents($file));
    }
    file_put_contents($file, $content);
}

function add_history($dir, $name, $content){
    $path = __DIR__.'/..';
    $historyDir = "$path/history/$dir";
    if(!is_dir($historyDir)) mkdir($historyDir, 0777, true);
    $timestamp = date('Ymd_His');
    $file = "$historyDir/{$name}_$timestamp.html";
    file_put_contents($file, $content);
    clean_history($historyDir);
}

function clean_history($historyDir){
    $max = get_setting('max_versions',5);
    $favFile = "$historyDir/favorites.json";
    $favorites = file_exists($favFile) ? json_decode(file_get_contents($favFile), true) : [];
    $files = glob("$historyDir/*.html");
    usort($files, function($a,$b){return filemtime($b)-filemtime($a);});
    $count=0;
    foreach($files as $f){
        $base = basename($f);
        if(in_array($base,$favorites)) continue;
        if($count++ >= $max) unlink($f);
    }
}

function list_items($dir){
    $path = __DIR__.'/..';
    $files = glob("$path/$dir/*.html");
    return array_map(function($f){return basename($f,'.html');},$files);
}

function get_history_list($dir,$name){
    $path = __DIR__.'/..';
    $historyDir = "$path/history/$dir";
    $files = glob("$historyDir/{$name}_*.html");
    usort($files,function($a,$b){return filemtime($b)-filemtime($a);});
    return $files;
}

function toggle_favorite($dir,$file){
    $favFile = __DIR__.'/../history/'.$dir.'/favorites.json';
    $favorites = file_exists($favFile) ? json_decode(file_get_contents($favFile),true):[];
    if(in_array($file,$favorites)){
        $favorites = array_diff($favorites,[$file]);
    }else{
        $favorites[]=$file;
    }
    file_put_contents($favFile,json_encode(array_values($favorites)));
}
?>
