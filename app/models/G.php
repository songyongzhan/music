<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/11/9
 * Time: 16:13
 * Email: songyongzhan@qianbao.com
 */

class GModel extends SourceDown {

  protected function _init() {
    parent::_init();
    $this->_host = 'http://g.yt99.com';
  }


  public function fetchBefore($url, $data) {
    $this->setRequestHeader([
      "accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
      "accept-encoding" => "gzip, deflate, br",
      "accept-language" => "zh-CN,zh;q=0.9",
      "cache-control" => "no-cache",
      "pragma" => "no-cache",
      "upgrade-insecure-requests" => "1",
      "user-agent" => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.81 Safari/537.36"
    ]);

    return $data;
  }

  protected function fetchFinish($data) {

    return $data;
  }

  public function getContent($url, $data = []) {
    $result = $this->send($url, $data);
    $ext = explode('.',basename($url))[1];
    return $this->save($result, static::SAVEPATH. DS . time() . rand(1000, 9999) . '.' . $ext);
  }


}
