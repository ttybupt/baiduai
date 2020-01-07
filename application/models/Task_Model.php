<?php
/**
 * Created by PhpStorm.
 * User: tangtianyu
 * Date: 2019-12-21
 * Time: 下午10:10
 */

class Task_Model extends MY_Model {
	private $_tables;
	/**
	 * @param $arrTable
	 * @param $arrTimeFields
	 * @param $arrFields
	 * @return array
	 */
	private $_arrFields = array(
        'id',
        'taskId',
        'taskName',
        'status',
        'contentFile',
        'detailFile',
        'createTime',
        'updateTime',
        'uid',
        'uname',
        'extFlag',
        'extData',
	);
	function __construct() {
		$this->_table = 'tblBdAITask';
		$this->_orderBy = "createTime";
		$this->_order = 'DESC';
	}

	protected $dataDir = 'upload';

    /**
     * @brief   查全表
     * @author  唐天宇 <tangtianyu@baidu.com>
     * @version 2019-12-21 11:23:46
     * @return mixed
     */
    public function getTaskListInfo() {
        $arrConds = array(
        );
        $arrResult = $this->select(array('*'),$arrConds);
        return $arrResult;
	}

    /**
     * @brief   更新任务名
     * @author  唐天宇 <tangtianyu@baidu.com>
     * @version 2019-12-21 15:27:50
     * @param   $strTaskName
     * @param   $strTaskId
     * @return   bool|mixed
     */
	public function updateTaskNameByTaskId($strTaskName, $strTaskId) {
        if (empty($strTaskName) || empty($strTaskId)) {
            return false;
        }
        $arrFields = array(
            'taskName' => $strTaskName,
        );
        $arrConds = array(
            'taskId' => $strTaskId,
        );
        $bolRet = $this->update($arrFields, $arrConds);
        return $bolRet;
    }

    /**
     * @brief   更新任务名
     * @author  唐天宇 <tangtianyu@baidu.com>
     * @version 2019-12-21 15:27:50
     * @param   $strTaskName
     * @param   $strTaskId
     * @return   bool|mixed
     */
    public function updateTaskFieldsByTaskId($arrFields, $strTaskId) {
        if (empty($arrFields) || empty($strTaskId)) {
            return false;
        }
        $arrConds = array(
            'taskId' => $strTaskId,
        );
        $bolRet = $this->update($arrFields, $arrConds);
        return $bolRet;
    }

    /**
     * @brief   删除任务
     * @author  唐天宇 <tangtianyu@baidu.com>
     * @version 2019-12-21 15:28:08
     * @param   $strTaskId
     * @return  bool
     */
    public function deleteTaskItemByTaskId($strTaskId) {
        if (empty($strTaskId)) {
            return false;
        }
        $arrConds = array('taskId' => $strTaskId);
        $arrTaskInfo = $this->get(array('*'), $arrConds);
        $this->delete();
    }

    /**
     * @brief   添加任务记录
     * @author  唐天宇 <tangtianyu@baidu.com>
     * @version 2019-12-21 16:04:37
     * @param   $strTaskId
     * @param   $strTaskName
     * @return  bool
     */
    public function addRecord($strTaskId, $strTaskName) {
        $arrFields = array(
            'taskId' => $strTaskId,
            'taskName' => $strTaskName,
//            'originFile' => $strTaskName,
            'createTime' => time(),
        );
        $bolRet = $this->insert($arrFields);
        return $bolRet;
    }
}
