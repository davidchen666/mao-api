<?php
/**
 * Copyright © 大猩猩
 * speaker api
 * Author 大猩猩
 * Create 18-05-06 11:31
 */
class SpeakerController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "Speaker";
    const S = "Service";

    function __construct()
    {
        $this->model = Model::instance(self::M);
        $this->service = Model::instance(self::S);
    }
    /*
    ###########################################
    ############## 后台管理接口 ################
    ###########################################
    */
    //获取演讲嘉宾列表
    function getSpeakerList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getSpeakerList();
    }

    //添加演讲嘉宾
    function addSpeaker()
    {
        //先验证用户的token值，后续加上
        echo $this->model->addSpeaker();
    }

    //修改演讲嘉宾
    function editSpeaker()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editSpeaker();
    }

    //更改演讲嘉宾状态
    function editSpeakerState()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editSpeakerState();
    }

    //上传图片
    public function uploadFile(){
        echo $this->service->uploadFile('speaker');
    }
}

?>