<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/23
 * Time: 10:25
 * Email: songyongzhan@qianbao.com
 */

class ArticleController extends ApiBaseController {


  public function getListAction() {

    $pageSize = $this->_get('pagesize', PAGESIZE);
    $pageNum = $this->_get('pagenum', 1);
    $where = [];
    $order = '';
    return $this->articleService->getList($where, $pageSize, $pageNum, $order);
  }


  public function topAction() {
    $num = $this->_get('num', 3);
    return $this->articleService->getTop($num);
  }

  /**
   * 获取单条音乐
   * @param int $id GET
   * @return mixed
   */
  public function detailAction() {
    $id = $this->_get('id');
    return $this->articleService->getOne($id);
  }

  public function updateAction(){
    $id=$this->_post('id');
    return $this->articleService->updateClicks($id);
  }

}