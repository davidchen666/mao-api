<?php

class MediaModel extends AgentModel
{
    //获取媒体列表
    public function getMediaList(){
        $pData = getData();
        $filter = '';
        //单条
        if($pData['mediaid']){
             $filter .= " AND media_id='{$pData['mediaid']}' ";
        }
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的
        $filter .= 'AND media_state <> -4 ';
        //搜索条件
        if($pData['status']){
            $filter .= " AND media_state='{$pData['status']}' ";
        }
        //media_id  media_name  media_company   media_company_simple    media_intro  media_remark    media_state create_date  update_date
        if($pData['searchVal']){
            $filter .= " AND (media_name like '%{$pData['searchVal']}%' OR media_company like '%{$pData['searchVal']}%' OR media_company_simple like '%{$pData['searchVal']}%' OR media_intro like '%{$pData['searchVal']}%' OR media_remark like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getMediaCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT media_id, media_name, media_company, media_company_simple, media_intro, media_remark, media_pic, media_state,media_url, create_date, update_date FROM events_media WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //添加媒体media_id, media_name, media_company, media_company_simple, media_intro, media_remark, media_pic, media_state,
    public function addMedia(){
        $pData = getData();
        //验证数据
        if(!$pData['media_name']){
            return to_error('媒体名称不能为空。');
        }
        $arrData = array(
            "media_name" => $pData['media_name'],
            "media_company" => $pData['media_company'],
            "media_company_simple" => $pData['media_company_simple'],
            "media_intro" => $pData['media_intro'],
            "media_remark" => $pData['media_remark'],
            "media_pic" => $pData['media_pic'],
            "media_state" => $pData['media_state'],
            "media_url" => $pData['media_url'],
            "create_date" => NOW,
            "update_date" => NOW
        );
        return to_success($this->mysqlInsert("events_media", $arrData, 'single', true));
    }

    //编辑媒体
    public function editMedia(){
        $pData = getData();
        //验证数据
        if(!$pData['media_id']){
            return to_error('操作失败,非法数据，不能获取ID。');
        }
        //查看媒体是否存在
        $filter = " media_id='{$pData['media_id']}' ";
        if($this->__getMediaCount(' AND '.$filter) === 0){
            return to_error('操作失败！该媒体不存在。');
        }else if($this->__getMediaCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个媒体。');
        }
        $arrData = array(
            "media_name" => $pData['media_name'],
            "media_company" => $pData['media_company'],
            "media_company_simple" => $pData['media_company_simple'],
            "media_intro" => $pData['media_intro'],
            "media_remark" => $pData['media_remark'],
            "media_pic" => $pData['media_pic'],
            "media_state" => $pData['media_state'],
            "media_url" => $pData['media_url'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_media", $arrData, $filter));
    }

    //更改媒体状态
    public function editMediaState(){
        $pData = getData();
        $rightStateArr = array('1','-1','-4');
        //查看用户是否存在
        $filter = " media_id='{$pData['mediaid']}' ";
        if($this->__getMediaCount(' AND '.$filter) === 0){
            return to_error('操作失败！该媒体不存在。');
        }else if($this->__getMediaCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户。');
        }
        //检查用户状态值是否合法
        if(!in_array($pData['state'], $rightStateArr)){
            return to_error('操作失败！非法状态值。');
        }
        $arrData = array(
            "media_state" => $pData['state'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_media", $arrData, $filter));
    }

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //获取媒体总数目
    private function __getMediaCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_media WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

}