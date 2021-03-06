//为 tips 提示框自定义 CSS,以下为默认
xcsoft.tipsCss = {
	height: '60px',
	fontSize: '16px'
};
//隐藏、显示速度 ，默认 fast
xcsoft.tipsHide=xcsoft.tipsShow=300;

function getRandomTags(){
	$("#recommandTags").html("正在为您生成随机推荐，请稍候...<i class=\"am-icon-spinner am-icon-spin\"></i>");
	$.post('/index/usertags/getRandomTags', {}, function(msg) {
		if(msg==''){
			$("#recommandTags").html('没有推荐给您的标签数据了');
		}else{
			$("#recommandTags_text").val(msg);
			var tag_list = $("#recommandTags_text").val();
			var tmpStr = '';
			tmpArr = tag_list.split('$$$');
			tmpArr.forEach(function(value){
				if(value != ''){
					tmpArr2 = value.split('___');
					if(tmpArr2[2] == 'true'){
						tmpStr += " <a href=\"javascript:void(0);\" class='tag_parent' title=\"有下一级子标签\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tmpArr2[0]+"\" data-tag-id=\""+tmpArr2[1]+"\" data-type=\"tags\">";
						tmpStr += tmpArr2[0];
						tmpStr += "</a> ";
					}else{
						tmpStr += " <a href=\"javascript:void(0);\" class='tag' title=\"点击查看标签详情\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tmpArr2[0]+"\" data-tag-id=\""+tmpArr2[1]+"\" data-type=\"users\">";
						tmpStr += tmpArr2[0];
						tmpStr += "</a> ";
					}
				}
			});
			$("#recommandTags").html(tmpStr);
		}
	});
}
//获取第一批推荐标签
getRandomTags();

function chkSearchTag(e){
	var e = e || event;
	var currKey = e.keyCode || e.which || e.charCode;//支持IE,FireFox
	if (currKey == 13) {
		searchTags();
		return false;
	}
}

function addCustomTag(){
	if(jQuery.trim($('#search_tags').val())==''){
		xcsoft.error('标签不能为空！',2000);
		return false;
	}
	$.post('/index/tag/addCustomTag', {text:jQuery.trim($('#search_tags').val())}, function(msg) {
		if(msg == 'ok'){
			xcsoft.success('标签已添加至标签库中！您可以再次搜索',2000);
			/*
			tmpArr = msg.split("___");
			user_tagid = tmpArr[2];
			*/
			$("#no_tag").css('display','none');
			$("#no_tag_remove").css('display','none');
			//$("#user_tag_list").html($("#user_tag_list").html()+"<span class=\"tag_remove\" id=\"tag_"+user_tagid+"\" onclick=\"delTag('"+user_tagid+"');\">"+tmpArr[1]+"</span> ");
			$("#search_result").css("display", "none");
		} else{
			xcsoft.error(msg,2000);
		} 
	});
}


function searchTags(new_page){
	if(jQuery.trim($('#search_tags').val())==''){
		xcsoft.error('请输入您想查找的标签内容',2000);
		return false;
	}
	$("#tags_recommand").css('display','none');
	$("#root_tags").css('display','none');
	$("#tag_searched").css('display','block');
	$("#tag_searched").html('<img src="/static/images/loading.gif">');
	if(new_page<0 || new_page == ''){new_page=1;}
	$.post('/index/usertags/searchTags', {tag:jQuery.trim($('#search_tags').val()),page:new_page}, function(msg) {
		if(msg == ''){
			xcsoft.error('没有找到您想要的标签',2000);
			$("#tags_recommand").css('display','block');
			$("#root_tags").css('display','block');
			$("#tag_searched").css('display','none');
			$("#search_result").css('display','block');
		}else{
			tmpArr = msg.split('###');
			tmpArr2 = tmpArr[0].split('___');
			no_searched = tmpArr2[0];
			if(no_searched == '1'){
				$("#root_tags").css('display','none');
				$("#search_result").css('display','block');
				tmpStr = "<span class='header_title'><span class=\"am-icon-frown-o am-icon-sm\" style=\"color:#900;margin-right=10px;\"></span> 没有找到您想要的标签，但是您可以从我们的标签分类开始查找哦~<br /></span><span style='color:#090;'>(tips: 蓝色的标签表示在该标签下还有子标签哦)<br />";
			}else{
				$("#search_result").css('display','none');
				$("#root_tags").css('display','block');
				tmpStr = "<span  class='header_title'><span class=\"am-icon-smile-o am-icon-sm\" style=\"color:#06C;margin-right=\"10px;\"\"></span> 搜索结果：</span><span style='color:#090;'>(tips: 蓝色的标签表示在该标签下还有子标签哦)<br />";
			}
			total_page = tmpArr2[1];
			current_page = tmpArr2[2];
			tmpArr = tmpArr[1].split('$$$');
			tmpArr.forEach(function(value){
				if(value != ''){
					tmpArr2 = value.split('___');
					if(tmpArr2[2] == 'true'){
						tmpStr += " <a href=\"javascript:void(0);\" class='tag_parent' title=\"有下一级子标签\" data-reveal-id=\"tag_box\" data-animation=\"fade\" data-tag=\""+tmpArr2[0]+"\" data-tag-id=\""+tmpArr2[1]+"\" data-type=\"tags\">";
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
				tmpStr += "<span class=\"prevNextBtn\" onclick=\"searchTags("+(parseInt(current_page)-1)+");\">上一页</span>";
			}
			tmpStr += "<span>( "+current_page+"/"+total_page+" )</span>";
			if(parseInt(current_page) < parseInt(total_page)){
				tmpStr += "<span class=\"prevNextBtn\" onclick=\"searchTags("+(parseInt(current_page)+1)+");\">下一页</span>";
			}
			tmpStr += "</div>";
			$("#tag_searched").html(tmpStr);
		}
	});
}

function showNoLogin(){
	$("#notLogin").css('display','block');
}