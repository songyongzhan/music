<?php

/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 14:22
 * Email: songyongzhan@qianbao.com
 */
class ArticleModel extends BaseModel {


  public function updateClicks($id) {


    $sql = 'update ' . $this->prefix . 'article set clicks=clicks+1 where id =?';

    $result = $this->exec($sql, [$id]);

    if ($result) {
      return ['result' => 'success'];
    } else {
      return ['result' => 'failure'];
    }

  }

}