<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * model层父类
 *
 * Detail description
 * @author
 * @version      1.0
 * @copyright
 * @since
 * @access       public
 */
class MY_Model extends CI_Model {

//    protected $_objModel;

    protected $_table;

    protected $_limit;

    protected $_offset = 0;

    protected $_orderBy;

    protected $_order = "DESC";
    /**
     * BaseDao_model constructor.
     */
//    function __construct() {
//
//    }
    /**
     * 增
     * @param $arrFields
     * @return bool
     */
    public function insert($arrFields) {
        if (!$this->_table) {
            return false;
        }
        $res = $this->db->insert($this->_table, $arrFields);
        if ($res) {
            $insert_id = $this->db->insert_id();
        }
        return $insert_id;
    }

    /**
     * 删
     * @param $arrConds
     * @return bool
     */
    public function delete($arrConds) {
        if (!$this->_table) {
            return false;
        }
        $this->where($arrConds);
        $res = $this->db->delete($this->_table);
        return $res;
    }
	/**
	 * @param array $arrFields
	 * @param array $arrConds
	 * @param array $arrOrderBy
	 * @param array $arrLimit
	 * @param bool $boolNoLimit true时不需要Limit部分
	 * @return mixed
	 * @author tangtianyu
	 * @date 2018-02-12
	 * @brief 查多条
	 */
    public function select($arrFields = array("*"), $arrConds = array(),  $arrOrderBy = array(), $arrLimit = array(), $boolNoLimit = false) {

    	if ($arrFields) {
            foreach ($arrFields as $v) {
                $this->db->select($v);
            }
        } else {
    		$arrFields = array("*");
		}
        $this->where($arrConds);
        $this->orderBy($arrOrderBy);
		if (!$boolNoLimit) {
			$this->limit($arrLimit);
		}
        $data = $this->db->get($this->_table)->result_array();
        return $data;
    }

    /**
     * 改
     * @param $arrConds
     * @param $arrFields
     * @return mixed
     */
    public function update($arrFields, $arrConds) {
        $this->where($arrConds);
        $this->db->update($this->_table, $arrFields);
        return $this->db->affected_rows();
    }

    /**
     * @param $arrLimit
     * @return $this
     */
    public function limit($arrLimit) {
        if (!$arrLimit) {
//            $this->db->limit($this->_limit, $this->_offset);
        } else if (isset($arrLimit['limit']) && isset($arrLimit['offset'])){
            $this->db->limit($arrLimit['limit'], $arrLimit['offset']);
        }
        return $this;
    }

    /**
     * @param $arrOrderBy
     * @return $this
     */
    public function orderBy($arrOrderBy) {
        if (!$arrOrderBy) {
            $this->db->order_by($this->_orderBy, $this->_order);
        } else {
            foreach ($arrOrderBy as $k => $v) {
                $this->db->order_by($k, $v);
            }
        }
        return $this;
    }

    /**
     * @param $arrConds
     */
    public function where($arrConds) {
        if ($arrConds) {
            foreach ($arrConds as $k => $v) {
                $this->db->where($k, $v);
            }
        }
    }
    /**
     * 查单行
     * @param array $arrFields
     * @param array $arrConds
     * @param array $arrOrderBy
     * @param array $arrLimit
     * @return mixed
     */
    public function get($arrFields = array("*"), $arrConds = array(),  $arrOrderBy = array(), $arrLimit = array()) {

        $data = $this->select($arrFields, $arrConds, $arrOrderBy, $arrLimit);
        return (isset($data[0]) && !empty($data))? $data[0]:false;
    }

    /**
     * @param $strSql
     * @return mixed
     */
    public function query($strSql) {
        $res = $this->db->query($strSql)->result_array();
        return $res;
    }

	/**
	 * @param $arrData
	 * @param $fltSalePrice
	 * @return bool|float|int 已经换算为%
	 * @author tangtianyu
	 * @date 2018-02-02
	 * @brief 计算定投的收益率
	 */
    public function autoInvestmentCal($arrData, $fltSalePrice) {
    	$fltPortion = 0.0;
    	if (!isset($arrData) || empty($arrData)) {
    		return false;
		}
		$intTotal = 0;
    	foreach ($arrData as $key => $value) {
    		if ($value == 0) {
    			continue;
			} else {
				$fltPortion += 1 / $value;
				$intTotal++;
			}

		}
		$fltSaleTotal = $fltPortion * $fltSalePrice;
//    	$intTotal = count($arrData);
    	$fltYield = ($fltSaleTotal - $intTotal) / $intTotal * 100;
    	return $fltYield;
	}


} // end class
