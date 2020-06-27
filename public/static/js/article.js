//为 tips 提示框自定义 CSS,以下为默认
xcsoft.tipsCss = {
	height: '60px',
	fontSize: '16px'
};
//隐藏、显示速度 ，默认 fast
xcsoft.tipsHide=xcsoft.tipsShow=300;

var isTitleOK=false;
var isTitleImgOK=false;
var isTagOK=false;

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
editor.customConfig.uploadImgServer = '/index/article/uploadPic'
editor.customConfig.uploadFileName = 'upFiles[]'
// 限制一次最多上传 5 张图片
editor.customConfig.uploadImgMaxLength = 5
// 将图片大小限制为 3M
editor.customConfig.uploadImgMaxSize = 3 * 1024 * 1024
editor.customConfig.debug = true
editor.customConfig.zIndex = 100
editor.create()

document.getElementById('submitbtn').addEventListener('click', function () {
	var tmpContent = editor.txt.html().trim();
	if(tmpContent.length < 20){
		alert('您输入的发布内容太少了，请再完善一些吧');
		return false;
	}
	if(tmpContent.length > 40000){
		xcsoft.error('您输入的内容太长了，请精简一些吧',3000);
		return false;
	}else{
		if(tagArr.length<=0){
			xcsoft.error('请选择和您的发布相关的标签',3000);
			return false;
		}else{
			isTagOK=true;
			$('#tag_list').val(tagArr);
		}
		if(isTitleOK && isTitleImgOK && isTagOK){
			document.getElementById("submitbtn").disabled = true;
			document.getElementById("submitbtn").innerHTML = '数据提交中，请稍后';
			// 读取 html
			$('#content').val(editor.txt.html().trim());
			$('#content_text').val(editor.txt.text().trim());
			$.post('/index/article/saveArticle', {title:jQuery.trim($('#title').val()),content:jQuery.trim($('#content').val()),content_text:jQuery.trim($('#content_text').val()),title_img:jQuery.trim($('#title_img').val()),tag_list:jQuery.trim($('#tag_list').val())}, function(msg) {
				if(msg=='ok'){
					xcsoft.success('发布成功！3秒后跳转',3000);
					setTimeout("window.location.href='/index'", 3000 ); //3秒后跳转
					return true;
				}else{
					xcsoft.error(msg,3000);
					document.getElementById("submitbtn").disabled = false;
					return false;
				}
			});
		}else{
			document.getElementById("submitbtn").disabled = false;
			xcsoft.error('请检查您输入的内容后重试',3000);
			return false;
		}
	}
}, false)

document.getElementById('updatebtn').addEventListener('click', function () {
	var tmpContent = editor.txt.html().trim();
	if(tmpContent.length < 20){
		alert('您输入的发布内容太少了，请再完善一些吧');
		return false;
	}
	if(tmpContent.length > 40000){
		xcsoft.error('您输入的内容太长了，请精简一些吧',3000);
		return false;
	}else{
		if(tagArr.length<=0){
			xcsoft.error('请选择和您的发布相关的标签',3000);
			return false;
		}else{
			isTagOK=true;
			$('#tag_list').val(tagArr);
		}
		if(!isTitleOK){
			chkTitle();
		}
		if(isTitleOK && isTagOK){
			document.getElementById("updatebtn").disabled = true;
			document.getElementById("updatebtn").innerHTML = '数据提交中，请稍后';
			// 读取 html
			$('#content').val(editor.txt.html().trim());
			$('#content_text').val(editor.txt.text().trim());
			$.post('/index/article/updateArticle', {articleid:jQuery.trim($('#articleid').val()),title:jQuery.trim($('#title').val()),content:jQuery.trim($('#content').val()),content_text:jQuery.trim($('#content_text').val()),title_img:jQuery.trim($('#title_img').val()),tag_list:jQuery.trim($('#tag_list').val())}, function(msg) {
				if(msg=='ok'){
					xcsoft.success('更新成功！3秒后跳转',3000);
					setTimeout("window.location.href='/index/userarticles'", 3000 ); //3秒后跳转
					return true;
				}else{
					xcsoft.error(msg,3000);
					document.getElementById("updatebtn").disabled = false;
					return false;
				}
			});
		}else{
			document.getElementById("updatebtn").disabled = false;
			xcsoft.error('请检查您输入的内容后重试',3000);
			return false;
		}
	}
}, false)

function yesNoImg(imgname,isok){
	var field = document.getElementById(imgname);
	if(isok == 'ok'){
		field.src = "/static/images/yes.png";
	}else{
		field.src = "/static/images/no.png";
	}
	field.style.display = "block";
}

var GetLength = function (str) {
///<summary>获得字符串实际长度，中文2，英文1</summary>
///<param name="str">要获得长度的字符串</param>
var realLength = 0, len = str.length, charCode = -1;
for (var i = 0; i < len; i++) {
  charCode = str.charCodeAt(i);
  if (charCode >= 0 && charCode <= 128) realLength += 1;
  else realLength += 2;
}
return realLength;
};

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
}$(function () { 
$("input[name=title]").bind('blur', function () {
	if(GetLength($(this).val()) < 20){
		yesNoImg('check_title','no');
		return;
	}else{
		if (GetLength($(this).val()) > 120) { 
			$(this).val(cutstr($(this).val(), 120)); 
		}
		isTitleOK = true;
		yesNoImg('check_title','ok');
		return; 
	}
}); 
}); 

function chkTitle(){
	if(GetLength($("#title").val()) < 20){
		yesNoImg('check_title','no');
		return;
	}else{
		if (GetLength($("#title").val()) > 120) { 
			$("#title").val(cutstr($("#title").val(), 120)); 
		}
		isTitleOK = true;
		yesNoImg('check_title','ok');
		return; 
	}
}

function setDetailMsgRow(rowID, type) {  
	var row = document.getElementById(rowID);  
	if (row != null) {  
		if (row.style.display == (document.all ? "block" : "table-row")) {  
			row.style.display = "none";  
		}  
		else {  
			row.style.display = (document.all ? "block" : "table-row");  
		}  
	}  
}  

function choosePic(){
	var tmpfile = document.getElementById('upFiles');
	tmpfile.click();
}

function chkPic() {  
	var upfile = document.getElementById('upFiles');
	if(upfile.value != ''){
		document.getElementById('upImage').disabled = "disabled";
		document.getElementById('upImage').innerHTML = "上传中，请稍后";
		var formData = new FormData($("#pictureForm")[0]);  
		$.ajax({  
			url: '/index/article/uploadTitlePic' ,  
			type: 'POST',  
			data: formData,  
			async: true,  
			cache: false,  
			contentType: false,  
			processData: false,
			dataType: "text",  
			success: function (msg) { 
				msg = msg.replace(/\\/g,"");
				msg = msg.replace(/\"/g,"");
				msg = String(msg);
				//$("#img").attr('src',msg);
				//$("#img").css('display','block');
				//$("#img_url").html(msg);
				$("#title_img").val(msg);
				if($("#title_image_preview")){
					$("#title_image_preview").attr('image-name',msg);
				}
				document.getElementById('upImage').disabled = false;
				document.getElementById('upImage').innerHTML = "<i class=\"am-icon-cloud-upload\"></i> 标题图片已上传</button>";
				isTitleImgOK = true;
				yesNoImg('check_title_img','ok');
				return true;
			},  
			error: function (msg) {  
				xcsoft.error('图片上传失败',3000);
				document.getElementById('upImage').disabled = false;
				document.getElementById('upImage').innerHTML = "<i class=\"am-icon-cloud-upload\"></i> 选择要上传的标题图片</button>";
				isTitleImgOK = false;
				yesNoImg('check_title_img','no');
				return false;
			}  
		});  
	}
}  
