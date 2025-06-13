<?php
$dir = __DIR__.'/uploads';
if (!is_dir($dir)) mkdir($dir, 0777, true);
$fname = basename($_FILES['file']['name']);
$path = $dir.'/'.time().'_'.$fname;
if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
    echo json_encode(['location' => $path]);
}
?>
