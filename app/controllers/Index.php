<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */


use \Symfony\Component\DomCrawler\Crawler;

class IndexController extends BaseController {

  public $redisKey = 'musiclist';

  public $redisMusicIdsKey = 'musicIds';

  private $musicHost = 'https://ab.weitiexiu.com/';

  public function cliAction($user = '') {

    return 'index cli ' . implode(',', $user);
  }

  public function tTAction() {


    echo 111;

  }


  public function articleAction() {

    if ($this->sourceDownModel->rlen($this->redisKey) <= 0)
      return;


    $redisData = $this->sourceDownModel->rpop($this->redisKey);

    $url = $this->musicHost . trim($redisData['url'], '/');

    $thumb = '';
    if (isset($redisData['thumb']) && $redisData['thumb'] != '') {
      $thumbData = $this->download($redisData['thumb']);
      $thumb = $thumbData['httpurl'];
    }

    $result = $this->sourceDownModel->getUrl($url);

    //$result = file_get_contents(APP_PATH . DS . 'aa.html');
    $content = preg_replace("/<(\/?script.*?)>/si", "", $result); //过滤script标签


    $setCookie = $this->sourceDownModel->getResponseHeaders('set-cookie');
    $header = [
      'Cookie' => $setCookie
    ];

    $crawler = new Crawler();
    $crawler->addHtmlContent($content);

    $patternUrl = '#(/index.php?[^+\']+)#si';
    $patternId = '#cid-(\d+)-id-(\d+).html#';

    $crawler->filter('#oldlist li')->each(function (Crawler $node, $i) use ($patternUrl, $patternId) {

      $title = $node->filter('.moretitle')->text();
      $url = '';
      $articleId = 0;
      $catId = 0;

      if (preg_match_all($patternUrl, $node->filter('a')->attr('onclick'), $result)) {
        $url = isset($result[1][0]) ? $result[1][0] : '';
        if ($url && preg_match_all($patternId, $url, $idResult)) {
          $catId = $idResult[1][0];
          $articleId = $idResult[2][0];
        }
      }

      if (!$this->sourceDownModel->hexists($this->redisMusicIdsKey, $articleId)) {
        $obj = [
          'title' => $title,
          'url' => $url,
          'thumb' => '',
          'catId' => $catId,
          'articleId' => $articleId
        ];
        $this->sourceDownModel->rpush($this->redisKey, $obj);
        $this->sourceDownModel->hadd($this->redisMusicIdsKey, $articleId);
      }

    });


    $crawler->filter('.conttjtw')->each(function (Crawler $node, $i) use ($patternUrl, $patternId) {


      $title = $node->filter('.tjtw-rtitle51')->text();
      $url = '';
      $articleId = 0;
      $catId = 0;

      if (preg_match_all($patternUrl, $node->attr('onclick'), $result)) {
        $url = isset($result[1][0]) ? $result[1][0] : '';
        if ($url && preg_match_all($patternId, $url, $idResult)) {
          $catId = $idResult[1][0];
          $articleId = $idResult[2][0];
        }
      }

      if (!$this->sourceDownModel->hexists($this->redisMusicIdsKey, $articleId)) {
        $obj = [
          'title' => $title,
          'url' => $url,
          'thumb' => $node->filter('.tjtw-l img')->attr('data-src'),
          'catId' => $catId,
          'articleId' => $articleId
        ];
        $this->sourceDownModel->rpush($this->redisKey, $obj);
        $this->sourceDownModel->hadd($this->redisMusicIdsKey, $articleId);
      }

    });


    /*  <li class="newsmore">
                      <a href="javascript:void(0);" onclick="window.location.href='/index.php?show--cid-1-id-53106.html&k=zsvcIYx4eFfLgY1'+urlstr+s1+tojump;return false;">
                          <div class="olditem"><div class="moretitle">♬ 真正的朋友，在心里，联不联系，都不会忘记！</div></div>
                      </a>
                  </li>*//*  <li class="newsmore">
                    <a href="javascript:void(0);" onclick="window.location.href='/index.php?show--cid-1-id-53106.html&k=zsvcIYx4eFfLgY1'+urlstr+s1+tojump;return false;">
                        <div class="olditem"><div class="moretitle">♬ 真正的朋友，在心里，联不联系，都不会忘记！</div></div>
                    </a>
                </li>*/


    $date = $crawler->filter('#post-date')->text();

    $title = $crawler->filter('title')->text();

    $pageContent = $crawler->filter('#page-content')->html();

    $crawlerContent = new Crawler();
    $crawlerContent->addHtmlContent($pageContent);

    $imgServerPattern = '/\/\/([g|k|r]).yt99.com/si';
    $imgs = $crawlerContent->filter('img')->each(function (Crawler $node, $i) use ($imgServerPattern, $header) {
      $imgSrc = 'http:' . $node->attr('data-src');
      return $this->download($imgSrc, $header);
    });

    if ($imgs) {
      foreach ($imgs as $key => $val) {
        $imgs[$val['key']] = $val;
        unset($imgs[$key]);
      }
    }

    //获取文件中图片地址
    $pattern = '#<img.*data-src=["|\']+(.*)["|\']+.*\/?>#simU';
    if (preg_match($pattern, $pageContent)) {
      //$str=preg_replace($pattern, 'songimg',$str);
      $pageContent = preg_replace_callback($pattern, function ($input) use ($imgs) {
        if (array_key_exists(md5($input[1]), $imgs)) {
          return str_replace($input[1], $imgs[md5($input[1])]['httpurl'], $input[0]);
        }
        return $input[0];
      }, $pageContent);
    }

    //看看是否存在css中的图片，如果存在，也是需要进行替换的

    $cssUrlPattern = '#url\(["|\']+(.*)["|\']+\)#simU';

    if (preg_match($cssUrlPattern, $pageContent)) {
      preg_match_all($cssUrlPattern, $pageContent, $result);
      foreach ($result[1] as $key => $imgurl) {
        $result = $this->download($imgurl, $header);
        $pageContent = str_replace($imgurl, $result['httpurl'], $pageContent);
      }
    }

    $musicName = $crawler->filter('#songname blockquote h4')->text();
    $musicNamePattern = '#《([a-zA-Z0-9\x{4e00}-\x{9fa5}]+)》\s*-?\s*([a-zA-Z0-9\x{4e00}-\x{9fa5}]*)#u';

    if (preg_match($musicNamePattern, $musicName, $result)) {
      $musicName = $result[1] . (empty($result[2]) ? '' : ' - ' . $result[2]);
    }

    //下载音乐文件
    $musicPattern = '#gSound\s?=\s?["|\']+(.*)["|\']+#imU';
    $music = '';
    if (preg_match_all($musicPattern, $content, $result)) {

      if (isset($result[1][0])) {
        $result = $this->download($result[1][0], $header);
        $music = $result['httpurl'];
      }
    }

    //描述
    $descPattern = '#s_desc\s?=\s?["|\']+(.*)["|\']+#imU';
    $desc = '';
    if (preg_match_all($descPattern, $content, $result)) {
      if (isset($result[1][0])) {
        $desc = $result[1][0];
      }
    }

    $data = [
      'title' => $title,
      'decoration' => $desc,
      'body' => $pageContent,
      'nav' => isset($redisData['catId']) ? $redisData['catId'] : 0,
      'thumb' => $thumb,
      'clicks' => 1,
      'article_from' => '',
      'forwardurl' => $url,
      'manageId' => '1',
      'posttime' => strtotime($date),
      'titlekeyword' => $music, //放上音乐地址
      'htmlname' => $musicName, //音乐地址
      'sortId' => isset($redisData['articleId']) ? $redisData['articleId'] : 0 //原文id
    ];

    $result = $this->articleModel->insert($data);


    if ($result) {
      $this->sourceDownModel->getRedis()->sAdd('addSuccess', $result);
    }

    //fwrite(STDOUT, '正在采集文章Id：' . $redisData['articleId'] . ' 目前剩余:' . $this->sourceDownModel->rlen($this->redisKey));
  }


  public function detailAction() {

    $content = $this->sourceDownModel->getUrl('https://ab.weitiexiu.com/index.php?cate--cid-1.html');
    $content = preg_replace("/<(\/?script.*?)>/si", "", $content); //过滤script标签

    $crawler = new Crawler();
    $crawler->addHtmlContent($content);

    echo $crawler->filter('div#all section')->count();

    $patternUrl = '#(/index.php?[^+\']+)#si';
    $patternId = '#cid-(\d+)-id-(\d+).html#';

    $crawler->filter('div#all section')->each(function (Crawler $node, $i) use ($patternUrl, $patternId) {

      $title = count($node->filterXPath('//section/table/tbody/tr/td/span')) > 0 ? $node->filterXPath('//section/table/tbody/tr/td/span')->text() : '';
      $url = '';
      $articleId = 0;
      $catId = 0;

      if (preg_match_all($patternUrl, $node->attr('onclick'), $result)) {
        $url = isset($result[1][0]) ? $result[1][0] : '';
        if ($url && preg_match_all($patternId, $url, $idResult)) {
          $catId = $idResult[1][0];
          $articleId = $idResult[2][0];
        }
      }

      if (!empty($title)) {
        if (!$this->sourceDownModel->hexists($this->redisMusicIdsKey, $articleId)) {
          $obj = [
            'title' => $title,
            'url' => $url,
            'thumb' => $node->filterXPath('//section/table/tbody/tr/td/img')->first()->attr('src'),
            'catId' => $catId,
            'articleId' => $articleId
          ];
          $this->sourceDownModel->rpush($this->redisKey, $obj);
          $this->sourceDownModel->hadd($this->redisMusicIdsKey, $articleId);
        }
      }
    });

  }


  private function getNavIdAndArticleId($url) {
    $patternId = '#cid-(\d+)-id-(\d+).html#';
    $result = [];
    if ($url && preg_match_all($patternId, $url, $idResult)) {
      $result['nav'] = $idResult[1][0];
      $result['articleid'] = $idResult[2][0];
    }
    return $result;
  }

  private function download($imgSrc, $header = []) {
    static $count = 1;
    $imgServerPattern = '/\/\/([g|k|r]).yt99.com/si';
    $httpPattern = '#^[http|https]#';
    if (!preg_match($httpPattern, $imgSrc))
      $imgSrc = 'http://' . trim($imgSrc, '//');

    $result = '';

    if (preg_match_all($imgServerPattern, $imgSrc, $result)) {
      switch ($result[1][0]) {
        case 'k':
          $result = $this->kModel->getContent($imgSrc, [], $header);
          break;
        case 'g':
          $result = $this->gModel->getContent($imgSrc);
          break;

        case 'r':
          $result = $this->rModel->getContent($imgSrc);
          break;
      }

      if (!$result && $count <= 3) {
        $this->download($imgSrc, $header);
        $count++;
      }

    }


    return [
      'img' => $imgSrc,
      'key' => md5(str_replace('http:', '', $imgSrc)),
      'result' => $result,
      'basename' => basename($result),
      'httpurl' => 'http://172.28.66.194:8066/data/source/' . basename($result)
    ];
  }


}
