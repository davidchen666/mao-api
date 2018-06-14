<?php

class RoadShowModel extends AgentModel
{
    
    //获取路演列表
    public function getRoadShowList(){
        $pData = getData();
        $filter = '';
        //单条
        if($pData['roadid']){
             $filter .= " AND road_id='{$pData['roadid']}' ";
        }
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的
        $filter .= 'AND road_state <> -4 ';
        //搜索条件
        if($pData['status']){
            $filter .= " AND road_state='{$pData['status']}' ";
        }
        //road_id   road_name   road_title  road_second_title   road_intro  road_target road_guide  road_course road_signup_intro   road_achieve    road_remark create_date update_date road_state
        if($pData['searchVal']){
            $filter .= " AND (road_name like '%{$pData['searchVal']}%' OR road_title like '%{$pData['searchVal']}%' OR road_second_title like '%{$pData['searchVal']}%' OR road_intro like '%{$pData['searchVal']}%' OR road_target like '%{$pData['searchVal']}%' OR road_guide like '%{$pData['searchVal']}%' OR road_course like '%{$pData['searchVal']}%'OR road_signup_intro like '%{$pData['searchVal']}%'OR road_achieve like '%{$pData['searchVal']}%' OR road_remark like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getRoadShowCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT road_id,road_name,road_title,road_second_title,road_intro,road_target, road_guide,road_course,road_signup_intro,road_achieve,road_warn,road_remark,create_date,update_date,road_state,road_begin_date,road_end_date,road_target_rename,road_guide_rename,road_course_rename,road_signup_intro_rename,road_achieve_rename FROM events_road_show WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //添加路演 road_id,road_name,road_title,road_second_title,road_intro,road_target road_guide,road_course,road_signup_intro,road_achieve,road_remark,create_date,update_date,road_state
    public function addRoadShow(){
        $pData = getData();
        //验证数据
        if(!$pData['road_name']){
            return to_error('路演名称不能为空。');
        }
        $arrData = array(
            "road_name" => $pData['road_name'],
            "road_title" => $pData['road_title'],
            "road_second_title" => $pData['road_second_title'],
            "road_intro" => $pData['road_intro'],
            "road_target" => $pData['road_target'],
            "road_guide" => $pData['road_guide'],
            "road_course" => $pData['road_course'],
            "road_signup_intro" => $pData['road_signup_intro'],
            "road_achieve" => $pData['road_achieve'],
            "road_target_rename" => $pData['road_target_rename'],
            "road_guide_rename" => $pData['road_guide_rename'],
            "road_course_rename" => $pData['road_course_rename'],
            "road_signup_intro_rename" => $pData['road_signup_intro_rename'],
            "road_achieve_rename" => $pData['road_achieve_rename'],
            "road_begin_date" => $pData['road_begin_date'],
            "road_end_date" => $pData['road_end_date'],
            "road_warn" => $pData['road_warn'],
            "road_remark" => $pData['road_remark'],
            "road_state" => $pData['road_state'],
            "create_date" => NOW,
            "update_date" => NOW
        );
        return to_success($this->mysqlInsert("events_road_show", $arrData, 'single', true));
    }

    //编辑路演
    public function editRoadShow(){
        $pData = getData();
        //验证数据
        if(!$pData['road_id']){
            return to_error('操作失败,非法数据，不能获取ID。');
        }
        //查看路演是否存在
        $filter = " road_id='{$pData['road_id']}' ";
        if($this->__getRoadShowCount(' AND '.$filter) === 0){
            return to_error('操作失败！该路演项目不存在。');
        }else if($this->__getRoadShowCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个路演。');
        }
        $arrData = array(
            "road_name" => $pData['road_name'],
            "road_title" => $pData['road_title'],
            "road_second_title" => $pData['road_second_title'],
            "road_intro" => $pData['road_intro'],
            "road_target" => $pData['road_target'],
            "road_guide" => $pData['road_guide'],
            "road_course" => $pData['road_course'],
            "road_signup_intro" => $pData['road_signup_intro'],
            "road_achieve" => $pData['road_achieve'],
            "road_target_rename" => $pData['road_target_rename'],
            "road_guide_rename" => $pData['road_guide_rename'],
            "road_course_rename" => $pData['road_course_rename'],
            "road_signup_intro_rename" => $pData['road_signup_intro_rename'],
            "road_achieve_rename" => $pData['road_achieve_rename'],
            "road_begin_date" => $pData['road_begin_date'],
            "road_end_date" => $pData['road_end_date'],
            "road_warn" => $pData['road_warn'],
            "road_remark" => $pData['road_remark'],
            "road_state" => $pData['road_state'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_road_show", $arrData, $filter));
    }

    //更改路演状态
    public function editRoadShowState(){
        $pData = getData();
        $rightStateArr = array('1','-1','-4');
        //查看用户是否存在
        $filter = " road_id='{$pData['roadid']}' ";
        if($this->__getRoadShowCount(' AND '.$filter) === 0){
            return to_error('操作失败！该路演不存在。');
        }else if($this->__getRoadShowCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户。');
        }
        //检查用户状态值是否合法
        if(!in_array($pData['state'], $rightStateArr)){
            return to_error('操作失败！非法状态值。');
        }
        $arrData = array(
            "road_state" => $pData['state'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_road_show", $arrData, $filter));
    }

    //获取路演报名列表
    public function getRoadShowRegisterList(){
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
            $filter .= " AND (com_name like '%{$pData['searchVal']}%' OR user_name like '%{$pData['searchVal']}%' OR user_job like '%{$pData['searchVal']}%' OR user_mobile like '%{$pData['searchVal']}%' OR user_email like '%{$pData['searchVal']}%' OR file_name like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getRoadShowRegisterCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT id,com_name,user_name,user_job,user_mobile,user_email,file_name,events_id,remark,create_date,update_date FROM events_road_show_sign_up WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //获取路演总数目
    private function __getRoadShowCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_road_show WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

    //获取路演报名总数目
    private function __getRoadShowRegisterCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_road_show_sign_up WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

}