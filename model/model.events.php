<?php
class EventsModel extends AgentModel
{
    //获取最新会议
    public function getLastEvents(){
        $sql = "SELECT events_id,events_name,events_begin_date,events_end_date,events_city,past_pic
                FROM `events_list` where events_state=1 ORDER BY events_begin_date DESC LIMIT 0, 2";
        $res = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //报名
    public function addMSignUp(){
    	$pData = getData();
    	if(!$pData || !$pData['uData']){
    		return to_error('缺少参数');
    	}
        if(!$pData['events_id']){
            return to_error('不能获取到会议id');
        }
    	$nowTime  = NOW;
    	//添加公司信息
    	$arrData = array(
            "com_name" => "{$pData['com_name']}",
            "com_Invoices_title" => "{$pData['com_Invoices_title']}",
            "com_duty_num" => "{$pData['com_duty_num']}",
            "com_phone" => "{$pData['com_phone']}",
            "com_fax" => "{$pData['com_fax']}",
            "com_postal_addr" => "{$pData['com_postal_addr']}",
            "com_postal_code" => "{$pData['com_postal_code']}",
            "com_field" => "{$pData['com_field']}",
            "com_from" => "{$pData['from']}",
            "events_id" => "{$pData['events_id']}",
            "update_date" => "{$nowTime}",
            "create_date" => "{$nowTime}"
        );
        $cid = $this->mysqlInsert("events_com_sign_up", $arrData, 'single', true);
        $res['cid'] = $cid;
        //添加用户信息
        foreach ($pData['uData'] as $k => $v) {
        	$arrUser = array(
        		'com_id'=>$cid,
        		'user_name'=>$v['uname'],
        		'user_job'=>$v['ujob'],
        		'user_mobile'=>$v['umobile'],
        		'user_email'=>$v['uemail'],
        		"update_date" => "{$nowTime}",
                "create_date" => "{$nowTime}"
        	);
        	$uid = $this->mysqlInsert("events_user_sign_up", $arrUser, 'single', true);
        	$res['user'][] = $uid;
        }
        return to_success($res);
    }

    //路演报名
    public function addRSignUp(){
    	$pData = getData();
    	if(!$pData){
    		return to_error('缺少参数');
    	}
        if(!$pData['events_id']){
            return to_error('不能获取到会议id');
        }
    	$nowTime  = NOW;
    	//添加公司信息
    	$arrData = array(
            "com_name" => "{$pData['cname']}",
            "user_name" => "{$pData['uname']}",
            "user_job" => "{$pData['ujob']}",
            "user_email" => "{$pData['uemail']}",
            "user_mobile" => "{$pData['umobile']}",
            "file_name" => "{$pData['fname']}",
            "events_id" => "{$pData['events_id']}",
            "update_date" => "{$nowTime}",
            "create_date" => "{$nowTime}"
        );
        $cid = $this->mysqlInsert("events_road_show_sign_up", $arrData, 'single', true);
        $res['cid'] = $cid;
        return to_success($res);
    }

    //前台通过会议id获取会议详情
    public function getEventsInfoById(){
        $pData = getData();
        if(!$pData){
            return to_error('不能获取到会议id');
        }
        // $sql = "SELECT * FROM events_list WHERE 1=1 events_id=1";
        // // $res['sql'] = $sql;
        // $res['items'] = $this->mysqlQuery($sql, "all");
        $condition=array(
            'eventsid'=>$pData['events_id'],
            'events_id'=>$pData['events_id'],
            'eventStatus'=> '1',
        );
        $baseData = $this->getEventsList($condition);
        $infoData = $this->getEventsInfo($condition);
        // var_dump($baseData);
        // var_dump($infoData);
        $res = array();
        if($baseData){
            $res['baseData'] = $baseData[0];
            $res['infoData'] = $infoData[0];
            //酒店详情
            if($res['infoData']['events_hotel_id'] && $pData['hotel']){
                $res['hotelData'] = $this->getHotelInfoById($res['infoData']['events_hotel_id']);
            }
            //演讲嘉宾详情---会议首页
            if($res['infoData']['events_speaker_simple'] && $pData['speaker']){
                $res['speakerData']['events_speaker_simple'] = $this->getSpeakerInfoById($res['infoData']['events_speaker_simple']);
            }
            //演讲嘉宾详情---会议详情页
            if($res['infoData']['events_speaker'] && $pData['speaker']){
                $res['infoData']['events_speaker'] = json_decode($res['infoData']['events_speaker'],true);
                foreach ($res['infoData']['events_speaker'] as $k => $v) {
                    $res['speakerData']['events_speaker'][$k]['speaker_type'] = $v['speaker_type'];
                    $res['speakerData']['events_speaker'][$k]['speaker_data'] = $this->getSpeakerInfoById(json_encode($v['speaker_data']));
                }
            }

            // //演讲主讲嘉宾详情
            // if($res['infoData']['events_speaker_main'] && $pData['speaker']){
            //     $res['speakerData']['events_speaker_main'] = $this->getSpeakerInfoById($res['infoData']['events_speaker_main']);
            // }
            // //演讲邀请嘉宾详情
            // if($res['infoData']['events_speaker_invite'] && $pData['speaker']){
            //     $res['speakerData']['events_speaker_invite'] = $this->getSpeakerInfoById($res['infoData']['events_speaker_invite']);
            // }
            //路演项目详情
            if($res['infoData']['events_road_id'] && $pData['roadShow']){
                $res['roadShowData'] = $this->getRoadShowInfoById($res['infoData']['events_road_id']);
            }
            //组织详情——主办方
            if($res['infoData']['events_organizer_organizer'] && $pData['organizer']){
                $res['organizerData']['events_organizer_organizer'] = $this->getOrganizerInfoById($res['infoData']['events_organizer_organizer']);
            }
            //组织详情——协办方
            if($res['infoData']['events_organizer_co_organizer'] && $pData['organizer']){
                $res['organizerData']['events_organizer_co_organizer'] = $this->getOrganizerInfoById($res['infoData']['events_organizer_co_organizer']);
            }
            //组织详情——战略伙伴
            if($res['infoData']['events_organizer_starategic_partner'] && $pData['organizer']){
                $res['organizerData']['events_organizer_starategic_partner'] = $this->getOrganizerInfoById($res['infoData']['events_organizer_starategic_partner']);
            }
            //组织详情——媒体支持
            if($res['infoData']['events_organizer_media_support'] && $pData['organizer']){
                $res['organizerData']['events_organizer_media_support'] = $this->getOrganizerInfoById($res['infoData']['events_organizer_media_support']);
            }
            //历届会议
            if($res['infoData']['events_past_events'] && $pData['review']){
                $res['reviewData'] = $this->getReviewInfoById($res['infoData']['events_past_events']);
            }

            return to_success($res);
        }else{
            return to_error('该会议已下线或者不存在');
        }
        
    }

    //获取酒店信息
    public function getHotelInfoById($id){
        $sql = "SELECT * FROM events_hotel WHERE hotel_id=".$id;
        $res = $this->mysqlQuery($sql, "all");
        return $res[0];
    }

    //获取演讲嘉宾信息
    public function getSpeakerInfoById($id){
        if(!is_array($id)){
            $id = implode(",", json_decode($id));
        }
        $sql = "SELECT * FROM events_speaker WHERE speaker_id in (".$id.") ORDER BY FIELD(speaker_id,".$id.")";
        $res = $this->mysqlQuery($sql, "all");
        return $res;
    }

    //获取路演项目信息
    public function getRoadShowInfoById($id){
        $sql = "SELECT * FROM events_road_show WHERE road_id=".$id;
        $res = $this->mysqlQuery($sql, "all");
        return $res[0];
    }

    //获取组织信息
    public function getOrganizerInfoById($id){
        if(!is_array($id)){
            $id = implode(",", json_decode($id));
        }
        $sql = "SELECT * FROM events_media WHERE media_id in (".$id.") ORDER BY FIELD(media_id,".$id.")";
        $res = $this->mysqlQuery($sql, "all");
        return $res;
    }

    //获取历届会议信息
    public function getReviewInfoById($id){
        if(!is_array($id)){
            $id = implode(",", json_decode($id));
        }
        $sql = "SELECT events_id,past_title,past_pic FROM events_list WHERE events_id in (".$id.") ORDER BY FIELD(events_id,".$id.")" ;
        $res = $this->mysqlQuery($sql, "all");
        return $res;
    }

    /*###############################################
      ################# admin #######################
    */###############################################

    //获取会议列表-admin
    public function getEventsList($data){
        $pData = $data ? $data : getData();
        $filter = '';
        //单条
        if($pData['eventsid']){
             $filter .= " AND events_id='{$pData['eventsid']}' ";
        }
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //搜索条件
        if($pData['eventStatus']){
        	$filter .= " AND events_state='{$pData['eventStatus']}' ";
        }
        if($pData['searchVal']){
        	$filter .= " AND (events_id like '%{$pData['searchVal']}%' OR events_name like '%{$pData['searchVal']}%' OR events_date like '%{$pData['searchVal']}%' OR events_city like '%{$pData['searchVal']}%' OR events_url like '%{$pData['searchVal']}%') ";
        }
        //总条数
        $res['page']['total'] = $this->__getEventsCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT * FROM events_list WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        if($data){
            return $res['items'];
        }
        return to_success($res);
    }

    //添加会议信息
    public function addEvents(){
        $pData = getData();
        //验证数据
        if(!$pData['events_name']){
            return to_error('会议标题不能为空。');
        }
        $arrData = array(
            "events_name" => $pData['events_name'],
            "events_begin_date" => $pData['events_begin_date'],
            "events_end_date" => $pData['events_end_date'],
            "events_city" => $pData['events_city'],
            "events_pic" => $pData['events_pic'],
            "past_pic" => $pData['past_pic'],
            "past_title" => $pData['past_title'],
            "events_menu" => json_encode($pData['events_menu']),
            "events_state" => $pData['events_state'],
            "events_remark" => $pData['events_remark'],
            "create_date" => NOW,
            "update_date" => NOW
        );
        $id = $this->mysqlInsert("events_list", $arrData, 'single', true);
        if($id){
            $id_info = $this->mysqlInsert("events_list_detail", array("events_id" => $id), 'single', true);
            return to_success(array('events_id'=>$id));
        }else {
            return to_error('添加失败');
        }
        
    }

    //编辑会议信息
    public function editEvents(){
        $pData = getData();
        //验证数据
        if(!$pData['events_id']){
            return to_error('操作失败,非法数据，不能获取ID。');
        }
        //查看会议id是否存在
        $filter = " events_id='{$pData['events_id']}' ";
        if($this->__getEventsCount(' AND '.$filter) === 0){
            return to_error('操作失败！该会议id不存在。');
        }else if($this->__getEventsCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个会议id');
        }
        if($pData['events_url'] && $pData['url']){
            $res_c = $this->createFile($pData['events_url'],$pData['url']);
            if(!$res_c){
                return to_error('操作失败！自定义url创建失败');
            }
        }
        $arrData = array(
            "events_name" => $pData['events_name'],
            "events_begin_date" => $pData['events_begin_date'],
            "events_end_date" => $pData['events_end_date'],
            "events_city" => $pData['events_city'],
            "events_pic" => $pData['events_pic'],
            "past_pic" => $pData['past_pic'],
            "past_title" => $pData['past_title'],
            "events_menu" => json_encode($pData['events_menu']),
            "events_state" => $pData['events_state'],
            "events_remark" => $pData['events_remark'],
            "events_url" => $pData['events_url'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_list", $arrData, $filter));
    }

    //获取会议详情-admin
    public function getEventsInfo($data){
        $pData = $data ? $data : getData();
        $filter = '';
        $query = '*';
        $query = $pData['query'] ? 'events_id,'.$pData['query'] : '*';
        //单条
        if($pData['events_id']){
             $filter .= " AND events_id='{$pData['events_id']}' ";
        }else{
            return '参数不正确！不能获取会议id。';
        }
        $sql = "SELECT {$query} FROM events_list_detail WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        if($data){
            return $res['items'];
        }
        return to_success($res);
    }

    //编辑会议详情-admin
    public function editEventsInfo(){
        $pData = getData();
        //验证数据
        if(!$pData['events_id']){
            return to_error('操作失败,非法数据，不能获取ID。');
        }
        //查看会议id是否存在
        $filter = " events_id='{$pData['events_id']}' ";
        if($this->__getEventsDetailCount(' AND '.$filter) === 0){
            return to_error('操作失败！该会议id不存在。');
        }else if($this->__getEventsDetailCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个会议id');
        }
        if($pData['query']){
            $arrData = array();
            $queryArr =  explode(",",$pData['query']);
            foreach ($queryArr as $k => $v) {
                $arrData[$v] = is_array($pData[$v]) ? json_encode($pData[$v]) : $pData[$v];
            }
            return to_success($this->mysqlEdit("events_list_detail", $arrData, $filter));
        }else{
            return to_error('不能获取query值');
        }
    }

    //获取会议菜单列表-admin
    public function getEventsMenuList(){
        $pData = getData();
        $sql = "SELECT * FROM events_menu_list";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //获取会议报名列表-admin
    public function getEventsRegisterList(){
        $pData = getData();
        $filter = '';
        //单条
        // if($pData['mediaid']){
        //      $filter .= " AND media_id='{$pData['mediaid']}' ";
        // }
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的
        // $filter .= 'AND media_state <> -4 ';
        //搜索条件
        if($pData['events_id']){
            $filter .= " AND aa.events_id='{$pData['events_id']}' ";
        }
        //com_id    events_id   com_name    com_Invoices_title  com_duty_num    com_phone   com_fax com_postal_addr com_postal_code com_field   com_from    create_date
        if($pData['searchVal']){
            $filter .= " AND (aa.com_id like '%{$pData['searchVal']}%' OR aa.events_id like '%{$pData['searchVal']}%' OR aa.com_name like '%{$pData['searchVal']}%' OR aa.com_Invoices_title like '%{$pData['searchVal']}%' OR aa.com_phone like '%{$pData['searchVal']}%' OR aa.com_fax like '%{$pData['searchVal']}%' OR aa.com_postal_addr like '%{$pData['searchVal']}%' OR aa.pay_method like '%{$pData['searchVal']}%' OR aa.remark like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getEventsRegisterCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT aa.com_id,aa.events_id,aa.com_name,aa.com_Invoices_title,aa.com_duty_num,aa.com_phone,aa.com_fax, aa.com_postal_addr,aa.com_postal_code,aa.com_field,aa.com_from,aa.create_date,aa.update_date, aa.pay_price,aa.pay_method,aa.invoice_state,aa.remark,
            bb.events_name,bb.past_title,bb.past_title
            FROM events_com_sign_up AS aa 
            LEFT JOIN events_list AS bb
            ON aa.events_id = bb.events_id
            WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        foreach ($res['items'] as $key => $value) {
             $res['items'][$key]['users']= $this->__getEventsRegisterUsers($value['com_id']);
        }
        return to_success($res);
    }

    //更改会议报名信息-> 报名费用，发票状态，付费渠道，备注信息
    public function editEventsRegister(){
        $pData = getData();
        $rightStateArr = array('1','-1');
        //查看报名是否存在
        $filter = " com_id='{$pData['com_id']}' ";
        if($this->__getEventsRegisterCount(' AND '.$filter) === 0){
            return to_error('操作失败！该报名ID不存在。');
        }else if($this->__getEventsRegisterCount(' AND '.$filter) > 1){
            return to_error('操作失败！数据有误。');
        }
        //检查用户状态值是否合法
        if(!in_array($pData['invoice_state'], $rightStateArr)){
            return to_error('操作失败！非法参数--发票状态。');
        }
        //判断金额是不是数字
        if($pData['pay_price'] && !is_numeric($pData['pay_price'])){
            return to_error('操作失败！付费价格参数不正确。');
        }
        $arrData = array(
            "pay_price" => $pData['pay_price'],
            "pay_method" => $pData['pay_method'],
            "invoice_state" => $pData['invoice_state'],
            "remark" => $pData['remark'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_com_sign_up", $arrData, $filter,''));
    }

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    private function __getEventsCount($filter){
    	$sql = "SELECT COUNT(*) total FROM events_list WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return $res[0]['total'];
    }

    private function __getEventsDetailCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_list_detail WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return $res[0]['total'];
    }
    
    private function __getEventsRegisterCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_com_sign_up WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return $res[0]['total'];
    }

    private function __getEventsRegisterUsers($com_id){
        $sql = "SELECT * FROM events_user_sign_up WHERE com_id='{$com_id}' ";
        return $this->mysqlQuery($sql, "all");
    }

    public function createFile($dirname,$url){
        // $dirname = 'ddd';
        // $url = 'dxx123.com';
        if(!$dirname || !$url){
            return false;
        }
        $dir = dirname(dirname(__FILE__));
        if(strstr($dir,"/chc-api")){
           $base_dir = str_replace("/chc-api","",$dir); 
        }
        if(strstr($dir,"\chc-api")){
           $base_dir = str_replace("\chc-api","",$dir); 
        }
        // $base_dir = str_replace("\chc-api","",$dir);
        //检查chc文件，查看是否存在events目录
        if(!file_exists($base_dir."/events")){
            $base_dir = $base_dir . "/chc";
        }
        $file_dir = $base_dir . "/events/".$dirname;
        // var_dump($dir,$base_dir,$file_dir);die();
        // var_dump($_SERVER);
        if(!file_exists($file_dir)) { 
            if(mkdir($file_dir,0777,true)) { 
                // echo "创建文件夹成功"; 
                $fp = fopen($file_dir."/index.php","w+"); 
                fwrite($fp,'<?php Header("Location: ' . $url . '"); exit;?>'); 
                fclose($fp); 
                // echo "文件写入成功"; 
                return true;
            }else{ 
                // echo "创建文件夹失败"; 
                return false;
            } 
        } else { 
            $fp = fopen($file_dir."/index.php","w+"); 
            fwrite($fp,'<?php Header("Location: ' . $url . '"); exit;?>'); 
            fclose($fp); 
            // echo "文件写入成功";
            return true;
        } 
        
    }

}