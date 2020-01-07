<?php
include_once('mp3record.php');

$strTaskId = $_POST['taskId'];

echo "<h3>要查询的taskId为：$strTaskId</h3>";

queryTask($strTaskId);
