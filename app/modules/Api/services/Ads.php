<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: songyongzhan@qianbao.com
 */

class AdsService extends BaseService {


  public function getList($navId, $sum) {
    $where = [];
    $field = [];
    if ($sum > 0) {
      $data = $this->adsModel->getListPage($where, $field, 1, $sum, 'sortId asc');
    } else {
      $data = $this->adsModel->getList($where, $field, 'sortId asc');
    }
    return $data;
  }


}