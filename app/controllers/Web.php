<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/11/14
 * Time: 11:08
 * Email: songyongzhan@qianbao.com
 */


class WebController extends BaseController{


  public function indexAction(){

    $this->_display('web/index.html');
  }


  public function listAction(){

    $this->_display('web/list.html');
  }



  public function detailAction(){

    $this->_display('web/detail.html');
  }

}