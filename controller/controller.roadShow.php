<?php
/**
 * Copyright © 大猩猩
 * RoadShow api
 * Author 大猩猩
 * Create 18-04-28 11:31
 */
class RoadShowController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "RoadShow";

    function __construct()
    {
        $this->model = Model::instance(self::M);
    }
    /*
    ###########################################
    ############## 后台管理接口 ################
    ###########################################
    */
    //获取路演列表
    function getRoadShowList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getRoadShowList();
    }

    //添加路演
    function addRoadShow()
    {
        //先验证用户的token值，后续加上
        echo $this->model->addRoadShow();
    }

    //修改路演
    function editRoadShow()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editRoadShow();
    }

    //更改路演状态
    function editRoadShowState()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editRoadShowState();
    }

    //获取路演报名列表
    function getRoadShowRegisterList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getRoadShowRegisterList();
    }
  
}

?>