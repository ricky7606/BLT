//为 tips 提示框自定义 CSS,以下为默认
xcsoft.tipsCss = {
	height: '60px',
	fontSize: '16px'
};
//隐藏、显示速度 ，默认 fast
xcsoft.tipsHide=xcsoft.tipsShow=300;

var isContentOK=false, isReportTypeOK=false;
var E = window.wangEditor
var editor = new E('#editordiv')
editor.customConfig.menus = [
    'head',  // 标题
    'bold',  // 粗体
    'italic',  // 斜体
    'underline',  // 下划线
    'strikeThrough',  // 删除线
    'foreColor',  // 文字颜色
    'backColor',  // 背景颜色
    'link',  // 插入链接
    'list',  // 列表
    'justify',  // 对齐方式
    'quote',  // 引用
    'emoticon',  // 表情
    'image',  // 插入图片
    'video',  // 插入视频
    'table',  // 表格
    'undo',  // 撤销
    'redo'  // 重复
]
editor.customConfig.uploadImgServer = '/mobile/article/uploadPic'
editor.customConfig.uploadFileName = 'upFiles[]'
// 限制一次最多上传 5 张图片
editor.customConfig.uploadImgMaxLength = 5
// 将图片大小限制为 3M
editor.customConfig.uploadImgMaxSize = 5 * 1024 * 1024
editor.customConfig.debug = true
editor.customConfig.zIndex = 100
editor.create()

document.getElementById('submitbtn').addEventListener('click', function () {
	chkContent();
	if(isContentOK){
		document.getElementById("submitbtn").disabled = true;
		// 读取 html
		$('#content').val(editor.txt.html()); 
		$('#content_text').val(editor.txt.text());
		$.post('/mobile/article/saveReplyArticle', {content:jQuery.trim($('#content').val()),content_text:jQuery.trim($('#content_text').val()),articleid:jQuery.trim($('#articleid').val()),replyid:jQuery.trim($('#replyid').val())}, function(msg) {
			if(msg=='ok'){
				xcsoft.success('评论发布成功！',3000);
				if(jQuery.trim($('#reply_type').val())=='detail'){
					setTimeout("window.location.href='/index/articledetails?id="+jQuery.trim($('#articleid').val())+"'", 1000 ); //3秒后刷新
				}else{
					setTimeout("window.location.reload(true)", 1000 );
				}
				return true;
			}else{
				xcsoft.error(msg,3000);
				document.getElementById("submitbtn").disabled = false;
				return false;
			}
		});
	}else{
		document.getElementById("submitbtn").disabled = false;
		xcsoft.error('您输入的内容太少啦~',3000);
		return false;
	}
}, false)

document.getElementById('reportbtn').addEventListener('click', function () {
	chkReportType();
	if(isReportTypeOK){
		document.getElementById("reportbtn").disabled = true;
		$.post('/mobile/article/report', {articleid:jQuery.trim($('#articleid').val()),report_type:jQuery.trim($('#report_type').val()),article_type:jQuery.trim($('#article_type').val()),report_comment:jQuery.trim($('#report_comment').val())}, function(msg) {
			if(msg=='ok'){
				xcsoft.success('举报提交成功！',3000);
				setTimeout("window.location.reload(true)", 1000 ); //3秒后刷新
				return true;
			}else{
				xcsoft.error(msg,3000);
				document.getElementById("reportbtn").disabled = false;
				return false;
			}
		});
	}else{
		document.getElementById("reportbtn").disabled = false;
		xcsoft.error('请选择举报理由',3000);
		return false;
	}
}, false)

function addView(articleid){
		$.post('/mobile/article/addView', {articleid:articleid}, function(msg) {
			return true;
		});
}

function chkReportComment(){
	var tmpStr = $("#report_comment").val();
	tmpStr = cutstr(tmpStr, 200);
	$("#report_comment").val(tmpStr);
}
function chkReportType(){
	if($("#report_type").val()==""){
		isReportTypeOK = false;
	}else{
		isReportTypeOK = true;
	}
}

function chkContent(){
	var content = editor.txt.text();
	if(content.length > 10){
		isContentOK = true;
	}else{
		isContentOK = false;
	}
}

//js截取字符串，中英文都能用 
//如果给定的字符串大于指定长度，截取指定长度返回，否者返回源字符串。 
//字符串，长度 

/** 
* js截取字符串，中英文都能用 
* @param str：需要截取的字符串 
* @param len: 需要截取的长度 
*/
function cutstr(str, len) {
var str_length = 0;
var str_len = 0;
str_cut = new String();
str_len = str.length;
for (var i = 0; i < str_len; i++) {
  a = str.charAt(i);
  str_length++;
  if (escape(a).length > 4) {
	//中文字符的长度经编码之后大于4 
	str_length++;
  }
  str_cut = str_cut.concat(a);
  if (str_length >= len) {
	return str_cut;
  }
}
//如果给定字符串小于指定长度，则返回源字符串； 
if (str_length < len) {
  return str;
}
}

function viewAll(i){
	var text_box = document.getElementById('content_text_'+i);
	var html_box = document.getElementById('content_html_'+i);
	text_box.style.display = 'none';
	html_box.style.display = 'block';
}
function hideAll(i){
	var text_box = document.getElementById('content_text_'+i);
	var html_box = document.getElementById('content_html_'+i);
	text_box.style.display = 'block';
	html_box.style.display = 'none';
}

function follow(articleid,i){
	$.post('/mobile/article/follow', {articleid:articleid}, function(msg) {
		if(msg=='ok'){
			var span = document.getElementById('followSpan'+i);
			var icon = document.getElementById('followIcon'+i);
			var count = document.getElementById('followCount'+i);
			xcsoft.success('收藏成功！',2000);
			var btn = document.getElementById('followBtn'+i);
			btn.className = "btn btn-default button_white";
			btn.style = "margin-left:30px;";
			btn.innerHTML = "<i class=\"am-icon-star am-icon-sm\"></i> 已收藏";
			btn.disabled = "";
			btn.onclick = "";
			span.onclick = "";
			icon.style.color = "#09F";
			count.innerHTML = parseInt(count.innerHTML)+1;
			return true;
		}else{
			xcsoft.error(msg,3000);
			return false;
		}
	});
}

function qna_follow(qnaid,i){
	$.post('/mobile/qna/follow', {qnaid:qnaid}, function(msg) {
		if(msg=='ok'){
			var span = document.getElementById('followSpan'+i);
			var icon = document.getElementById('followIcon'+i);
			var count = document.getElementById('followCount'+i);
			xcsoft.success('收藏成功！',2000);
			var btn = document.getElementById('followBtn'+i);
			btn.className = "btn btn-default button_white";
			btn.style = "margin-left:30px;";
			btn.innerHTML = "<i class=\"am-icon-star am-icon-sm\"></i> 已收藏";
			btn.disabled = "";
			btn.onclick = "";
			span.onclick = "";
			icon.style.color = "#09F";
			count.innerHTML = parseInt(count.innerHTML)+1;
			return true;
		}else{
			xcsoft.error(msg,3000);
			return false;
		}
	});
}

function chkLike(articleid,i){
	$.post('/mobile/article/like', {articleid:articleid}, function(msg) {
		xcsoft.success(msg,2000);
		if(msg=='点赞成功'||msg=='取消点赞成功'){
			var span = document.getElementById('likeSpan'+i);
			var icon = document.getElementById('likeIcon'+i);
			var count = document.getElementById('likeCount'+i);
			if(msg=='点赞成功'){
				span.title = "取消点赞";
				icon.style.color = "#09F";
				count.innerHTML = parseInt(count.innerHTML)+1;
			}else{
				span.title = "点赞";
				icon.style.color = "#999";
				count.innerHTML = parseInt(count.innerHTML)-1;
			}
			return true;
		}else{
			return false;
		}
	});
}

function attUser(userid,i){
	$.post('/mobile/userarticles/attUser', {userid:userid}, function(msg) {
		if(msg=='ok'){
			xcsoft.success('关注成功！',2000);
			//setTimeout("window.location.reload(true)", 2000 ); //3秒后刷新
			var btn = document.getElementById('attBtn'+i);
			btn.className = "btn btn-default button_white";
			btn.style = "width:auto; margin-left:20px;";
			btn.innerHTML = "<i class=\"am-icon-user-plus am-icon-sm\"></i> 已关注";
			btn.disabled = "";
			btn.onclick = "";
			return true;
		}else{
			xcsoft.error(msg,3000);
			return false;
		}
	});
}

function attReplyUser(userid,i){
	$.post('/mobile/userarticles/attUser', {userid:userid}, function(msg) {
		if(msg=='ok'){
			xcsoft.success('关注成功！',2000);
			//setTimeout("window.location.reload(true)", 2000 ); //3秒后刷新
			var btn = document.getElementById('attReplyBtn'+i);
			btn.className = "btn btn-default button_white";
			btn.style = "width:auto; margin-left:20px;";
			btn.innerHTML = "<i class=\"am-icon-user-plus am-icon-sm\"></i> 已关注";
			btn.disabled = "";
			btn.onclick = "";
			return true;
		}else{
			xcsoft.error(msg,3000);
			return false;
		}
	});
}

function showAllTags(type){
	for(i=6;i<30;i++){
		if($("#"+type+"_tag"+i)){
			$("#"+type+"_tag"+i).css({"display":"inline"});
		}
	}
	$("#"+type+"_tag_more").css({"display":"none"});
	$("#"+type+"_tag_less").css({"display":"inline"});
}

function hideAllTags(type){
	for(i=6;i<30;i++){
		if($("#"+type+"_tag"+i)){
			$("#"+type+"_tag"+i).css({"display":"none"});
		}
	}
	$("#"+type+"_tag_more").css({"display":"inline"});
	$("#"+type+"_tag_less").css({"display":"none"});
}

function showContent(){
	$("#loading").css({"display":"none"});
}

setTimeout("showContent()", 1000 ); //3秒后跳转

var thisHref = window.location.href;
if(thisHref.indexOf("index") > 0 && thisHref.indexOf("index/") < 0){
	$("#index_header").attr("class","form-group header_title current_header");
}else if(thisHref.indexOf("index/qnalist") > 0){
	$("#qnalist_header").attr("class","form-group header_title current_header");
}else if(thisHref.indexOf("index/articlelist") > 0){
	$("#articlelist_header").attr("class","form-group header_title current_header");
}