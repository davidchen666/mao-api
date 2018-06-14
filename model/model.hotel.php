<?php

class HotelModel extends AgentModel
{
    
    //获取酒店列表
    public function getHotelList(){
        $pData = getData();
        $filter = '';
        //单条
        if($pData['hotelid']){
             $filter .= " AND hotel_id='{$pData['hotelid']}' ";
        }
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的
        $filter .= 'AND hotel_state <> -4 ';
        //搜索条件
        if($pData['status']){
            $filter .= " AND hotel_state='{$pData['status']}' ";
        }
        if($pData['searchVal']){
            $filter .= " AND (hotel_name like '%{$pData['searchVal']}%' OR hotel_info like '%{$pData['searchVal']}%' OR arrive_info like '%{$pData['searchVal']}%' OR hotel_remark like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getHotelCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT hotel_id, hotel_name, hotel_state, hotel_info, hotel_pic, arrive_info, arrive_pic, hotel_remark,hotel_pic_rename,hotel_arrive_rename, create_date, update_date FROM events_hotel WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //添加酒店
    public function addHotel(){
        $pData = getData();
        //验证数据
        if(!$pData['hotel_name']){
            return to_error('酒店名称不能为空。');
        }
        $arrData = array(
            "hotel_name" => $pData['hotel_name'],
            "hotel_state" => $pData['hotel_state'],
            "hotel_info" => $pData['hotel_info'],
            "hotel_pic" => json_encode($pData['hotel_pic']),
            "arrive_info" => $pData['arrive_info'],
            "arrive_pic" => $pData['arrive_pic'],
            "hotel_pic_rename" => $pData['hotel_pic_rename'],
            "hotel_arrive_rename" => $pData['hotel_arrive_rename'],
            "hotel_remark" => $pData['hotel_remark'],
            "create_date" => NOW,
            "update_date" => NOW
        );
        return to_success($this->mysqlInsert("events_hotel", $arrData, 'single', true));
    }

    //编辑酒店
    public function editHotel(){
        $pData = getData();
        //验证数据
        if(!$pData['hotel_id']){
            return to_error('操作失败,非法数据，不能获取酒店ID。');
        }
        //查看酒店是否存在
        $filter = " hotel_id='{$pData['hotel_id']}' ";
        if($this->__getHotelCount(' AND '.$filter) === 0){
            return to_error('操作失败！该酒店不存在。');
        }else if($this->__getHotelCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个酒店。');
        }
        $arrData = array(
            "hotel_name" => $pData['hotel_name'],
            "hotel_state" => $pData['hotel_state'],
            "hotel_info" => $pData['hotel_info'],
            "hotel_pic" => json_encode($pData['hotel_pic']),
            "arrive_pic" => $pData['arrive_pic'],
            "arrive_info" => $pData['arrive_info'],
            "hotel_pic_rename" => $pData['hotel_pic_rename'],
            "hotel_arrive_rename" => $pData['hotel_arrive_rename'],
            "hotel_remark" => $pData['hotel_remark'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_hotel", $arrData, $filter));
    }

    //更改酒店状态
    public function editHotelState(){
        $pData = getData();
        $rightStateArr = array('1','-1','-4');
        //查看用户是否存在
        $filter = " hotel_id='{$pData['hotelid']}' ";
        if($this->__getHotelCount(' AND '.$filter) === 0){
            return to_error('操作失败！该酒店不存在。');
        }else if($this->__getHotelCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户。');
        }
        //检查用户状态值是否合法
        if(!in_array($pData['state'], $rightStateArr)){
            return to_error('操作失败！非法状态值。');
        }
        $arrData = array(
            "hotel_state" => $pData['state'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_hotel", $arrData, $filter));
    }

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //获取酒店总数目
    private function __getHotelCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_hotel WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

}