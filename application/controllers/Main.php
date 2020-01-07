<?php
/**
 * Created by PhpStorm.
 * User: tangtianyu
 * Date: 2018/1/17
 * Time: 下午10:21
 * 主页类,目前用来呈现市场指数信息
 */

class Main extends CI_Controller {

    const STATUS_INIT = 0;
    const STATUS_DEALING = 1;
    const STATUS_SUCCUESS = 2;
    const STATUS_FAIL = 3;

    protected $arrStatus = array(
        self::STATUS_INIT => '初始化',
        self::STATUS_DEALING => '转换中',
        self::STATUS_SUCCUESS => '转换成功',
        self::STATUS_FAIL => '转换失败',
    );

    protected $downloadDir = 'download/';

	function __construct() {
		parent::__construct();
		$this->load->model('task_model','tm');
	}

	/**
	 * 主页页面呈现
	 * Detail description
	 * @param
	 * @since     1.0
	 * @access    public
	 * @return    void
	 * @throws
	 */
	public function index(){
		//构造堆叠图数据
        date_default_timezone_set("Asia/Shanghai");
		$arrTaskInfo = $this->tm->getTaskListInfo();
		foreach ($arrTaskInfo as $k => &$v) {
		    $v['createTime'] = date("Y-m-d H:i:s", $v['createTime']);
		    $v['updateTime'] = empty($v['updateTime']) ? '' : date("Y-m-d H:i:s", $v['updateTime']);
		    $v['status'] = $this->arrStatus[$v['status']];
		    $v['contentFile'] = empty($v['contentFile']) ? '' : base_url() . "/download/" . $v['contentFile'];
		    $v['detailFile'] = empty($v['detailFile']) ? '' : base_url() . "/download/" . $v['detailFile'];
		}
		$return['data'] = $arrTaskInfo;
		$this->load->view("Main.php", $return);
	}

    public function process() {
        $strMod = $this->input->post('mod');
        $strTaskName = $this->input->post('taskName');
        $strTaskId = $this->input->post('taskId');
        switch($strMod) {
//            case '修改' :
//                $bolRet = $this->tm->updateTaskNameByTaskId($strTaskName, $strTaskId);
//                break;
            case 'query' :
                $bolRet = $this->queryTaskInfo($strTaskId);
                break;
            case 'del' :
                $bolRet = $this->del($strTaskId);
                break;
            case '上传' :
                $bolRet = $this->upload();
                break;
            default :
                break;
        }
        $arrRet['ret'] = $bolRet;
        return $arrRet;
	}

    public function upload() {
	    //TODO:用框架upload相关类
        $dir = '/Users/tangtianyu/www/baiduai/upload/';
        $fileName = $_FILES["uploadFile"]["name"];
        $strNewFileName = md5(time()) .".mp3";
        move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $dir . $strNewFileName);
        $strNewFileName = '1576841208.mp3';
        $url = "http://120.79.24.61/baiduai/upload/$strNewFileName";

        $arrRet = $this->createTask($url);

        if (empty($arrRet['error_code']) && !empty($arrRet['task_id']) && $arrRet['task_status'] == 'Created') {
            $strTaskId = $arrRet['task_id'];
            //插入
            $bolRet = $this->tm->addRecord($fileName,$strTaskId);
            if (!$bolRet) {
                return false;
            }
            $arrRet = $this->queryTask($strTaskId);
            redirect("Main/index");
        } else {
            return false;
        }

    }
	
	
	public function queryTaskInfo($strTaskId) {
        if (empty($strTaskId)) {
            return false;
        }
        $arrConds  = array('taskId' => $strTaskId);
        $arrTask = $this->tm->get(array('*'), $arrConds);
//        if (!empty($arrTask['contentFile']) && !empty($arrTask['detailFile'])) {
//            return true;
//        } else {
            $this->queryTask($strTaskId);
//        }
        redirect("Main/index");
	}


    /**
     * @brief   创建语音转文本任务
     * @author  唐天宇 <tangtianyu@baidu.com>
     * @version 2019-12-16 19:05:32
     * @param   $mp3Url
     */
    public function createTask($mp3Url) {
        $url = 'https://aip.baidubce.com/rpc/2.0/aasr/v1/create';
        $arrPost = array(
            'speech_url' => $mp3Url,
            'format' => 'mp3',
            'pid' => 1537,
        );
        $strToken = $this->getToken();
        $url .= '?access_token=' . $strToken;

        $strJson = json_encode($arrPost);
        $arrHeaders[] = "Content-Length: ".strlen($strJson);
        $arrHeaders[] = 'Content-Type: application/json; charset=utf-8';
        $res = $this->request_post($url, $strJson, $arrHeaders);
        $arrRet = json_decode($res, true);
        return  $arrRet;
    }

    /**
     * @brief   查询任务状态
     * @author  唐天宇 <tangtianyu@baidu.com>
     * @version 2019-12-16 19:06:28
     * @param   string $strTask
     */
    public function queryTask($strTaskId) {
        if (empty($strTaskId)) {
            return false;
        }

        $url = 'https://aip.baidubce.com/rpc/2.0/aasr/v1/query';
        $arrPost = array(
            'task_ids' => array($strTaskId),
        );
        $strToken = $this->getToken();
        $url .= '?access_token=' . $strToken;

        $strJson = json_encode($arrPost);
        $arrHeaders[] = "Content-Length: ".strlen($strJson);
        $arrHeaders[] = 'Content-Type: application/json; charset=utf-8';
        $res = $this->request_post($url, $strJson, $arrHeaders);
        $arrRet = json_decode($res, true);
        $arrTaskInfo = $arrRet['tasks_info'][0];
        $strStatus = $arrTaskInfo['task_status'];
        if ($strStatus == 'Running') {
            $this->tm->updateTaskFieldsByTaskId(array('status' => self::STATUS_DEALING, 'updateTime' => time()), $strTaskId);
        } else if ($strStatus == 'Success') {
            $outputFile =  $strTaskId . '.txt';
            $outputFileDetails = $strTaskId . '-details.txt';

            // 写入全文本
            file_put_contents($this->downloadDir . $outputFile, $arrTaskInfo['task_result']['result'][0]);
            // 写入文本细节
            $arrDetails = $this->convertDetailContent($arrTaskInfo['task_result']['detailed_result']);
            $arrLines = array();
            foreach($arrDetails as $arrLine) {
                $arrLines[] = implode("\t", $arrLine);
            }
            file_put_contents($this->downloadDir . $outputFileDetails, implode("\n", $arrLines));

            // 更新数据库
            $arrFields = array(
                'status' => self::STATUS_SUCCUESS,
                'updateTime' => time(),
                'contentFile' => $outputFile,
                'detailFile'  => $outputFileDetails,
            );
            $this->tm->updateTaskFieldsByTaskId($arrFields, $strTaskId);
        } else if ($strStatus == 'Failure') {
            $arrFields = array(
                'status' => self::STATUS_FAIL,
                'updateTime' => time(),
            );
            $this->tm->updateTaskFieldsByTaskId($arrFields, $strTaskId);
        }
    }

    /**
     * @brief   获取token
     * @author  唐天宇 <tangtianyu@baidu.com>
     * @version 2019-12-16 19:05:51
     * @return  mixed
     */
    public function getToken() {
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $arrPostData['grant_type']       = 'client_credentials';
        $arrPostData['client_id']      = 'zPC8FwUSsvy6Zbzr01RuFGsQ';
        $arrPostData['client_secret'] = 'AVHihdfPTy9PpXZ1DzaOPUvNIqBrsGGA';
        $strQuery = "";
        foreach ($arrPostData as $k => $v ) {
            $strQuery .= "$k=" . urlencode( $v ). "&" ;
        }
        $strPost = substr($strQuery,0,-1);
        $res = $this->request_post($url, $strPost);
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
    public function request_post($url = '', $param = '', $arrHeader = array()) {
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

    public function convertDetailContent($arrData) {
        $arrRet = array();
        foreach($arrData as $lineNo => $v) {
            $strStartTime = date("H时i分s秒", intval($v['begin_time'] / 1000));
            $strEndTime = date("H时i分s秒", intval($v['end_time'] / 1000));
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


    public function microtime_format($tag, $time) {

        $time = strval($time / 1000);
        list($usec, $sec) = explode(".", $time);

        $date = date($tag,$usec);

        return str_replace('x', $sec, $date);

    }


}
