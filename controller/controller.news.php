<?php
/**
 * Copyright © 大猩猩
 * News api
 * Author 大猩猩
 * Create 18-04-28 11:31
 */
class NewsController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "News";
    const S = "Service";

    function __construct()
    {
        $this->model = Model::instance(self::M);
        $this->service = Model::instance(self::S);
    }

    //获取新闻-----前台
    function getNewsData()
    {
        echo $this->model->getNewsList();
    }
    /*
    ###########################################
    ############## 后台管理接口 ################
    ###########################################
    */
    //获取列表
    function getNewsList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getNewsList();
    }

    //添加
    function addNews()
    {
        //先验证用户的token值，后续加上
        echo $this->model->addNews();
    }

    //修改
    function editNews()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editNews();
    }

    //更改状态
    function editNewsState()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editNewsState();
    }

    //上传图片
    public function uploadFile(){
        echo $this->service->uploadFile('news');
    }
}

?>