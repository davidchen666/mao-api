<?php
/**
 * Copyright © 大猩猩
 * events api
 * Author 大猩猩
 * Create 18-02-22 14:34
 */
class HotelController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "Hotel";
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
    //获取酒店列表
    function getHotelList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getHotelList();
    }

    //添加酒店
    function addHotel()
    {
        //先验证用户的token值，后续加上
        echo $this->model->addHotel();
    }

    //修改酒店
    function editHotel()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editHotel();
    }

    //更改酒店状态
    function editHotelState()
    {
        //先验证用户的token值，后续加上
        echo $this->model->editHotelState();
    }

    //上传图片
    public function uploadFile(){
        echo $this->service->uploadFile('hotel');
    }
}

?>