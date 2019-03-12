<form action="/weixin/wx_user_tag" method="post">
    <h3>微信号(openid){{$info['openid']}}</h3>
    <input type="hidden" name="openid" value="{{$info['openid']}}">
    <input type="checkbox" checked name="new_user" value="新用户" id="">新用户
    <input type="checkbox" name="old_user" value="老用户" id="">老用户
    <input type="checkbox" name="Bj_user" value="北京地区" id="">北京地区
    <input type="checkbox" name="Sh_user" value="上海地区" id="">上海地区
    <input type="submit" value="Send">
</form>