<?php
/**
 * Copyright © 大猩猩
 * speaker api
 * Author 大猩猩
 * Create 18-04-28 11:31
 */
class MediaController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "Media";
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
    //获取媒体列表
    function getMediaList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getMediaList();
    }

    //添加媒体
    function addMedia()
    {
        //先验证用户的token值，后续加上
        echo $this->model->addMedia();
    }

    //修改媒体
    function editMedia()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editMedia();
    }

    //更改媒体状态
    function editMediaState()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editMediaState();
    }

    //上传图片
    public function uploadFile(){
        echo $this->service->uploadFile('media');
    }
}

?>