<?php
/**
 * Created by PhpStorm.
 * User: zhangxian
 * Date: 16/5/16
 * Time: 下午4:01
 */
namespace App\Repositories\InterfacesBag;
interface Administrator extends BaseInterface{
    public function login(array $info);
    public function register();
    public function update();
}