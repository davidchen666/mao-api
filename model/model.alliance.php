<?php
class AllianceModel extends AgentModel
{
    //路演报名
    public function addAllianceSignUp(){
    	$pData = getData();
        if(!$pData['com_name']){
            return to_error('公司名不能为空。');
        }
        if(!$pData['user_name']){
            return to_error('负责人不能为空。');
        }
        if(!$pData['user_job']){
            return to_error('负责人职业不能为空。');
        }
        if(!$pData['user_email']){
            return to_error('负责人邮箱不能为空。');
        }
        if(!$pData['user_mobile']){
            return to_error('负责人电话不能为空。');
        }
        if(!$pData['com_business']){
            return to_error('请填写公司从事业务');
        }
    	$nowTime  = NOW;
    	//添加公司信息
    	$arrData = array(
            "com_name" => "{$pData['com_name']}",
            "user_name" => "{$pData['user_name']}",
            "user_job" => "{$pData['user_job']}",
            "user_email" => "{$pData['user_email']}",
            "user_mobile" => "{$pData['user_mobile']}",
            "com_business" => "{$pData['com_business']}",
            "update_date" => "{$nowTime}",
            "create_date" => "{$nowTime}"
        );
        $id = $this->mysqlInsert("alliance_sign_up", $arrData, 'single', true);
        $res['id'] = $id;
        return to_success($res);
    }

    
    /*###############################################
      ################# admin #######################
    */###############################################
    //获取路演报名列表
    public function getAllianceRegisterList(){
        $pData = getData();
        $filter = '';
        //单条
        // if($pData['roadid']){
        //      $filter .= " AND road_id='{$pData['roadid']}' ";
        // }
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的
        // $filter .= 'AND road_state <> -4 ';
        //搜索条件
        // if($pData['status']){
        //     $filter .= " AND road_state='{$pData['status']}' ";
        // }
        //id    com_name    user_name   user_job    user_mobile user_email  file_name   road_id remark  create_date update_date
        if($pData['searchVal']){
            $filter .= " AND (com_name like '%{$pData['searchVal']}%' OR user_name like '%{$pData['searchVal']}%' OR user_job like '%{$pData['searchVal']}%' OR user_mobile like '%{$pData['searchVal']}%' OR user_email like '%{$pData['searchVal']}%' OR com_business like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getAllianceRegisterCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT id,com_name,user_name,user_job,user_mobile,user_email,com_business, remark,create_date,update_date FROM alliance_sign_up WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }
    

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //获取报名总数目
    private function __getAllianceRegisterCount($filter){
        $sql = "SELECT COUNT(*) total FROM alliance_sign_up WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

}