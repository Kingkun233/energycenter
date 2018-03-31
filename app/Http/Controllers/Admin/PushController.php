<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Illuminate\Support\Facades\DB;

class PushController extends Controller
{
    /**
     * 添加推送
     */
    public function addPush(Request $request)
    {
        $type = 'A2001';
        $post = $request->all();
        $this->apiPreTreat($type, $post);
        $add['title'] = $post['title'];
        $add['create_time'] = date("Y-m-d H:i:s");
        $add['update_time'] = date("Y-m-d H:i:s");
        $add['coverurl'] = $post['coverurl'] ? $post['coverurl'] : "";
        $add['pushtype'] = $post['pushtype'];
        $add['content'] = $post['content'];
        $add['author'] = $post['author'];
        if ($post['pushtype'] == 2) {
            $add['knowledgetype'] = $post['knowledgetype'];
        } else {
            $add['knowledgetype'] = 0;
        }
        $flag = DB::table('push')->insertGetId($add);
        if ($flag) {
            $msg['pushid'] = $flag;
            return $this->response_treatment(0, $type, $msg);
        } else {
            return $this->response_treatment(1, $type);
        }
    }

    /**
     * 修改推送
     */
    public function updatePush(Request $request)
    {
        $type = 'A2002';
        $post = $request->all();
        $this->apiPreTreat($type, $post);
        $update['title'] = $post['title'];
        $pushid = $post['pushid'];
        $update['update_time'] = date("Y-m-d H:i:s");
        $update['coverurl'] = $post['coverurl'];
        $update['content'] = $post['content'];
        $update['author'] = $post['author'];
        //如果pushtype是2，则更新knowledgetype
        $pushtype = DB::table('push')->where(['id' => $pushid])->value('pushtype');
        if ($pushtype == 2) {
            $update['knowledgetype'] = $post['knowledgetype'];
        }
        DB::table('push')->where(['id' => $pushid])->update($update);
        return $this->response_treatment(0, $type);

    }

    /**
     * 删除推送
     */
    public function deletePush(Request $request)
    {
        $type = 'A2003';
        $post = $request->all();
        $this->apiPreTreat($type, $post);
        $pushid = $post['pushid'];
        $flag = DB::table('push')->where(['id' => $pushid])->delete();
        if ($flag) {
            return $this->response_treatment(0, $type);
        } else {
            return $this->response_treatment(1, $type);
        }
    }

    /**
     * 搜索推送
     */
    public function searchPush(Request $request)
    {
        $type = 'A2004';
        $post = $request->all();
        $this->apiPreTreat($type, $post);
        $key = $post['key'];
        $pushes = DB::table('push')
            ->where('title', 'like', '%' . $key . '%')
            ->orwhere('content', 'like', '%' . $key . '%')
            ->orderBy('create_time', 'desc')
            ->get()
            ->toArray();
        $msg['pushes'] = $this->strip_tags_for_array($pushes);
        return $this->response_treatment(0, $type, $msg);
    }

    /**
     * 获取多个简单推送
     */
    public function showSimplePushes(Request $request)
    {
        $type = 'A2005';
        $post = $request->all();
        $this->apiPreTreat($type, $post);
        $pagestep = 16;
        $pushtype = $post['pushtype'];
        $headpage = $post['headpage'];
        $nextpage = $headpage + $pagestep;
        $pushes = DB::table('push')
            ->where(['pushtype' => $pushtype])
            ->offset($headpage)
            ->limit($pagestep)
            ->orderBy('create_time', 'desc')
            ->get()->toArray();
        $pushcount = DB::table('push')
            ->where(['pushtype' => $pushtype])
            ->count();
        $msg['pushes'] = $this->strip_tags_for_array($pushes);
        $msg['headpage'] = $nextpage;
        $msg['pushcount'] = $pushcount;
        return $this->response_treatment(0, $type, $msg);
    }

    /**
     * 获取详细推送
     */
    public function showDetailPush(Request $request)
    {
        $type = 'A2006';
        $post = $request->all();
        $this->apiPreTreat($type, $post);
        $pushid = $post['pushid'];
        $pushes = DB::table('push')->where(['id' => $pushid])->first();
        $msg['push'] = $pushes;
        if ($pushes) {
            return $this->response_treatment(0, $type, $msg);
        } else {
            return $this->response_treatment(1, $type);
        }
    }

    /**
     * 上传图片
     */
    public function uploadImageForUrl(Request $request)
    {
        $type = 'A2008';
        $file = $request->file('photo');
        $name = $request->input('name');
        $allowed_extensions = ["png", "jpg", "gif", "jpeg"];
        if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
            $this->response_treatment(6, $type);
        }
        $destinationPath = 'uploads/covers/'; //public 文件夹下面建 storage/uploads 文件夹
        $extension = $file->getClientOriginalExtension();
        $fileName = str_random(10) . '.' . $extension;
        $file->move($destinationPath, $fileName);
        $url = asset($destinationPath . $fileName);
        $msg['coverurl'] = $url;
        return $this->response_treatment(0, $type, $msg);
    }

    /**上传多张图片并返回url数组
     * @param Request $request
     * @return array
     */
    public function uploadImagesForUrls(Request $request)
    {
        $type = 'A2009';
        $file = $request->file('photo');
        $urls = [];
        $msg['errno']=0;
        foreach ($file as $key => $value) {
            // 判断图片上传中是否出错
            if (!$value->isValid()) {
                $msg['errno']=1;
                $msg['data']=[];
                return $msg;
            }
            if (!empty($value)) {//此处防止没有多文件上传的情况
                $allowed_extensions = ["png", "jpg", "gif", "jpeg"];
                if ($value->getClientOriginalExtension() && !in_array($value->getClientOriginalExtension(), $allowed_extensions)) {
                    $this->response_treatment(6, $type);
                }
                $destinationPath = 'uploads/imgs/' . date('Y-m-d') . '/'; // public文件夹下面uploads/xxxx-xx-xx 建文件夹
                $extension = $value->getClientOriginalExtension();   // 上传文件后缀
                $fileName = date('YmdHis') . mt_rand(100, 999) . '.' . $extension; // 重命名
                $value->move($destinationPath, $fileName); // 保存图片
                $url = asset($destinationPath . $fileName);
                $urls[] = $url;
            }
        }
        $msg['data']=$urls;
        return $msg;
    }
}
