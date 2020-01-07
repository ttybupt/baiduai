<?php

//$url = $argv[1];
//if (empty($url)) {
//    echo "请输入音频url地址！！\n";die;
//}
//createTask($url);
// queryTask('5df76421f66d4cad9b798496');
/**
 * @brief   创建语音转文本任务
 * @author  唐天宇 <tangtianyu@baidu.com>
 * @version 2019-12-16 19:05:32
 * @param   $mp3Url
 */
function createTask($mp3Url) {
    $url = 'https://aip.baidubce.com/rpc/2.0/aasr/v1/create';
    $arrPost = array(
        'speech_url' => $mp3Url,
        'format' => 'mp3',
        'pid' => 1537,
    );
    $strToken = getToken();
    $url .= '?access_token=' . $strToken;

    $strJson = json_encode($arrPost);
    $arrHeaders[] = "Content-Length: ".strlen($strJson);
    $arrHeaders[] = 'Content-Type: application/json; charset=utf-8';
    $res = request_post($url, $strJson, $arrHeaders);
    $arrRet = json_decode($res, true);

    if (empty($arrRet['error_code']) && !empty($arrRet['task_id']) && $arrRet['task_status'] == 'Created') {
        echo "<p>创建任务成功！！任务id为：" . $arrRet['task_id'] ."</p>";
    } else {
        echo "<p>任务失败</p>";
        var_dump($arrRet);
        return false;
    }
    return $arrRet['task_id'];
}

/**
 * @brief   查询任务状态
 * @author  唐天宇 <tangtianyu@baidu.com>
 * @version 2019-12-16 19:06:28
 * @param   string $strTask
 */
function queryTask($strTask = '5df76421f66d4cad9b798496') {
    $url = 'https://aip.baidubce.com/rpc/2.0/aasr/v1/query';
    $arrPost = array(
        'task_ids' => array($strTask),
    );
    $strToken = getToken();
    $url .= '?access_token=' . $strToken;

    $strJson = json_encode($arrPost);
    $arrHeaders[] = "Content-Length: ".strlen($strJson);
    $arrHeaders[] = 'Content-Type: application/json; charset=utf-8';
    $res = request_post($url, $strJson, $arrHeaders);
    $arrRet = json_decode($res, true);
    $arrTaskInfo = $arrRet['tasks_info'][0];
    $strStatus = $arrTaskInfo['task_status'];
    if ($strStatus == 'Running') {
        echo "<p>正在进行转写,请耐心等候</p>";
    } else if ($strStatus == 'Success') {
        $dir = 'download/';
        $outputFile =  'tmp-' . $strTask . '.txt';
        $outputFileDetails = 'tmp-' . $strTask . '-details.txt';
        echo "<p>转写成功！将内容写入临时文件：http://120.79.24.61/$outputFile 和  http://120.79.24.61/$outputFileDetails</p>";
        echo "<p>完整内容：</p>";
        echo "<p>" . $arrTaskInfo['task_result']['result'][0] . "</p>";
        // echo "<p>详细内容：</p>";
        $arrDetails = convertDetailContent($arrTaskInfo['task_result']['detailed_result']);

        echo '<table border="1" align="center">';
        echo '<caption><h1>详细内容</h1></caption>';
        echo '<tr bgcolor="#dddddd">';
        echo '<th>编号</th><th>开始时间</th><th>结束时间</th><th>内容文本</th>';
        echo '</tr>';
        //使用双层for语句嵌套二维数组$contact1,以HTML表格的形式输出
        //使用外层循环遍历数组$contact1中的行
        for($row=0;$row<count($arrDetails);$row++) {
            echo '<tr>';
            //使用内层循环遍历数组$contact1 中 子数组的每个元素,使用count()函数控制循环次数
            for($col=0;$col<count($arrDetails[$row]);$col++)
            {
                echo '<td>'.$arrDetails[$row][$col].'</td>';
            }
            echo '</tr>';
        }
        echo '</table>';


        if (!is_dir($dir)) {
            mkdir($dir);
        }
        file_put_contents($dir . $outputFile, $arrTaskInfo['task_result']['result'][0]);
        $arrLines = array();
        foreach($arrDetails as $arrLine) {
            $arrLines[] = implode("\t", $arrLine);
        }
        file_put_contents($dir . $outputFileDetails, implode("\n", $arrLines));
    } else if ($strStatus == 'Failure') {
        echo "<p>转写失败！</p>";
        var_dump($arrRet);
    }
}

/**
 * @brief   获取token
 * @author  唐天宇 <tangtianyu@baidu.com>
 * @version 2019-12-16 19:05:51
 * @return  mixed
 */
function getToken() {
    $url = 'https://aip.baidubce.com/oauth/2.0/token';
    $arrPostData['grant_type']       = 'client_credentials';
    $arrPostData['client_id']      = 'zPC8FwUSsvy6Zbzr01RuFGsQ';
    $arrPostData['client_secret'] = 'AVHihdfPTy9PpXZ1DzaOPUvNIqBrsGGA';
    $strQuery = "";
    foreach ($arrPostData as $k => $v ) {
        $strQuery .= "$k=" . urlencode( $v ). "&" ;
    }
    $strPost = substr($strQuery,0,-1);
    $res = request_post($url, $strPost);
    $arrRet = json_decode($res, true);
    return $arrRet['access_token'];
}

/**
 * @brief   curl请求封装
 * @author  唐天宇 <tangtianyu@baidu.com>
 * @version 2019-12-16 19:06:06
 * @param   string $url
 * @param   string $param
 * @param   array  $arrHeader
 * @return  bool|string
 */
function request_post($url = '', $param = '', $arrHeader = array()) {
    if (empty($url) || empty($param)) {
        return false;
    }

    $postUrl = $url;
    $curlPost = $param;
    $curl = curl_init();//初始化curl
    curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    if (!empty($arrHeader)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $arrHeader);
    }
    $data = curl_exec($curl);//运行curl
    curl_close($curl);

    return $data;
}

function convertDetailContent($arrData) {
    $arrRet = array();
    foreach($arrData as $lineNo => $v) {
        $strStartTime = microtime_format("H时i分s秒 x毫秒", $v['begin_time']);
        $strEndTime = microtime_format("H时i分s秒 x毫秒", $v['end_time']);
        // $$arrRet[] = array(
        //     'index' => $lineNo + 1,
        //     'startTime' => $strStartTime,
        //     'endTime' => $strEndTime,
        //     'content' => $v['res'][0],
        // );
        $arrRet[] = array(
            $lineNo + 1,
            $strStartTime,
            $strEndTime,
            $v['res'][0],
        );
    }
    return $arrRet;
}


function microtime_format($tag, $time) {
    $time = strval($time / 1000);
    list($usec, $sec) = explode(".", $time);

    $date = date($tag,$usec);

    return str_replace('x', $sec, $date);

}
