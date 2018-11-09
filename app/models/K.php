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
    $this->_host = 'http://k.yt99.com';
  }


  public function fetchBefore($url, $data) {
    $this->setRequestHeader([
      'Accept' => '*/*',
      'Accept-Encoding' => 'gzip, deflate, br',
      'Accept-Language' => 'zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2',
      'Cache-Control' => 'no-cache',
      'Connection' => 'keep-alive',
      //dede581e15416473174475151e24260ab39cf7b6aa640683df4bbd6621
      'Cookie' => 'acw_tc=dede581e154164731744751…60ab39cf7b6aa640683df4bbd6621',
      'Host' => 'k.yt99.com',
      'Pragma' => 'no-cache',
      'Referer' => 'https://ab.weitiexiu.com/index…l&k=8cmDeLQG5xs5HdI&s=2&date=1',
      'TE'=>'Trailers',
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
    return $this->save($result,static::SAVEPATH. DS . time() . rand(1000, 9999) . '.' . $ext);
  }


}