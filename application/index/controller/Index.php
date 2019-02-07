<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\TicketModel;

class Index extends Controller
{
    //场馆区域对应数组
    protected $areaArray = array(0 => "A", 1 => "B", 2 => "C", 3 => "D");

    //首页
    public function index()
    {

        $places = $this->seatInit();
        $places = $this->deleteSold($places);
        $seatNum = $this->countSeat($places);
        $user = session("user");
        $this->assign("user", $user);
        $this->assign("seatNum", $seatNum);
        return $this->fetch();
    }

    //购买票
    public function buy()
    {
        if (empty(session("user"))) {
            $this->error("您未登录", "user/login");
        }
        $num = intval(input("post.num"));
        if ($num > 0 && $num < 6) {
            $this->assign("num", $num);
        } else {
            $this->error("数量不正确");
        }
        $ticketModel = new TicketModel();
        $ticketModel->startTrans();
        $places = $this->seatInit();
        $places = $this->deleteSold($places);
        $seatNum = $this->countSeat($places);
        if ($seatNum == 0) {
            $this->error("票已售罄", 'index/index');
        }
        if ($seatNum < $num) {
            $this->error("余票不足", 'index/index');
        }
        $ticketArray = array();//展示票信息
        $ticketData = array();//存入数据库票信息
        for ($n = 0; $n < $num; $n++) {
            $randSeatArr = $this->randSeat($places);
            $ticketArray[$n]["area"] = $randSeatArr[0];
            $ticketArray[$n]["row"] = $randSeatArr[1];
            $ticketArray[$n]["seat"] = $randSeatArr[2];
            $ticketData[$n]["uid"] = session("user.id");
            $ticketData[$n]["area_id"] = $randSeatArr[0];
            $ticketData[$n]["row_number"] = $randSeatArr[1];
            $ticketData[$n]["seat_number"] = $randSeatArr[2];
            unset($places[$randSeatArr[0]][$randSeatArr[1]][$randSeatArr[2]]);//剔除已设置的位置
        }

        $rs = $ticketModel->saveAll($ticketData);//保存数据库
        if ($rs) {
            $ticketModel->commit();
        } else {
            $ticketModel->rollback();
        }
        $this->assign("area", $this->areaArray);//对应ABCD区数组
        $this->assign("ticket", $ticketArray);
        return $this->fetch();
    }

    //获取随机位置
    protected function randSeat($places)
    {
        $placeNulls = array();//空余的座位
        foreach ($places as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                foreach ($v2 as $k3 => $v3) {
                    $placeNulls[] = array($k1, $k2, $k3);
                }
            }
        }
        $key = array_rand($placeNulls, 1);
        return $placeNulls[$key];
    }

    //计算剩下位置数量
    protected function countSeat($places)
    {
        $num = 0;
        foreach ($places as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                foreach ($v2 as $k3 => $v3) {
                    $num++;
                }
            }
        }
        return $num;
    }

    //场馆所有位置初始化
    protected function seatInit()
    {
        //位置总和
        $places = array();
        //初始化座位
        for ($i = 0; $i < 4; $i++) {//abcd
            for ($j = 50; $j <= 100; $j += 2) {//每隔多两位
                for ($k = 0; $k < $j; $k++) {
                    $p = ($j - 50) / 2;//排位数
                    $places[$i][$p][$k] = null;//初始化座位
                }
            }
        }
        return $places;
    }

    //删除已卖出位置
    protected function deleteSold($places)
    {
        $ticketModel = new TicketModel();
        $list = $ticketModel->lock(true)->select();
        foreach ($list as $key => $value) {
            //如座位已卖出则剔除
            unset($places[$value['area_id']][$value['row_number']][$value['seat_number']]);
            //排卖完剔除
            if (count($places[$value['area_id']][$value['row_number']], 1) == 0) {
                unset($places[$value['area_id']][$value['row_number']]);
            }
            //区域卖完剔除
            if (count($places[$value['area_id']], 1) == 0) {
                unset($places[$value['area_id']]);
            }
        }
        return $places;
    }

}
