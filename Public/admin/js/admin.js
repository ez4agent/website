$(function() {
	//导航菜单
	$('.m-nav').find('li').hover(function() {
    	$(this).find('.m-pulldown').show();
  	},function() {
		$(this).find('.m-pulldown').hide();
  	});
	
	/*
	
	$('.m-nav').find('li').click(function(event) {
		function stopEvent(e) {
			e=e||event;
			if (e && e.stopPropagation) {
			  //W3C取消冒泡事件
			  e.stopPropagation();
			} else {
			  //IE取消冒泡事件
			  window.event.cancelBubble = true;
			}
		};
		stopEvent(event);
		var obj = $(this);
		var subNav = $(this).find('.m-pulldown');
		function hide(){
			obj.removeClass('nav-open');
			subNav.hide();
		}
		function hideAll(){
			$('.m-nav li .m-pulldown').hide();
			$('.m-nav li').removeClass('nav-open');
		}
		if(subNav.is(":hidden")) 
		{ 
			hideAll();
			obj.addClass('nav-open');
			subNav.show();
		}else{
			hide();
		}
		$(document).click(function() {
			hide(); 
		});
    });
	*/
	//移动端导航
	$('.u-nav-mobile').click(function() {
		var id = $(this).attr('target');
		console.log(id);
		if($(id).is(":hidden")) 
		{
			$('.u-menu-mobile').hide();
			$(id).show();
		}else{
			$(id).hide();
		
		}
    });
	
});
$(function() {
    $(window).bind("resize", function() {
		$('.m-nav .m-pulldown').hide();
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width > 768) {
			//重置菜单显示
            $('.u-menu-mobile').show();
        }
    })
})