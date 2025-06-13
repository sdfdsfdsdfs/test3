<?php
// Simple CMS front controller
$template = file_get_contents(__DIR__ . '/templates/index.html');
if (!$template) { die('Missing main template'); }
preg_match_all('/{{block:(.*?)}}/', $template, $matches);
foreach ($matches[1] as $blockName) {
    $file = __DIR__ . "/blocks/$blockName.html";
    $content = file_exists($file) ? file_get_contents($file) : '';
    $template = str_replace("{{block:$blockName}}", $content, $template);
}

echo $template;
?>
