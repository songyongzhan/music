<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 9:44
 * Email: songyongzhan@qianbao.com
 */

class AdsController extends ApiBaseController {

  public function indexAction() {

    return $this->UserService->index('james', '123', '2', 'ff');

  }


  public function getList() {

    $navId = $this->_get('navid');
    $sum = $this->_get('sum', 0);
    $data = $this->adsService->getList($navId, $sum);
    return $data;

  }


}