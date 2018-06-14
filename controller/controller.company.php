<?php
/**
 * Copyright © 大猩猩
 * SDK中间api类
 * Author 大猩猩
 * Create 18-02-22 14:34
 */
class CompanyController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "Company";

    function __construct()
    {
        $this->model = Model::instance(self::M);
    }


    //验证token
    //获取管理员信息
    function getCompanyInfo(){
        echo $this->model->getCompanyInfo();
    }

    //验证token
    //获取管理员列表
    function getCompanyList(){
        echo $this->model->getCompanyList();
    }

    //验证token
    //添加管理员
    function addCompany(){
        echo $this->model->addCompany();
    }
    
    //验证token
    //添加管理员
    function editCompany(){
        echo $this->model->editCompany();
    }
    
    //验证token
    //重置密码
    function editCompanyPwd(){
        echo $this->model->editCompanyPwd();
    }
    
    //验证token
    //删除管理员
    function delCompany(){
        echo $this->model->delCompany();
    }

}

?>