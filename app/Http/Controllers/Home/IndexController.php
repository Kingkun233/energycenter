<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Illuminate\Support\Facades\DB;

class IndexController extends Controller
{

    /**
     * 获取首页信息
     */
    public function showHomePage(Request $request)
    {
        $type = 'C1001';
        $post = $request->all();
        $this->apiPreTreat($type, $post, false);
        $informations = DB::table('push')
            ->where('pushtype', '4')
            ->orderBy('create_time', 'desc')
            ->offset(0)
            ->limit(6)
            ->get()
            ->toArray();

        $knowledges['life'] = DB::table('push')
            ->where(['pushtype' => '2', 'knowledgetype' => '1'])
            ->orderBy('create_time', 'desc')
            ->offset(0)
            ->limit(3)
            ->get()
            ->toArray();
        $knowledges['life']=$this->strip_tags_for_array($knowledges['life']);

        $knowledges['common'] = DB::table('push')
            ->where(['pushtype' => '2', 'knowledgetype' => '2'])
            ->orderBy('create_time', 'desc')
            ->offset(0)
            ->limit(3)
            ->get()
            ->toArray();
        $knowledges['common']=$this->strip_tags_for_array($knowledges['common']);

        $knowledges['office'] = DB::table('push')
            ->where(['pushtype' => '2', 'knowledgetype' => '3'])
            ->orderBy('create_time', 'desc')
            ->offset(0)
            ->limit(3)
            ->get()
            ->toArray();
        $knowledges['office']=$this->strip_tags_for_array($knowledges['office']);

        $news = DB::table('push')
            ->where('pushtype', '1')
            ->orderBy('create_time', 'desc')
            ->offset(0)
            ->limit(6)
            ->get()
            ->toArray();

        $msg['informations'] = $this->strip_tags_for_array($informations);
        $msg['knowledges'] = $knowledges;
        $msg['news'] = $this->strip_tags_for_array($news);
        return $this->response_treatment(0, $type, $msg);
    }

    /**
     * 获取多个简单推送
     */
    public function showSimplePushes(Request $request)
    {
        $type = 'C1002';
        $post = $request->all();
        $this->apiPreTreat($type, $post, false);
        $pagestep = 6;
        $pushtype = $post['pushtype'];
        $headpage = $post['headpage'];
        $knowledgetype = $post['knowledgetype'];
        $nextpage = $headpage + $pagestep;
        if ($pushtype == 2) {
            if($knowledgetype!=0){
                $where = ['pushtype' => $pushtype, 'knowledgetype' => $knowledgetype];
            }else{
                $where = ['pushtype' => $pushtype];
            }

        } else {
            $where = ['pushtype' => $pushtype];
        }
        $pushes = DB::table('push')
            ->where($where)
            ->offset($headpage)
            ->limit($pagestep)
            ->orderBy('create_time', 'desc')
            ->get()->toArray();
        $pushcount = DB::table('push')
            ->where(['pushtype' => $pushtype])
            ->count();
        $msg['pushcount'] = $pushcount;
        $msg['pushes'] = $this->strip_tags_for_array($pushes);
        $msg['headpage'] = $nextpage;
        return $this->response_treatment(0, $type, $msg);
    }

    /**
     * 获取详细推送
     */
    public function showDetailPush(Request $request)
    {
        $type = 'C1003';
        $post = $request->all();
        $this->apiPreTreat($type, $post, false);
        $pushid = $post['pushid'];
        $pushes = DB::table('push')->where(['id' => $pushid])->first();
        $msg['push'] = $pushes;
        if ($pushes) {
            return $this->response_treatment(0, $type, $msg);
        } else {
            return $this->response_treatment(1, $type);
        }
    }

}
