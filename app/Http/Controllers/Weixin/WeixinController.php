<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp;

class WeixinController extends Controller
{
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    //接受微信推送事件
    public function wxEvent()
    {
        $data = file_get_contents("php://input");
        //处理xml字符串
        $xml_str=simplexml_load_string($data);
        //获取事件类型
        $event= $xml_str->Event;
        if($event=='subscribe'){
            //获取openid
            $openid=$xml_str->FromUserName;
            //获取扫描时间
            $sub_time=$xml_str->CreateTime;
            //根据openid获取用户信息
            $userInfo=$this->getUserInfo($openid);
            $key='h:wx_user_info';
            $user_data=Redis::hGetAll($key);
            //判断该用户是否存在redis中

                //保存用户信息到redis
                $user_data = [
                    'openid'            => $userInfo['openid'],
                    'add_time'          => time(),
                    'nickname'          => $userInfo['nickname'],
                    'sex'               => $userInfo['sex'],
                    'headimgurl'        => $userInfo['headimgurl'],
                    'subscribe_time'    => $sub_time
                ];
                $res=Redis::hmset($key,$user_data);
                if($res){
                    echo 'redis缓存成功';exit;
                }else{
                    echo '缓存失败';exit;
                }

    }
    }

    /**
     * 微信用户列表展示
     */
    public function wxUserList()
    {

        $key='h:wx_user_info';
        $user_data=Redis::hGetAll($key);
        $data=[
          'info'=>$user_data
        ];
        return view('index.weixin.userlist',$data);
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo($openid)
    {
        $access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $data = json_decode(file_get_contents($url),true);
        return $data;
    }

    /**
     * 获取微信AccessToken
     */
    public function getWXAccessToken()
    {
        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){
            $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET');
            $data=json_decode(file_get_contents($url),true);
            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;
    }

    /**
     * 给微信用户打标签View
     * @param $openid
     */
    public function userTag($openid)
    {
        $openid=[
          'openid'=>$openid
        ];
        $data=[
          'info'=>$openid
        ];
        return view('index.weixin.usertag',$data);
    }
    public function SendTag()
    {
        $access_token=$this->getWXAccessToken();
        $data=$_POST;
        if(!empty($data['new_user'])){
            $tag=100;
        }else if(!empty($data['old_user'])){
            $tag=101;
        }else if(!empty($data['Bj_user'])){
            $tag=102;
        }else if(!empty($data['Sh_user'])){
            $tag=103;
        }
        $url='https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.$access_token;
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            'openlist'=>[$data['openid']],
            'tagid'=>$tag
        ];
        $res=$client->request('POST', $url, ['body' => json_encode($data,JSON_UNESCAPED_UNICODE)]);
        $res_arr=json_decode($res->getBody(),true);
        var_dump($res_arr);exit;
//        if($res_arr['errcode']==0&&$res_arr['errmsg']='ok'){
//            echo '打标签成功';exit;
//        }
    }
    /**
     * 设置标签
     */
    public function setTag()
    {
        $access_token=$this->getWXAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.$access_token;
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            'tag'=>['name'=>'上海地区']
        ];
        $res=$client->request('POST', $url, ['body' => json_encode($data,JSON_UNESCAPED_UNICODE)]);
        $res_arr=json_decode($res->getBody(),true);
        var_dump($res_arr);exit;
    }

    /**
     * 获取标签列表
     */
    public function getTag()
    {
        $access_token=$this->getWXAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$access_token;
        $tagInfo=file_get_contents($url);
        var_dump($tagInfo);exit;
    }

    public function blackList($openid)
    {
        $access_token=$this->getWXAccessToken();
//        $url='https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?access_token='.$access_token;
        $url='https://api.weixin.qq.com/cgi-bin/tags/members/getblacklist?access_token='.$access_token;
        $data=[
          'openid_list'=>$openid
        ];
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $res=$client->request('POST', $url, ['body' => json_encode($data,JSON_UNESCAPED_UNICODE)]);
        $res_arr=json_decode($res->getBody(),true);
        var_dump($res_arr);exit;
    }
}
