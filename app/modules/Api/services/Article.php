<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: songyongzhan@qianbao.com
 */

class ArticleService extends BaseService {


  /**
   * @param string $user <require>
   * @param string $password <require>
   * @param int $type <require|lt:25>
   * @param string $message <require>
   * @return mixed
   */
  /*public function index($user, $password, $type, $message) {


    $data = $this->UserModel->index([
      'user' => $user,
      'password' => $password,
      'type' => $type,
      'message' => $message,
    ]);

    return $this->show($data);

  }*/


  /**
   * 获取音乐列表
   * @param array $where
   * @param int $pageSize <require|integer> pagenum必须是数字
   * @param int $pageNum <require|integer> pagenum必须是数字
   */
  public function getList($where, $pageSize, $pageNum, $order) {
    $data = $this->articleModel->getListPage($where, ['id', 'title', 'thumb', 'htmlname as musicname', 'titlekeyword as musicsrc', 'clicks', 'posttime'], $pageNum, $pageSize, $order);

    return $this->show($data);
  }


  /**
   * 获取单条音乐
   * @param int $id <require|integer> id不能为空且必须是数字
   * @return array
   */
  public function getOne($id) {
    $detail = $this->articleModel->getOne($id, ['id', 'title', 'body', 'htmlname as musicname', 'titlekeyword as musicsrc', 'thumb', 'posttime', 'clicks']);

    //判断是否存在 关联文章，如果不存在，就随机取出 20条
    return $this->show($detail);
  }


  /**
   * 获取顶部轮播图
   * @param int $num <require|integer> num必须是数字
   * @return array
   */
  public function getTop($num) {
    $data=$this->articleModel->getListPage([], ['id', 'title', 'thumb', 'htmlname as musicname', 'titlekeyword as musicsrc', 'clicks', 'posttime'], 1, $num, 'clicks desc');
    return $this->show($data);
  }

  /**
   * 更新文章点击率
   * @param int $id <require|integer> id不能为空且必须是数字
   * @return array
   */
  public function updateClicks($id) {

    $data=$this->articleModel->updateClicks($id);
    return $this->show($data);
  }


}