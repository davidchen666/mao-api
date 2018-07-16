<?php

class CompanyModel extends AgentModel
{

    //获取公司列表
    public function getCompanyList(){
        $pData = getData();
        $filter = '';
        $filter2 = '';
        $filterTotal = '';
        $nowDate = date("Y-m-d",time());
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的用户
        // $filter = 'AND user_state <> -4 ';
        //搜索条件-信息是否可用
        if($pData['info_state']){
            $filter .= " AND info_state='{$pData['info_state']}' ";
        }
        //搜索条件-报备状态
        if($pData['report_state']){
            $filter .= " AND report_state='{$pData['report_state']}' ";
        }
        //搜索条件-提醒
        if($pData['nextDate']){
            $filter .= " AND next_date like '%{$nowDate}%' ";
        }
        // var_dump($pData);die();
        // if($pData['connect_state']){
        //     $filter .= " AND connect_state='{$pData['connect_state']}' ";
        // }
        if($pData['dateRange'] && count($pData['dateRange']) === 2){
            $filterTotal .= " AND (bb.create_date>='{$pData['dateRange'][0]}' AND bb.create_date<='{$pData['dateRange'][1]}') ";
        }
        if($pData['searchVal']){
            $filterTotal .= " AND (aa.company_name like '%{$pData['searchVal']}%' OR aa.domain_name like '%{$pData['searchVal']}%' OR aa.mail_brand like '%{$pData['searchVal']}%' OR aa.domain_info like '%{$pData['searchVal']}%' OR aa.person_mail like '%{$pData['searchVal']}%' OR aa.phone like '%{$pData['searchVal']}%' OR aa.company_address like '%{$pData['searchVal']}%' or bb.remark like '%{$pData['searchVal']}%' ) ";
            // $filter .= "AND bb.remark like '%{$pData['searchVal']}%' ";
        }
        // $filter2 .= " AND state=1 AND create_date like '%{$nowDate}%' ";
        $filter2 .= " AND state=1 AND id in(SELECT MAX(id) FROM company_connect_log GROUP BY company_id ) ";
        //总条数
        $res['page']['total'] = $this->__getCompanyCount($filter,true,$filter2,$filterTotal);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        //排序
        $sortFilter = 'order by 1 desc';
        if(!empty($pData['order']) && !empty($pData['prop'])){
            $propStr = $pData['prop'];
            $orderStr = $pData['order'] === 'ascending'? 'asc':'desc';
            if($pData['prop'] === 'log_create_date'){
               $propStr = 'bb.create_date';
            }
            $sortFilter = " order by {$propStr} {$orderStr} ";
        }
        // $sql = "SELECT * FROM company_list WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        //aa.*,bb.create_date log_create_date,if(bb.create_date IS NULL,'-1','1') as connect_state,bb.remark
        $sql = "SELECT aa.*,bb.create_date log_create_date,bb.remark
                FROM (SELECT * FROM company_list WHERE 1=1 {$filter} ) AS aa
                LEFT JOIN (SELECT * FROM company_connect_log WHERE 1=1 {$filter2} ) AS bb
                ON aa.id=bb.company_id WHERE 1=1 {$filterTotal} {$sortFilter} {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        foreach ($res['items'] as $k => $v) {
            $res['items'][$k]['connect_state'] = '-1';
            $logDate = date("Y-m-d",strtotime($v['log_create_date']));
            // var_dump($logDate,$nowDate);
            if($logDate  === $nowDate){
                $res['items'][$k]['connect_state'] = '1';
            }
            if($pData['data_type'] == 'connect_log'){
                $logFilter = " company_id = '{$v['id']}' AND state=1 ";
                $res['items'][$k]['connect_log'] = $this->__getCompanyConnectLog(' AND '.$logFilter);
            }
        }
        return to_success($res);
    }

    //添加公司
    public function addCompany(){
        $pData = getData();
        //验证数据
        if(!$pData['company_name'] && strlen($pData['company_name']) < 1){
            return to_error('公司名称不能为空且至少1位。');
        }
        // if(!$pData['info_state'] && ($pData['info_state'] != -1 || $pData['info_state'] != 1)){
        //     return to_error('请选择信息状态');
        // }
        // if(!$pData['connect_state'] && ($pData['connect_state'] != -1 || $pData['connect_state'] != 1)){
        //     return to_error('请选择联系状态');
        // }
        //查看公司名是否已经重复
        $filter = " AND company_name = '{$pData['company_name']}' ";
        if($this->__getCompanyCount($filter) > 0){
            return to_error('该公司名已重复，请更换公司名。');
        }
        $arrData = array(
            "company_name" => trim($pData['company_name']),
            "company_remark" => trim($pData['company_remark']),
            "domain_info" => trim($pData['domain_info']),
            "report_state" => $pData['report_state'],
            "domain_name" => trim($pData['domain_name']),
            "mail_brand" => trim($pData['mail_brand']),
            "person_mail" => trim($pData['person_mail']),
            "company_address" => trim($pData['company_address']),
            "next_date" => trim($pData['next_date']),
            "phone" => trim($pData['phone']),
            "create_date" => NOW,
            "update_date" => NOW
        );
        return to_success($this->mysqlInsert("company_list", $arrData, 'single', true));
    }

    //编辑公司信息
    public function editCompany(){
        $pData = getData();
        //验证数据
        if(!$pData['company_name'] && strlen($pData['company_name']) < 1){
            return to_error('公司名称不能为空且至少1位。');
        }
        // if(!$pData['info_state'] && ($pData['info_state'] != -1 || $pData['info_state'] != 1)){
        //     return to_error('请选择信息状态');
        // }
        // if(!$pData['connect_state'] && ($pData['connect_state'] != -1 || $pData['connect_state'] != 1)){
        //     return to_error('请选择联系状态');
        // }
        //查看公司名是否存在
        $filter = " id = '{$pData['id']}' ";
        if($this->__getCompanyCount(' AND '.$filter) === 0){
            return to_error('操作失败！该公司不存在。');
        }else if($this->__getCompanyCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法公司，存在多个该公司名公司');
        }
        $arrData = array(
            "company_name" => trim($pData['company_name']),
            "company_remark" => trim($pData['company_remark']),
            "domain_info" => trim($pData['domain_info']),
            "report_state" => $pData['report_state'],
            "domain_name" => trim($pData['domain_name']),
            "mail_brand" => trim($pData['mail_brand']),
            "person_mail" => trim($pData['person_mail']),
            "company_address" => trim($pData['company_address']),
            "next_date" => trim($pData['next_date']),
            "phone" => trim($pData['phone']),
            "update_date" => NOW
        );
        $res = $this->mysqlEdit("company_list", $arrData, $filter);
        if(!$res){
            return to_error('修改失败');
        }

        //添加日志记录
        $filter = " company_id = '{$pData['id']}' ";
        $nowDate = date("Y-m-d",time());
        $filter .= " AND create_date like '%{$nowDate}%' ";
        $logNum = $this->__getCompanyConnectLogCount(' AND '.$filter);

        if($logNum === 0 && $pData['connect_state'] == 1){
            //添加一条记录
            $res = $this->__addConnectLog($pData['id'],$pData['remark'],$pData['connect_state']);
            if(!$res){
                return to_error('添加操作记录日志失败');
            }
        }else if($logNum === 1){
            //编辑当天记录
            $res = $this->__editConnectLog($pData['id'],$pData['remark'],$pData['connect_state']);
            if(!$res){
                return to_error('添加操作记录日志失败');
            }
        }else if($logNum > 1){
            return to_error('数据异常！请联系大猴子，存在多条记录');
        }   
        return to_success($res);
    }

    //获取公司联系日志
    public function getConnectLog(){
        $pData = getData();
        if(!$pData['company_id']){
            return to_error('不能获取公司id');
        }
        $sql = "SELECT * FROM company_connect_log WHERE company_id={$pData['company_id']} AND state=1 order by 1 desc";
        $res['items'] = $this->mysqlQuery($sql, "all");
        $res['sql'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //获得公司数量
    public function getCompanyCount($filter,$join=false,$filter2='',$filterTotal=''){
        return $this->__getCompanyCount($filter,$join=false,$filter2='',$filterTotal='');
    }

    //获取公司联系总数目
    public function getCompanyConnectLogCount($filter){
        return $this->__getCompanyConnectLogCount($filter);
    }

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //获取公司总数目
    private function __getCompanyCount($filter,$join=false,$filter2='',$filterTotal=''){
        $sql = "SELECT COUNT(*) total FROM company_list WHERE 1=1 {$filter}";
        if($join){
            $sql = "SELECT count(*) total
                FROM (SELECT * FROM company_list WHERE 1=1 {$filter} ) AS aa
                LEFT JOIN (SELECT * FROM company_connect_log WHERE 1=1 {$filter2} ) AS bb
                ON aa.id=bb.company_id WHERE 1=1 {$filterTotal} ";
        }
        
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

    //添加用户的联系日志
    private function __addConnectLog($companyId,$remark,$state){
        //登录用户入库
        $arrData = array(
            "company_id" => $companyId,
            "remark" => $remark,
            "state" => $state,
            "create_date" => NOW,
            "update_date" => NOW
        );
        return $this->mysqlInsert("company_connect_log", $arrData, 'single', true);
    }

    //编辑用户当天的联系日志
    private function __editConnectLog($companyId,$remark,$state){
        //登录用户入库
        $arrData = array(
            "remark" => $remark,
            "state" => $state,
            "update_date" => NOW
        );
        $nowDate = date("Y-m-d",time());
        $filter = " company_id = '{$companyId}' AND create_date like '%{$nowDate}%' ";
        return $this->mysqlEdit("company_connect_log", $arrData, $filter);
    }

    //获取公司联系总数目
    private function __getCompanyConnectLogCount($filter){
        $sql = "SELECT COUNT(*) total FROM company_connect_log WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

    //获取公司联系日志
    private function __getCompanyConnectLog($filter){
        $sql = "SELECT * FROM company_connect_log WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return $res;
    }

}