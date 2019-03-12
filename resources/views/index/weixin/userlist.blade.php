<h1><font>UID: Welcome back!</font></h1>
    <table border="1" class="table table-bordered">
        <tr>
            <td>微信号(openid)</td>
            <td>微信昵称</td>
            <td>性别</td>
            <td>头像</td>
            <td>添加时间</td>
            <td>操作</td>
        </tr>
        <tr>
            <td>{{$info['openid']}}</td>
            <td>{{$info['nickname']}}</td>
            @if($info['sex']==1)
                <td>男</td>
            @elseif($info['sex'] == 2)
                <td>女</td>
            @else
                <td>暂无填写</td>
            @endif
            <td><img src="{{$info['headimgurl']}}"></td>
            <td>{{date('Y/m/d H:i:s',$info['add_time'])}}</td>
            <td>
                <a href="/weixin/wx_user_tag/{{$info['openid']}}">打标签</a>
                <a href="/weixin/set_blank/{{$info['openid']}}">加入黑名单</a>
            </td>
        </tr>
    </table>
