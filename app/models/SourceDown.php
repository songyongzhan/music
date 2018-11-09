<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/11/9
 * Time: 16:16
 * Email: songyongzhan@qianbao.com
 */

class SourceDownModel extends BaseModel {

  const SAVEPATH = APP_PATH . DS . 'source';

  protected function _init() {
    parent::_init();
    if (!is_dir(static::SAVEPATH))
      mkdir(static::SAVEPATH, 0755, TRUE);

    $this->_host = '';
  }

  public function getUrl($url) {
    return $this->send($url, []);
  }

  protected function save($file, $save) {
    return file_put_contents($save, $file, LOCK_EX);
  }

}