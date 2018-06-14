<?php
/**
 * Copyright © 大猩猩
 * events api
 * Author 大猩猩
 * Create 18-02-22 14:34
 */
class EventsController extends Controller
{
    private $model;
    private $service;
    private $_api;
    private $title;
    const M = "Events";
    const S = "Service";

    function __construct()
    {
        $this->model = Model::instance(self::M);
        $this->service = Model::instance(self::S);
    }

    //获取最新的会议
    function getLastEvents(){
        echo $this->model->getLastEvents();
    }

    /**
     * 会议报名
     */
    function addMSignUp()
    {
        echo $this->model->addMSignUp();
    }

    //路演报名
    function addRSignUp()
    {
        echo $this->model->addRSignUp();
    }

    //通过会议id获取详情会议详情
    function getEventsInfoById()
    {
        echo $this->model->getEventsInfoById();
    }

    /*
    ###########################################
    ############## 后台管理接口 ################
    ###########################################
    */
    //获取会议列表
    function getEventsList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getEventsList();
    }

    //添加会议
    function addEvents()
    {
        //先验证用户的token值，后续加上
        echo $this->model->addEvents();
    }

    //编辑会议信息
    function editEvents()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editEvents();
    }

    //获取会议详情
    function getEventsInfo()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getEventsInfo();
    }

    //获取编辑会议详情
    function editEventsInfo()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editEventsInfo();
    }
    
    //获取会议菜单列表
    function getEventsMenuList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getEventsMenuList();
    }
    
    //获取会议报名列表
    function getEventsRegisterList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getEventsRegisterList();
    }

    //更改会议报名信息-> 报名费用，发票状态，付费渠道，备注信息
    function editEventsRegister()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editEventsRegister();
    }
    //上传图片
    public function uploadFile(){
        echo $this->service->uploadFile('events');
    }

    public function createFile(){
        echo $this->model->createFile();
    }
}

?>