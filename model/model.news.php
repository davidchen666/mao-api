<?php

class NewsModel extends AgentModel
{
    
    //获取新闻列表
    public function getNewsList(){
        $pData = getData();
        $filter = '';
        //单条
        if($pData['newsid']){
             $filter .= " AND news_id='{$pData['newsid']}' ";
        }
        //新闻类型
        if($pData['newstype']){
             $filter .= " AND news_type='{$pData['newstype']}' ";
        }
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的
        $filter .= 'AND news_state <> -4 ';
        //搜索条件
        if($pData['status']){
            $filter .= " AND news_state='{$pData['status']}' ";
        }
        if($pData['searchVal']){
            $filter .= " AND (news_title like '%{$pData['searchVal']}%' OR news_content like '%{$pData['searchVal']}%' OR news_auther like '%{$pData['searchVal']}%' OR news_id like '%{$pData['searchVal']}%' OR news_remark like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getNewsCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT * FROM news_list WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //添加新闻 
    public function addNews(){
        $pData = getData();
        //验证数据
        if(!$pData['news_title']){
            return to_error('新闻标题不能为空。');
        }
        $arrData = array(
            "news_title" => $pData['news_title'],
            "news_content" => $pData['news_content'],
            "news_pic" => $pData['news_pic'],
            "news_auther" => $pData['news_auther'],
            "news_remark" => $pData['news_remark'],
            "news_state" => $pData['news_state'],
            "news_type" => $pData['news_type'],
            "create_date" => NOW,
            "update_date" => NOW
        );
        return to_success($this->mysqlInsert("news_list", $arrData, 'single', true));
    }

    //编辑新闻
    public function editNews(){
        $pData = getData();
        //验证数据
        if(!$pData['news_id']){
            return to_error('操作失败,非法数据，不能获取ID。');
        }
        //查看新闻是否存在
        $filter = " news_id='{$pData['news_id']}' ";
        if($this->__getNewsCount(' AND '.$filter) === 0){
            return to_error('操作失败！该新闻不存在。');
        }else if($this->__getNewsCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个新闻。');
        }
        $arrData = array(
            "news_title" => $pData['news_title'],
            "news_content" => $pData['news_content'],
            "news_pic" => $pData['news_pic'],
            "news_auther" => $pData['news_auther'],
            "news_remark" => $pData['news_remark'],
            "news_state" => $pData['news_state'],
            "news_type" => $pData['news_type'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("news_list", $arrData, $filter));
    }

    //更改新闻状态
    public function editNewsState(){
        $pData = getData();
        $rightStateArr = array('1','-1','-4');
        //查看用户是否存在
        $filter = " news_id='{$pData['newsid']}' ";
        if($this->__getNewsCount(' AND '.$filter) === 0){
            return to_error('操作失败！该新闻不存在。');
        }else if($this->__getNewsCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户。');
        }
        //检查用户状态值是否合法
        if(!in_array($pData['state'], $rightStateArr)){
            return to_error('操作失败！非法状态值。');
        }
        $arrData = array(
            "news_state" => $pData['state'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("news_list", $arrData, $filter,''));
    }

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //获取新闻总数目
    private function __getNewsCount($filter){
        $sql = "SELECT COUNT(*) total FROM news_list WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

}