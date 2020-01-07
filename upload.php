<?php
include_once('mp3record.php');
if ($_FILES["file"]["error"] > 0) {
    echo "<p>Error: " . $_FILES["file"]["error"] . "</p>";
} else {
    echo "<p>上传文件: " . $_FILES["file"]["name"] . "</p>";
    echo "<p>文件类型: " . $_FILES["file"]["type"] . "</p>";
    echo "<p>文件大小: " . ($_FILES["file"]["size"] / 1024 / 1024) . " Mb</p>";
    $dir = '/var/www/upload/';
    $strNewFileName = time() .".mp3";
    if (file_exists($dir . $strNewFileName)) {
        echo "<p>" . $strNewFileName . " already exists. </p>";
    } else {
        move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $strNewFileName);
        $url = "http://120.79.24.61/$dir/strNewFileName";
        echo "<p>Stored in: " . $url . "</p>";
    }

    // TODO:入数据库记录
    echo "<p>准备开始创建任务</p>";
    // TODO:调用接口
    $strTaskId = createTask($url);
    if (!empty($strTaskId)) {
        file_put_contents('lastestTaskId.txt', $strTaskId);
        $strAllTaskId = file_get_contents('allTaskId.txt');
        $arrTaskId = explode("\n", $strAllTaskId);
        if (empty($arrTaskId)) {
            $arrTaskId = array();
        }
        $arrTaskId = array_merge($arrTaskId, array($strTaskId));
        file_put_contents('allTaskId.txt', implode("\n", $arrTaskId));
        echo "<p>任务状态：</p>"; 
        queryTask($strTaskId);
    }
}
