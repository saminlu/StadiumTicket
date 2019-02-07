<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 2018/12/7
 * Time: 23:03
 */

namespace app\index\model;

use think\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $autoWriteTimestamp = true;
    protected $field = true;
}