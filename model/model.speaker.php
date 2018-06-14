<?php

class SpeakerModel extends AgentModel
{
    
    //获取演讲嘉宾列表
    public function getSpeakerList(){
        $pData = getData();
        $filter = '';
        //单条
        if($pData['speakerid']){
             $filter .= " AND speaker_id='{$pData['speakerid']}' ";
        }
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的
        $filter .= 'AND speaker_state <> -4 ';
        //搜索条件
        if($pData['status']){
            $filter .= " AND speaker_state='{$pData['status']}' ";
        }
        if($pData['searchVal']){
            $filter .= " AND (speaker_name like '%{$pData['searchVal']}%' OR speaker_honor like '%{$pData['searchVal']}%' OR speaker_identity like '%{$pData['searchVal']}%' OR speaker_simple_intro like '%{$pData['searchVal']}%' OR speaker_remark like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getSpeakerCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT speaker_id, speaker_name, speaker_pic, speaker_honor, speaker_identity, speaker_simple_intro, speaker_detail_intro, speaker_state, speaker_remark, create_date, update_date FROM events_speaker WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //添加演讲嘉宾 speaker_id, speaker_name, speaker_pic, speaker_honor, speaker_identity, speaker_simple_intro, speaker_detail_intro, speaker_state, speaker_remark, create_date, update_date`
    public function addSpeaker(){
        $pData = getData();
        //验证数据
        if(!$pData['speaker_name']){
            return to_error('嘉宾名称不能为空。');
        }
        $arrData = array(
            "speaker_name" => $pData['speaker_name'],
            "speaker_pic" => $pData['speaker_pic'],
            "speaker_honor" => $pData['speaker_honor'],
            "speaker_identity" => $pData['speaker_identity'],
            "speaker_simple_intro" => $pData['speaker_simple_intro'],
            "speaker_detail_intro" => $pData['speaker_detail_intro'],
            "speaker_state" => $pData['speaker_state'],
            "speaker_remark" => $pData['speaker_remark'],
            "create_date" => NOW,
            "update_date" => NOW
        );
        return to_success($this->mysqlInsert("events_speaker", $arrData, 'single', true));
    }

    //编辑演讲嘉宾
    public function editSpeaker(){
        $pData = getData();
        //验证数据
        if(!$pData['speaker_id']){
            return to_error('操作失败,非法数据，不能获取ID。');
        }
        //查看演讲嘉宾是否存在
        $filter = " speaker_id='{$pData['speaker_id']}' ";
        if($this->__getSpeakerCount(' AND '.$filter) === 0){
            return to_error('操作失败！该演讲嘉宾不存在。');
        }else if($this->__getSpeakerCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个演讲嘉宾。');
        }
        $arrData = array(
            "speaker_name" => $pData['speaker_name'],
            "speaker_pic" => $pData['speaker_pic'],
            "speaker_honor" => $pData['speaker_honor'],
            "speaker_identity" => $pData['speaker_identity'],
            "speaker_simple_intro" => $pData['speaker_simple_intro'],
            "speaker_detail_intro" => $pData['speaker_detail_intro'],
            "speaker_state" => $pData['speaker_state'],
            "speaker_remark" => $pData['speaker_remark'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_speaker", $arrData, $filter));
    }

    //更改演讲嘉宾状态
    public function editSpeakerState(){
        $pData = getData();
        $rightStateArr = array('1','-1','-4');
        //查看用户是否存在
        $filter = " speaker_id='{$pData['speakerid']}' ";
        if($this->__getSpeakerCount(' AND '.$filter) === 0){
            return to_error('操作失败！该演讲嘉宾不存在。');
        }else if($this->__getSpeakerCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户。');
        }
        //检查用户状态值是否合法
        if(!in_array($pData['state'], $rightStateArr)){
            return to_error('操作失败！非法状态值。');
        }
        $arrData = array(
            "speaker_state" => $pData['state'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_speaker", $arrData, $filter,''));
    }

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //获取演讲嘉宾总数目
    private function __getSpeakerCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_speaker WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

}