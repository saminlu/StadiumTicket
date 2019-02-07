<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 2018/12/8
 * Time: 23:09
 */

namespace app\index\model;

use think\Model;

class TicketModel extends Model
{
    protected $table = 'ticket';
    protected $autoWriteTimestamp = true;
    protected $field = true;
}