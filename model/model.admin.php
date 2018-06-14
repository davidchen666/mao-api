<?php

class AdminModel extends AgentModel
{
    public function verifyLogin(){
        $pData = getData();
        // $res = $this->mysqlQuery($sql, "all");
        if($pData['username'] && $pData['password']){
            // $newPwd = md5(md5($pData['password'].'chc'));
        	$newPwd = $this->__encodePassword($pData['password']);
        	$sqltotal = "select user_id userid,user_name username FROM user_admin  WHERE 1=1 AND user_name= '{$pData['username']}' AND user_pwd= '{$newPwd}' ";
        	$ret = $this->mysqlQuery($sqltotal, "all");
        	if(count($ret) === 1){
                //生成token
                $nowTime = NOW;
                $randNum = rand(1, 99999);
                $token = md5((string)$ret[0]['userid'].$ret[0]['username'].$nowTime.(string)randNum);
                // return to_success($ret[0]);
                if($this->__saveLogin($ret[0]['userid'],$token,$nowTime)){
                	return to_success(array('token'=>$token));
                }else{
                	return to_error('登录失败，请刷新网页重新登录。');
                }
        	}else{
        		return to_error('用户名或密码错误。');
        	}
        }else{
        	return to_error('用户名或密码不合法。');
        }
        // return $res;
    }

    //获取管理员信息
    public function getAdminInfo(){
        $pData = getData();
        $sql = "SELECT user_id,user_name FROM user_admin where user_id in(select user_id from user_admin_token where user_token='{$pData['token']}' AND login_state=1)";
        $ret = $this->mysqlQuery($sql, "all");
        if(count($ret) === 1){
        	return to_success($ret[0]);
        }else{
        	return to_error('无效的token。请重新登录。');
        }
    }

    //获取管理员列表
    public function getAdminList(){
        $pData = getData();
        $filter = '';
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的用户
        $filter = 'AND user_state <> -4 ';
        //搜索条件
        if($pData['status']){
            $filter .= " AND user_state='{$pData['status']}' ";
        }
        if($pData['searchVal']){
            $filter .= " AND (user_name like '%{$pData['searchVal']}%' OR user_id like '%{$pData['searchVal']}%' OR user_realName like '%{$pData['searchVal']}%' OR user_mobile like '%{$pData['searchVal']}%' OR user_mail like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getAdminCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT user_id, user_name, user_state, user_mobile, user_mail, user_realName, user_remark, create_date, update_date FROM user_admin WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //添加管理员
    public function addAdmin(){
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
        if($this->__getAdminCount($filter) > 0){
            return to_error('该用户名已重复，请更换用户名。');
        }
        $arrData = array(
            "user_name" => $pData['username'],
            "user_pwd" => $this->__encodePassword($pData['password']),
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

    //编辑管理员信息
    public function editAdmin(){
        $pData = getData();
        //验证数据
        // if(!$pData['username'] && strlen($pData['username']) < 4){
        //     return to_error('操作失败,用户名不能为空且至少4位。');
        // }
        //查看用户是否存在
        $filter = " user_name = '{$pData['username']}' AND user_id='{$pData['userid']}' ";
        if($this->__getAdminCount(' AND '.$filter) === 0){
            return to_error('操作失败！该用户不存在。');
        }else if($this->__getAdminCount(' AND '.$filter) > 1){
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
    public function editAdminPwd(){
        $pData = getData();
        //验证数据
        if(!$pData['password'] && strlen($pData['username']) < 6){
            return to_error('操作失败，密码不能为空且不能至少6位。');
        }
        //查看用户是否存在
        $filter = " user_name = '{$pData['username']}' AND user_id='{$pData['userid']}' ";
        if($this->__getAdminCount(' AND '.$filter) === 0){
            return to_error('操作失败！该用户不存在。');
        }else if($this->__getAdminCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户，存在多个该用户名用户名');
        }
        $arrData = array(
            "user_pwd" => $this->__encodePassword($pData['password']),
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("user_admin", $arrData, $filter));
    }

    //删除管理员
    public function delAdmin(){
        $pData = getData();    
        //查看用户是否存在
        $filter = " user_name = '{$pData['username']}' AND user_id='{$pData['userid']}' ";
        if($this->__getAdminCount(' AND '.$filter) === 0){
            return to_error('操作失败！该用户不存在。');
        }else if($this->__getAdminCount(' AND '.$filter) > 1){
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

    //加密生成新的密码
    private function __encodePassword($pwd){
        return md5(md5($pwd.'chc'));
    }

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
    private function __getAdminCount($filter){
        $sql = "SELECT COUNT(*) total FROM user_admin WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

}