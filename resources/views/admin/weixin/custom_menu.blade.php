<form action="">
   <div class="one_div">
       <h5>一级菜单</h5>
       类型：
       <select name="one_type" class="one_type">
           <option value="view">网页类型</option>
           <option value="click">点击类型</option>
           <option value="miniprogram">小程序类型</option>
       </select>
       名称：
       <input type="text">
       键名
       <input type="text">
       {{--<button ><a href="#" class="one_clone">克隆</a></button>--}}
       <input type="button" class="one_clone" value="克隆">
       <div class="two_div">
           <h5>二级菜单</h5>
           二级菜单类型：
           <select name="two_type" class="two_type">
               <option value="">---请选择---</option>
               <option value="view">网页类型</option>
               <option value="click">点击类型</option>
               <option value="miniprogram">小程序类型</option>
           </select>
           二级菜单名称：
           <input type="text">
           二级菜单键名
           <input type="text">
           <input type="button" class="two_clone" value="克隆">
       </div>
   </div>
</form>
<script src="{{URL::asset('/js/jquery-3.2.1.min.js')}}"></script>
<script>
    $(function () {
        //一级菜单克隆
        $(document).on('click','.one_clone',function () {
            var _this=$(this)
            var _num=$('.one_type').length
            if(_num>2){
                alert('以及菜单最多三个')
                return false
            }
            var _div=$('.one_div').parent().children().first().html();
            var _div="<div class='one_div'>"+_div+"</div>"
            _this.parent().last().after(_div)

        })
        //二级菜单克隆
        $(document).on('click','.two_clone',function () {
            var _this=$(this)
            var _num=_this.parent().siblings('div').length
            if(_num>=4){
                alert('二级菜单最多五个')
                return false
            }
            var two_div=_this.parent().html()
            var _div="<div class='two_div'>"+two_div+"</div>"
            _this.parent().after(_div)
        })
    })
</script>