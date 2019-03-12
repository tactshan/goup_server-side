<?php

namespace App\Admin\Controllers;

use App\Model\Wx_users;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp;

class WeixinController extends Controller
{
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('微信用户列表')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Wx_users);

        $grid->id('Id');
        $grid->uid('Uid');
        $grid->openid('Openid');
        $grid->add_time('Add time')->display(function ($add_time){
            return date('Y-m-d H:i:s',$add_time);
        });
        $grid->nickname('Nickname');
        $grid->sex('Sex');
        $grid->headimgurl('Headimgurl')->display(function ($url){
            return  '<img width:100px; src='.$url.'>';
        });
        $grid->subscribe_time('Subscribe time');
        $grid->status('Status');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Wx_users::findOrFail($id));

        $show->id('Id');
        $show->uid('Uid');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');
        $show->status('Status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Wx_users);

        $form->number('uid', 'Uid');
        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('nickname', 'Nickname');
        $form->text('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->text('subscribe_time', 'Subscribe time');
        $form->text('status', 'Status')->default('1');

        return $form;
    }

    /**
     * 微信群发视图View
     * @param Content $content
     * @return Content
     */
    public function wx_group_send_view(Content $content)
    {
        return $content
            ->header('微信群发助手')
            ->description('description')
            ->body(view('admin.weixin.group_send'));
    }

    /**
     * 群发送消息
     */
    public function wx_group_send(){
        $msg=$_POST['msg'];
        //获取access_token
        $access_token=$this->getAccessToken();
        //拼接url
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$access_token;
        //请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        //拼接数据
        $userInfo=Wx_users::all()->toArray();
        $openid=array_column($userInfo,'openid');
        $data=[
            'touser'=>$openid,
            "msgtype"=>"text",
            "text"=>["content"=>$msg],
        ];
        $res=$client->request('POST', $url, ['body' => json_encode($data,JSON_UNESCAPED_UNICODE)]);
        $res_arr=json_decode($res->getBody(),true);
        if($res_arr['errcode']==0&&$res_arr['errmsg']=='send job submission success'){
            echo '群发成功';
        }else{
            echo '群发失败！错误码'.$res_arr['errmsg'];
        }
    }



    /**
     * 微信后台自定义菜单管理视图
     * @param Content $content
     * @return Content
     */
    public function customMenu(Content $content)
    {
        return $content
            ->header('微信自定义菜单')
            ->description('设置菜单')
            ->body(view('admin.weixin.custom_menu'));
    }


    //月考


    /**
     * 接受微信推送事件
     */
    public function wxEvent()
    {
        echo '111';
    }


    /**
     * 用户列表展示
     */
    public function mon_user_list()
    {
        //查询数据库中的用户数据，获取openid，根据openid查询用户信息

    }
    /**
     * 获取微信access_token
     */
    public function getAccessToken()
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
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
//        echo $openid;exit;
        $access_token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $data = json_decode(file_get_contents($url),true);
        return $data;
//        echo '<pre>';print_r($data);echo '</pre>';die;
    }
}
