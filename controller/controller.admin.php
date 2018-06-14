<?php
/**
 * Copyright © 大猩猩
 * SDK中间api类
 * Author 大猩猩
 * Create 18-02-22 14:34
 */
class AdminController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "Admin";

    function __construct()
    {
        $this->model = Model::instance(self::M);
    }

    function verifyLogin(){
        echo $this->model->verifyLogin();
    }

    //验证token
    //获取管理员信息
    function getAdminInfo(){
        echo $this->model->getAdminInfo();
    }

    //验证token
    //获取管理员列表
    function getAdminList(){
        echo $this->model->getAdminList();
    }

    //验证token
    //添加管理员
    function addAdmin(){
        echo $this->model->addAdmin();
    }
    
    //验证token
    //添加管理员
    function editAdmin(){
        echo $this->model->editAdmin();
    }
    
    //验证token
    //重置密码
    function editAdminPwd(){
        echo $this->model->editAdminPwd();
    }
    
    //验证token
    //删除管理员
    function delAdmin(){
        echo $this->model->delAdmin();
    }

}

?>