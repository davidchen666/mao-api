<?php
/**
 * Copyright © 大猩猩
 * speaker api
 * Author 大猩猩
 * Create 18-05-06 11:31
 */
class SystemController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const C = "Company";
    const S = "Service";

    function __construct()
    {
        $this->company = Model::instance(self::C);
        $this->service = Model::instance(self::S);
    }
    /*
    ###########################################
    ########### linux系统服务接口 ##############
    ###########################################
    */
    
    //发送邮件
    public function sendMail(){
        //当前年月日
        $nowDate = date("Y-m-d",time());
        $nextDate = date("Y-m-d",strtotime("+1 day"));
        // echo $nextDate;
        //获取添加记录
        $addNum = $this->company->getCompanyCount(" AND create_date like '%{$nowDate}%' ");
        //今日联系公司
        $connectNum = $this->company->getCompanyConnectLogCount(" AND create_date like '%{$nowDate}%' AND state=1" );
        //明日联系公司
        $nextNum = $this->company->getCompanyCount(" AND next_date like '%{$nextDate}%' ");
        // echo $addNum.'<br>'. $connectNum .'<br>'. $nextNum;
        // echo $connectNum;
        // die();
        echo $this->service->sendMail($addNum,$connectNum,$nextNum);
    }
}

?>