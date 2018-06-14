<?php
/**
 * Copyright © 大猩猩
 * speaker api
 * Author 大猩猩
 * Create 18-04-28 11:31
 */
class AboutController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "About";
    const S = "Service";

    function __construct()
    {
        $this->model = Model::instance(self::M);
        $this->service = Model::instance(self::S);
    }
    //获取
    function getPageData()
    {
        echo $this->model->getPage();
    }
    /*
    ###########################################
    ############## 后台管理接口 ################
    ###########################################
    */
    
    //获取
    function getPage()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getPage();
    }

    //修改
    function editPage()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editPage();
    }

    //上传图片
    public function uploadFile(){
        echo $this->service->uploadFile('about');
    }
}

?>