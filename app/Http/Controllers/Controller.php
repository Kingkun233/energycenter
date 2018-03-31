<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \Illuminate\Support\Facades\URL;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**数据整合返回
     * @param int $re
     * @param string $type
     * @param array $msg
     * @return mixed
     */
    function response_treatment($re = 0, $type = '', $msg = null)
    {
        if ($msg === null) {
            $msg = new \stdClass;
        }
        $res['re'] = $re;
        $res['type'] = $type;
        $res['msg'] = $msg;
        return $res;
    }

    /**登录接口预处理
     * @param $type
     * @param $post
     * @param int
     */
    function apiPreTreat($type, $post,$check_login=true)
    {
        if ($type != $post['type']) {
            $this->my_redirect('return', 2, $type);
        }
//        if($check_login){
//            if (!isset($_SESSION['username'])) {
//                $this->my_redirect('return', 3, $type);
//            }
//        }
    }

    /**自己的重定向
     * @param $route_name
     * @param $re
     * @param $type
     */
    function my_redirect($route_name, $re, $type)
    {
        header("Location:" . URL::route($route_name) . "?re=" . $re . "&type=" . $type);
        exit();
    }

    /**以数组形式返回对象数组的某个值
     * @param $objects
     * @param $value_name
     * @return array
     */
    function get_object_value_as_array($objects, $value_name)
    {
        $value_list = [];
        foreach ($objects as $object) {
            $value_list[] = $object->$value_name;
        }
        return $value_list;
    }
    /**
     * 一维结果数组content去html标签
     */
    function strip_tags_for_array($arr){
        foreach($arr as $k){
            $k->content=strip_tags($k->content);
        }
        return $arr;
    }
}
