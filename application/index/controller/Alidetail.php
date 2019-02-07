<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 2018/12/14
 * Time: 14:12
 */

namespace app\index\controller;

use think\Controller;
use QL\QueryList;
use QL\Ext\PhantomJs;

class Alidetail extends Controller
{
    public function index()
    {
        set_time_limit(300);
        $url = input("post.url");
        if ($url) {
            //获取前五张主图大图
//            $url = "https://detail.1688.com/offer/584064443369.html?spm=a261y.7663282.autotrace-offerDetailContext1.12.7c2f3d60Z4jYSC&sk=consign";
//            $html = QueryList::get($url)->getHtml();
//
//            $html = mb_convert_encoding($html, 'utf-8', 'GBK');
//            $mainImg = QueryList::html($html)->find('.vertical-img>a>img')->attrs('src');
//            $title = QueryList::html($html)->find('.vertical-img>a>img')->attrs('alt');
//            $dirName = $title[0];
//            $mainDir = $dirName . "/" . "main/";
//            $detailDir = $dirName . "/" . "detail/";
//            if (is_dir($dirName) == false) {
//                mkdir($dirName);
//            }
//            if (is_dir($mainDir) == false) {
//                mkdir($mainDir);
//            }
//            if (is_dir($detailDir) == false) {
//                mkdir($detailDir);
//            }
//            $mainImgArr = $mainImg->all();
//            for ($i = 0; $i < 5; $i++) {
//                $formatMainImg[$i] = str_replace(".60x60", "", $mainImgArr[$i]);
//                file_put_contents("./" . $mainDir . $i . ".jpg", file_get_contents($formatMainImg[$i]));
//            }
//            echo "<pre>";
//            echo "主图获取完成<br />";
//            $formatMainImg = array();
//            $detailImg = QueryList::html($html)->find('.desc-lazyload-container>p>img')->attrs('src');
            $GLOBALS['finalUrl'] = $url;
            $ql = QueryList::getInstance();
            $ql->use(PhantomJs::class, 'D:/phantomjs/phantomjs.exe');
            $ql->use(PhantomJs::class, 'D:/phantomjs/phantomjs.exe', 'browser');
            $mainImgArr = $detailImg = array();
            $html = $ql->browser(function (\JonnyW\PhantomJs\Http\RequestInterface $r) {
                $r->setMethod('GET');
                $r->setHeaders(['referer' => 'https://purchase.1688.com/favorites/favorite_shop.htm?keywords=&tagName=&orderBy=&tab=&lb=&pageIndex=2']);
                $r->setUrl($GLOBALS['finalUrl']);//外部变量URL
                $r->setViewportSize(1000, 10000);//调整浏览器大小，确保无需滚动则加载内容（防止懒加载）
                $r->setDelay(5);//设置等待浏览器执行结果秒数，一般懒加载需要等待久一点才能把内容加载完毕，如无内容则加长秒数
                return $r;
            })->getHtml();
            echo $html;exit;
            //解析HTML
            $detailArr = $ql->find('#de-description-detail img')->map(function ($item) {
                $attr = 'src';
                print_r($item->$attr) ;
            });
//            var_dump($detailArr);exit;
//            foreach ($detailArr->all() as $k => $v) {
//                file_put_contents("./" . $detailDir . $k . ".jpg", file_get_contents($v));
//            }
            echo "详情图片获取完成<br />";
        }
        return $this->fetch();
    }

    public function detail()
    {
        $content = input("post.html");
        $ql = QueryList::getInstance();
        $detailImg = $ql->find('img')->attrs('src');
    }

    public function test()
    {
        $a = 1;
        $b = 2;
        $c = 3;
        $d = 4;
        $e = 5;
        $a = &$b;
        echo "$a,$b,$c,$d,$e<br>";
        $b = "100$a";
        echo "$a,$b,$c,$d,$e<br>";
        $c = $d = $a++;
        echo "$a,$b,$c,$d,$e<br>";
        $e = ($d >= $a) ? ($b += 2) : ($c -= 2);
        echo "$a,$b,$c,$d,$e<br>";
    }

}