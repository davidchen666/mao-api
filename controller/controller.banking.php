<?php
/**
 * Copyright © 大猩猩
 * Banking api
 * Author 大猩猩
 * Create 18-04-28 11:31
 */
class BankingController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "Banking";

    function __construct()
    {
        $this->model = Model::instance(self::M);
    }

    //提交计划书-----前台
    function addBankingSignUp()
    {
        echo $this->model->addBankingSignUp();
    }
    /*
    ###########################################
    ############## 后台管理接口 ################
    ###########################################
    */
    //获取报名列表
    function getBankingRegisterList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getBankingRegisterList();
    }

}

?>