<form action="/admin/admin/action/group_send" method="post">
    {{csrf_field()}}
    <textarea name="msg" id="" cols="80" rows="15"></textarea>
    <input type="submit" value="Send">
</form>