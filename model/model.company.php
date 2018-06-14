<?php

class CompanyModel extends AgentModel
{

    //获取公司信息
    public function getCompanyInfo(){
        $pData = getData();
        $sql = "SELECT user_id,user_name FROM user_admin where user_id in(select user_id from user_admin_token where user_token='{$pData['token']}' AND login_state=1)";
        $ret = $this->mysqlQuery($sql, "all");
        if(count($ret) === 1){
        	return to_success($ret[0]);
        }else{
        	return to_error('无效的token。请重新登录。');
        }
    }

    //获取公司列表
    public function getCompanyList(){
        $pData = getData();
        $filter = '';
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的用户
        $filter = 'AND user_state <> -4 ';
        //搜索条件
        if($pData['info_state']){
            $filter .= " AND info_state='{$pData['info_state']}' ";
        }
        if($pData['connect_state']){
            $filter .= " AND connect_state='{$pData['info_state']}' ";
        }
        if($pData['searchVal']){
            $filter .= " AND (company_name like '%{$pData['searchVal']}%' OR domain_name like '%{$pData['searchVal']}%' OR mail_brand like '%{$pData['searchVal']}%' OR domain_info like '%{$pData['searchVal']}%' OR person_mail like '%{$pData['searchVal']}%' OR remark like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getCompanyCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT * FROM company WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //添加公司
    public function addCompany(){
        $pData = getData();
        //验证数据
        if(!$pData['username'] && strlen($pData['username']) < 4){
            return to_error('用户名不能为空且至少4位。');
        }
        if(!$pData['password'] && strlen($pData['username']) < 6){
            return to_error('密码不能为空且不能至少6位。');
        }
        //查看用户名是否已经重复
        $filter = " AND user_name = '{$pData['username']}' ";
        if($this->__getCompanyCount($filter) > 0){
            return to_error('该用户名已重复，请更换用户名。');
        }
        $arrData = array(
            "user_name" => $pData['username'],
            "user_pwd" => $pData['password'],
            "user_state" => 1,
            "user_realName" => $pData['realname'],
            "user_mobile" => $pData['mobile'],
            "user_mail" => $pData['email'],
            "user_remark" => $pData['remark'],
            "create_date" => NOW,
            "update_date" => NOW
        );
        return to_success($this->mysqlInsert("user_admin", $arrData, 'single', true));
    }

    //编辑公司信息
    public function editCompany(){
        $pData = getData();
        //验证数据
        // if(!$pData['username'] && strlen($pData['username']) < 4){
        //     return to_error('操作失败,用户名不能为空且至少4位。');
        // }
        //查看用户是否存在
        $filter = " user_name = '{$pData['username']}' AND user_id='{$pData['userid']}' ";
        if($this->__getCompanyCount(' AND '.$filter) === 0){
            return to_error('操作失败！该用户不存在。');
        }else if($this->__getCompanyCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户，存在多个该用户名用户');
        }
        $arrData = array(
            "user_state" => $pData['state'],
            "user_realName" => $pData['realname'],
            "user_mobile" => $pData['mobile'],
            "user_mail" => $pData['email'],
            "user_remark" => $pData['remark'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("user_admin", $arrData, $filter));
    }

    //重置密码
    public function editCompanyPwd(){
        $pData = getData();
        //验证数据
        if(!$pData['password'] && strlen($pData['username']) < 6){
            return to_error('操作失败，密码不能为空且不能至少6位。');
        }
        //查看用户是否存在
        $filter = " user_name = '{$pData['username']}' AND user_id='{$pData['userid']}' ";
        if($this->__getCompanyCount(' AND '.$filter) === 0){
            return to_error('操作失败！该用户不存在。');
        }else if($this->__getCompanyCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户，存在多个该用户名用户名');
        }
        $arrData = array(
            "user_pwd" => $pData['password'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("user_admin", $arrData, $filter));
    }

    //删除公司
    public function delCompany(){
        $pData = getData();    
        //查看用户是否存在
        $filter = " user_name = '{$pData['username']}' AND user_id='{$pData['userid']}' ";
        if($this->__getCompanyCount(' AND '.$filter) === 0){
            return to_error('操作失败！该用户不存在。');
        }else if($this->__getCompanyCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户，存在多个该用户名用户名');
        }
        $arrData = array(
            "user_state" => -4,
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("user_admin", $arrData, $filter));
    }
    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //存储用户的登录token
    private function __saveLogin($uid,$token,$nowTime){
        //登录用户入库
    	$arrData = array(
            "user_id" => $uid,
            "user_token" => $token,
            "login_state" => 1,
            "create_date" => $nowTime,
            "update_date" => $nowTime
        );
        return $this->mysqlInsert("user_admin_token", $arrData, 'single', true);
    }

    //获取admin总数目
    private function __getCompanyCount($filter){
        $sql = "SELECT COUNT(*) total FROM company WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

}