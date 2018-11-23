<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/11/9
 * Time: 16:16
 * Email: songyongzhan@qianbao.com
 */

class SourceDownModel extends BaseModel {

  const SAVEPATH = APP_PATH . DS . 'data/source';

  /**
   * @var Redis;
   */
  static $redis;

  protected function _init() {
    parent::_init();
    if (!is_dir(static::SAVEPATH))
      mkdir(static::SAVEPATH, 0755, TRUE);

    $this->_host = '';
  }

  public function getRedis() {
    if (is_null(self::$redis)) {
      $redis = new Redis();
      if (!$redis)
        throw new Exceptions('Redis Create Error.');
      if (!$redis->connect('127.0.0.1', 6379))
        throw new Exceptions('Redis Connect Not Failure.');

      self::$redis = $redis;
    }
    return self::$redis;
  }

  public function rlen($key) {
    $redis = $this->getRedis();
    return $redis->lLen($key);
  }

  public function rpush($key, $data) {
    $redis = $this->getRedis();
    return $redis->rPush($key, serialize($data));
  }

  public function rpop($key) {
    $redis = $this->getRedis();
    return unserialize($redis->rPop($key));
  }

  public function rdel($key) {
    $redis = $this->getRedis();
    return $redis->del($key);
  }


  public function rexists($key) {
    $redis = $this->getRedis();
    return $redis->exists($key);
  }


  public function hadd($key, $val) {
    $redis = $this->getRedis();
    return $redis->sAdd($key, $val);
  }

  public function hexists($key, $val) {
    $redis = $this->getRedis();
    return $redis->sIsMember($key, $val);
  }

  public function _hlen($key) {
    $redis = $this->getRedis();
    return $redis->sCard($key);
  }


  public function getUrl($url) {
    return $this->send($url, []);
  }

  protected function save($file, $save) {
    $pattern = '#![\d]+#';
    if (preg_match($pattern, $save)) {
      $save = preg_replace($pattern, '', $save);
    }

    if (empty($file))
      return FALSE;

    if (file_put_contents($save, $file, LOCK_EX)) {
      return $save;
    }

  }

}