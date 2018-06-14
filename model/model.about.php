<?php

class AboutModel extends AgentModel
{
    
    //获取
    public function getPage(){
        $pData = getData();
        $filter = '';
        //单条
        if($pData['id']){
             $filter .= " AND id='{$pData['id']}' ";
        }
        //分页查询
        $sql = "SELECT * FROM page_config WHERE 1=1 {$filter}";
        // $res['sql'] = $sql;
        $res = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //编辑 id name    content state   pic remark  create_time update_time
    public function editPage(){
        $pData = getData();
        //验证数据
        if(!$pData['id']){
            return to_error('操作失败,非法数据，不能获取ID。');
        }
        $filter = " id='{$pData['id']}' ";
        $arrData = array(
            "content" => $pData['content'],
            "state" => $pData['state'],
            "pic" => $pData['pic'],
            "remark" => $pData['remark'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("page_config", $arrData, $filter));
    }



    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    

}