(function($) {

/*---------------------------
 Defaults for Reveal
----------------------------*/
	 
/*---------------------------
 Listener for data-reveal-id attributes
----------------------------*/

	$('a[data-reveal-id]').live('click', function(e) {
		e.preventDefault();
		var modalLocation = $(this).attr('data-reveal-id');
		var tag = $(this).attr('data-tag');
		var tagId = $(this).attr('data-tag-id');
		var type = $(this).attr('data-type');
		var page = $(this).attr('data-page');
		$("#current_text").html("当前标签");
		if(type=='tags'){
			$("#current_tag").html("<a href=\"javascript:void(0);\" class='tag' title=\"点击查看标签详情\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tag+"\" data-tag-id=\""+tagId+"\" data-type=\"users\">"+tag+"</a>");
		}else if($("#userid").val() != ''){
			$("#current_tag").html("<span class='tag'>"+tag+"</span>"); 
		}else{
			$("#current_tag").html("<span class='tag'>"+tag+"</span> ");
		}
		if(type == 'tags'){
			$("#box_title").html('子标签列表');
			$("#tips").html('tips: 也可以点击该父标签查看标签详情');
		}else{
			$("#box_title").html('标签详情');
			$("#tips").html('');
		}
		$("#data_list").html("<img src='/static/images/loading.gif'>");
		$("#article_list").html('');
		
		if(type == 'tags'){
			//获取子标签
			$.post('/mobile/usertags/getMoreTags', {tagid:tagId,page:page}, function(msg) {
				if(msg == ''){
					xcsoft.error('没有更多的子标签',2000);
					$("#data_list").html("没有更多的子标签");
				}else{
					tmpStr = "";
					tmpArr = msg.split('###');
					tmpArr2 = tmpArr[0].split('___');
					total_page = tmpArr2[0];
					current_page = tmpArr2[1];
					if(current_page==''){current_page=1;}
					tmpArr = tmpArr[1].split('$$$');
					tmpArr.forEach(function(value){
						if(value != ''){
							tmpArr2 = value.split('___');
							if(tmpArr2[2] == 'true'){
								tmpStr += "<a href=\"javascript:void(0);\" class='tag_parent' title=\"有下一级子标签\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tmpArr2[0]+"\" data-tag-id=\""+tmpArr2[1]+"\" data-type=\"tags\">";
								tmpStr += tmpArr2[0];
								tmpStr += "</a> ";
							}else{
								tmpStr += " <a href=\"javascript:void(0);\" class='tag' title=\"点击查看标签详情\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tmpArr2[0]+"\" data-tag-id=\""+tmpArr2[1]+"\" data-type=\"users\">";
								tmpStr += tmpArr2[0];
								tmpStr += "</a> ";
							}
						}
					});
					tmpStr += "<div style=\"padding-top:20px;\">";
					if(parseInt(current_page)>1){
						tmpStr += "<a class=\"prevNextBtn\" href=\"javascript:void(0);\" class='tag_parent' title=\"上一页\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tag+"\" data-tag-id=\""+tagId+"\" data-type=\"tags\" data-page=\""+(parseInt(current_page)-1)+"\">上一页</a>";
					}
					tmpStr += "<span>( "+current_page+"/"+total_page+" )</span>";
					if(parseInt(current_page) < parseInt(total_page)){
						tmpStr += "<a class=\"prevNextBtn\" href=\"javascript:void(0);\" class='tag_parent' title=\"下一页\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tag+"\" data-tag-id=\""+tagId+"\" data-type=\"tags\" data-page=\""+(parseInt(current_page)+1)+"\">下一页</a>";
					}
					tmpStr += "</div>";
					$("#data_list").html(tmpStr);
				}
			});
		}else{
			//获取用户列表
			$.post('/mobile/usertags/getTagUsers', {tagid:tagId,page:page}, function(msg) {
				if(msg == ''){
					//xcsoft.error('暂时还没有用户添加该标签',2000);
					$("#data_list").html("暂时还没有用户添加该标签");
				}else{
					$("#data_list").html('');
					tmpStr = "<div style='width:100%; margin-left:50px; margin-right:30px; text-align:left; line-height:70px;'><div><i class=\"am-icon-users\"></i> 以下用户已经添加了该标签，您可以查看用户信息。</div>";
					tmpArr = msg.split('___');
					tmpArr2 = tmpArr[0].split('###');
					total_page = tmpArr2[0];
					current_page = tmpArr2[1];
					if(current_page==''){current_page=1;}
					tmpArr = tmpArr[1].split('$$$');
					tmpArr.forEach(function(value){
						if(value != ''){
							tmpArr2 = value.split('###');
							if(tmpArr2[2] != ''){
								tmpStr += "<div style=\"float:left;\"><img src=\""+tmpArr2[2]+"\" class=\"user_pic\" style=\"margin-right:20px;\"> ";
							}else{
								tmpStr += "<div style=\"float:left;\"><img src=\"/static/images/profile_pic.jpg\" class=\"user_pic\" style=\"margin-right:20px;\"> ";
							}
							tmpStr += "<a href=\"/index/userreplydetail?userid="+tmpArr2[0]+"\" title=\"点击查看该用户\" target=\"details\" style=\"margin-right:40px;\">"+tmpArr2[1]+"</a></div>";
						}
					});
					tmpStr += "<div style=\"clear:both;\"></div></div>";
					tmpStr += "<div style=\"padding-top:20px;\">";
					if(parseInt(current_page)>1){
						tmpStr += "<a class=\"prevNextBtn\" href=\"javascript:void(0);\" class='tag_parent' title=\"上一页\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tag+"\" data-tag-id=\""+tagId+"\" data-type=\"users\" data-page=\""+(parseInt(current_page)-1)+"\">上一页</a>";
					}
					tmpStr += "<span>( "+current_page+"/"+total_page+" )</span>";
					if(parseInt(current_page) < parseInt(total_page)){
						tmpStr += "<a class=\"prevNextBtn\" href=\"javascript:void(0);\" class='tag_parent' title=\"下一页\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tag+"\" data-tag-id=\""+tagId+"\" data-type=\"users\" data-page=\""+(parseInt(current_page)+1)+"\">下一页</a>";
					}
					tmpStr += "</div>";
					$("#data_list").html(tmpStr);
				}
			});
			//获取发布列表
			$.post('/mobile/articlelist/getArticleListByTagId', {tagid:tagId,page:page}, function(msg) {
				if(msg == ''){
					$("#article_list").html("暂时还没有发布使用该标签");
				}else{
					$("#article_list").html('');
					tmpStr = "<div style='width:100%; margin-left:50px; margin-right:30px; line-height:40px; text-align:left;'><div><i class=\"am-icon-file-text\"></i> 以下发布使用了该标签，您可以点击查看！</div>";
					tmpArr = msg.split('___');
					tmpArr2 = tmpArr[0].split('###');
					total_page = tmpArr2[0];
					current_page = tmpArr2[1];
					if(current_page==''){current_page=1;}
					tmpArr = tmpArr[1].split('$$$');
					tmpArr.forEach(function(value){
						if(value != ''){
							tmpArr2 = value.split('###');
							tmpStr += "<li><a href=\"/index/articledetails?id="+tmpArr2[0]+"\" title=\"点击查看该发布\" target=\"articles\">"+tmpArr2[1]+"</a></li>";
						}
					});
					tmpStr += "<div style=\"padding-top:20px; width:85%; text-align:center;\">";
					if(parseInt(current_page)>1){
						tmpStr += "<a class=\"prevNextBtn\" href=\"javascript:void(0);\" class='tag_parent' title=\"上一页\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tag+"\" data-tag-id=\""+tagId+"\" data-type=\"articles\" data-page=\""+(parseInt(current_page)-1)+"\">上一页</a>";
					}
					tmpStr += "<span>( "+current_page+"/"+total_page+" )</span>";
					if(parseInt(current_page) < parseInt(total_page)){
						tmpStr += "<a class=\"prevNextBtn\" href=\"javascript:void(0);\" class='tag_parent' title=\"下一页\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tag+"\" data-tag-id=\""+tagId+"\" data-type=\"articles\" data-page=\""+(parseInt(current_page)+1)+"\">下一页</a>";
					}
					tmpStr += "</div>";
					$("#article_list").html(tmpStr);
				}
			});
		}
		
		$('#'+modalLocation).reveal($(this).data());
		//$("html,body").css({overflow:"hidden"}); //禁用滚动条
	});

	$('a[user-reveal-id]').live('click', function(e) {
		//e.preventDefault();
		var modalLocation = $(this).attr('user-reveal-id');
		var user = $(this).attr('data-user');
		var userId = $(this).attr('data-user-id');
		var type = $(this).attr('data-type');
		if(type=='user_tags'){
			$("#box_title").html('用户标签报告');
			$("#current_text").html("当前用户");
			$("#current_tag").html("<span class=\"header_title_arctile\" style=\"cursor:pointer;\" onclick=\"window.location.href='/mobile/userreplydetail?userid="+userId+"';\">"+user+"</span> &nbsp;&nbsp;<button type=\"button\" name=\"qnabtn\" id=\"qnabtn\" class=\"btn btn-default button_blue\" style=\"height:35px;\" onclick=\"window.location.href='/mobile/qna?invite_user="+userId+"'\"><i class=\"am-icon-question-circle\"></i> 向TA提问</button>");
			$("#tips").html('tips: 也可以点击该标签查看标签使用详情');
			//清空发布信息
			$("#article_list").html('');
		}
		$("#data_list").html("<img src='/static/images/loading.gif'>");
		
		if(type == 'user_tags'){
			//获取标签列表
			$.post('/index/usertags/getTagsByUserId', {userid:userId}, function(msg) {
				if(msg == ''){
					xcsoft.error('该用户没有添加过标签',2000);
					$("#data_list").html("没有标签记录");
				}else{
					tmpStr = "";
					msg.forEach(function(value){
						tmpStr += " <a href=\"$('.close-reveal-modal').click();\" class='tag' title=\"点击查看标签详情\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+value['tag']+"\" data-tag-id=\""+value['tagid']+"\" data-type=\"users\">";
						tmpStr += value['tag'];
						tmpStr += "</a> ";
					});
					tmpStr += "<div style=\"padding-top:20px;\"></div>";
					$("#data_list").html(tmpStr);
				}
			});
		}
		
		$('#'+modalLocation).reveal($(this).data());
		//$("html,body").css({overflow:"hidden"}); //禁用滚动条
	});

	$('a[article-reveal-id]').live('click', function(e) {
		e.preventDefault();
		var modalLocation = $(this).attr('article-reveal-id');
		var articleTitle = $(this).attr('data-title');
		var articleId = $(this).attr('data-id');
		var replyId = $(this).attr('data-reply-id');
		$("#replyid").val($('#'+replyId).val());
		$("#articleid").val($('#'+articleId).val());
		$("#article_title").html($('#'+articleTitle).val());
		$('#'+modalLocation).reveal($(this).data());
		//$("html,body").css({overflow:"hidden"}); //禁用滚动条
	});

	$('a[report-reveal-id]').live('click', function(e) {
		e.preventDefault();
		var modalLocation = $(this).attr('report-reveal-id');
		var articleId = $(this).attr('data-article-id');
		var type = $(this).attr('data-type');
		$("#articleid").val($('#'+articleId).val());
		$("#article_type").val(type);
		
		$('#'+modalLocation).reveal($(this).data());
		//$("html,body").css({overflow:"hidden"}); //禁用滚动条
	});

/*---------------------------
 Extend and Execute
----------------------------*/

    $.fn.reveal = function(options) {
        
        
        var defaults = {  
	    	animation: 'fadeAndPop', //fade, fadeAndPop, none
		    animationspeed: 300, //how fast animtions are
		    closeonbackgroundclick: true, //if you click background will modal close?
		    dismissmodalclass: 'close-reveal-modal' //the class of a button or element that will close an open modal
    	}; 
    	
        //Extend dem' options
        var options = $.extend({}, defaults, options); 
	
        return this.each(function() {
        
/*---------------------------
 Global Variables
----------------------------*/
        	var modal = $(this),
        		topMeasure  = parseInt(modal.css('top')),
				topOffset = modal.height() + topMeasure,
          		locked = false,
				modalBG = $('.reveal-modal-bg');

/*---------------------------
 Create Modal BG
----------------------------*/
			if(modalBG.length == 0) {
				modalBG = $('<div class="reveal-modal-bg" />').insertAfter(modal);
			}		    
     
/*---------------------------
 Open & Close Animations
----------------------------*/
			//Entrance Animations
			modal.bind('reveal:open', function () {
			  modalBG.unbind('click.modalEvent');
				$('.' + options.dismissmodalclass).unbind('click.modalEvent');
				if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modal.css({'top': $(document).scrollTop()-topOffset, 'opacity' : 0, 'visibility' : 'visible'});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							"top": $(document).scrollTop()+topMeasure + 'px',
							"opacity" : 1
						}, options.animationspeed,unlockModal());					
					}
					if(options.animation == "fade") {
						//modal.css({'opacity' : 0, 'visibility' : 'visible', 'top': $(document).scrollTop()+topMeasure});
						modal.css({'opacity' : 0, 'visibility' : 'visible', 'top': $(document).scrollTop()+100});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							"opacity" : 1
						}, options.animationspeed,unlockModal());					
					} 
					if(options.animation == "none") {
						modal.css({'visibility' : 'visible', 'top':$(document).scrollTop()+topMeasure});
						modalBG.css({"display":"block"});	
						unlockModal()				
					}
				}
				modal.unbind('reveal:open');
			}); 	

			//Closing Animation
			modal.bind('reveal:close', function () {
			  if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"top":  $(document).scrollTop()-topOffset + 'px',
							"opacity" : 0
						}, options.animationspeed/2, function() {
							modal.css({'top':topMeasure, 'opacity' : 1, 'visibility' : 'hidden'});
							unlockModal();
						});					
					}  	
					if(options.animation == "fade") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"opacity" : 0
						}, options.animationspeed, function() {
							modal.css({'opacity' : 1, 'visibility' : 'hidden', 'top' : topMeasure});
							unlockModal();
						});					
					}  	
					if(options.animation == "none") {
						modal.css({'visibility' : 'hidden', 'top' : topMeasure});
						modalBG.css({'display' : 'none'});	
					}		
				}
				modal.unbind('reveal:close');
			});     
   	
/*---------------------------
 Open and add Closing Listeners
----------------------------*/
        	//Open Modal Immediately
    	modal.trigger('reveal:open')
			
			//Close Modal Listeners
			var closeButton = $('.' + options.dismissmodalclass).bind('click.modalEvent', function () {
			  modal.trigger('reveal:close');
			  //$("html,body").css({overflow:"auto"}); //启动滚动条
			});
			
			if(options.closeonbackgroundclick) {
				modalBG.css({"cursor":"pointer"})
				modalBG.bind('click.modalEvent', function () {
				  modal.trigger('reveal:close');
				  //$("html,body").css({overflow:"auto"}); //启动滚动条

				});
			}
			$('body').keyup(function(e) {
        		if(e.which===27){ modal.trigger('reveal:close'); 
				//$("html,body").css({overflow:"auto"});
				// 27 is the keycode for the Escape key
				}
			});
			
			
/*---------------------------
 Animations Locks
----------------------------*/
			function unlockModal() { 
				locked = false;
			}
			function lockModal() {
				locked = true;
			}	
			
        });//each call
    }//orbit plugin call
})(jQuery);
        
