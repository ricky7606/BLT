//为 tips 提示框自定义 CSS,以下为默认
xcsoft.tipsCss = {
	height: '60px',
	fontSize: '16px'
};
//隐藏、显示速度 ，默认 fast
xcsoft.tipsHide=xcsoft.tipsShow=300;

var isMobileOK=false, isPasswordOK=false, isPassword2OK=false, isImgcodeOK=false, isSmscodeOK=false;
function refreshVerify() {
	var ts = Date.parse(new Date())/1000;
	var img = document.getElementById('verify_img');
	img.src = "/captcha?id="+ts;
}
function chkCellphone(){
	var cellphone = document.getElementById('mobile').value;
	if(cellphone.length != 11){
		return false;
	}else{ 
		for(var i=0; i<cellphone.length; i++){
			if(cellphone.charAt(i)<'0' || cellphone.charAt(i)>'9'){
				return false;
			}
		}
	}
	return true;
}
function chkMobile(){
	var mobile = document.getElementById('mobile').value;
	if(mobile.length > 0){
		if(chkCellphone()){
			yesNoImg('check_mobile','ok');
			isMobileOK = true;
			return true;
		}else{
			xcsoft.error('手机号码错误',3000);
			yesNoImg('check_mobile','no');
			return false;
		}
	}else{
		isMobileOK = false;
	}
}
function chkMobileExist(){
	var mobile = document.getElementById('mobile').value;
	if(mobile.length > 0){
		if(chkCellphone()){
			$.post('/mobile/register/chkMobile', {mobile:jQuery.trim($('#mobile').val())}, function(msg) {
				if(msg=='exists'){
					yesNoImg('check_mobile','ok');
					isMobileOK = true;
					return true;
				}else{
					xcsoft.error('该手机号码并未注册',3000);
					yesNoImg('check_mobile','no');
					return false;
				}
			});		
		}else{
			xcsoft.error('手机号码错误',3000);
			yesNoImg('check_mobile','no');
			return false;
		}
	}else{
		isMobileOK = false;
	}
}
function chkPassword(){
	var pwd = document.getElementById('password').value;
	if(pwd.length > 0){
		if(pwd.length < 6){
			xcsoft.error('密码必须6位以上',3000);
			yesNoImg('check_password','no');
			return false;
		}else{
			var m=0; 
			var Modes=0; 
			for(i=0; i<pwd.length; i++){ 
			var charType=0; 
			var t=pwd.charCodeAt(i); 
			if(t>=48 && t <=57){charType=1;} 
			else if(t>=65 && t <=90){charType=2;} 
			else if(t>=97 && t <=122){charType=4;} 
			else{charType=4;} 
			Modes |= charType; 
			} 
			for(i=0;i<4;i++){ 
				if(Modes & 1){m++;} 
				Modes>>>=1; 
			} 
			if(pwd.length<=5){m=1;} 
			if(pwd.length<=0){m=0;} 
			if(m<2){
				xcsoft.error('密码须6个字符以上，且数字/大写字母/小写字母/符号必须包含两种',4000);
				yesNoImg('check_password','no');
				return false;
			}else{
				yesNoImg('check_password','ok');
				isPasswordOK = true;
				return true;
			}
		}
	}else{
		isPasswordOK = false;
	}
}
function chkPassword2(){
	var pwd = document.getElementById('password').value;
	var pwd2 = document.getElementById('password2').value;
	if(pwd2.length > 0){
		if(pwd == pwd2){
			yesNoImg('check_password2','ok');
			isPassword2OK = true;
			return true;
		}else{
			xcsoft.error('您输入的两次密码不同',3000);
			yesNoImg('check_password2','no');
			return false;
		}
	}else{
		isPassword2OK = false;
	}
}
function chkImgcode(){
	var imgcode = document.getElementById('imgcode').value;
	if(imgcode.length = 4){
		/*
        $.post('/index/register/chkImgcode', {imgcode:jQuery.trim($('#imgcode').val())}, function(msg) {
			if(msg=='error'){
				xcsoft.error('图形验证码错误或者超时！',3000);
				yesNoImg('check_imgcode','no');
				return false;
			}else{
				yesNoImg('check_imgcode','ok');
				isImgcodeOK = true;
				return true;
			}
        });	
		*/
		isImgcodeOK = true;
	}else{
		isImgcodeOK = false;
	}
}
function chkSmscode(){
	var smscode = document.getElementById('smscode').value;
	if(smscode.length > 0){
        $.post('/mobile/register/chkSmscode', {smscode:jQuery.trim($('#smscode').val()),mobile:jQuery.trim($('#mobile').val())}, function(msg) {
			if(msg=='error'){
				xcsoft.error('短信验证码错误或者超时！',3000);
				yesNoImg('check_smscode','no');
				return false;
			}else{
				yesNoImg('check_smscode','ok');
				isSmscodeOK = true;
				return true;
			}
        });		
	}else{
		isSmscodeOK = false;
	}
}
function submitForm(){
	var ok_login = false;
	document.getElementById("loginbtn").disabled = true;
	if(isMobileOK && isPasswordOK && isImgcodeOK){
		var ok_login = true;
	}else{
		chkImgcode();
		chkPassword();
		chkMobile();
		if(!(isMobileOK && isPasswordOK && isImgcodeOK)){
			ok_login = false;
			document.getElementById("loginbtn").disabled = false;
			xcsoft.error('请正确填写手机号码密码及图形验证码',3000);
			return false;
		}else{
			ok_login = true;
		}
	}
	if(ok_login){
		$.post('/mobile/login/getLogin', {mobile:jQuery.trim($('#mobile').val()),pwd:jQuery.trim($('#password').val()),imgcode:jQuery.trim($('#imgcode').val()),isonemonth:jQuery.trim($('#isOneMonthLogin').val())}, function(msg) {
			if(msg=='ok'){
				xcsoft.success('登录成功！2秒后跳转',2000);
				setTimeout("window.location.href='/mobile'", 2000 ); //3秒后跳转
				return true;
			}else{
				xcsoft.error(msg,3000);
				document.getElementById("loginbtn").disabled = false;
				return false;
			}
		});
	}
}
function resetpwd1(){
	var ok_login = false;
	document.getElementById("loginbtn").disabled = true;
	if(isMobileOK && isImgcodeOK && isSmscodeOK){
		var ok_login = true;
	}else{
		chkMobile();
		chkImgcode();
		chkSmscode();
		if(!(isMobileOK && isSmscodeOK && isImgcodeOK)){
			ok_login = false;
			document.getElementById("loginbtn").disabled = false;
			xcsoft.error('请正确填写手机号码图形验证码及短信验证码',3000);
			return false;
		}else{
			ok_login = true;
		}
	}
	if(ok_login){
		$.post('/mobile/login/checkReset', {mobile:jQuery.trim($('#mobile').val()),imgcode:jQuery.trim($('#imgcode').val()),smscode:jQuery.trim($('#smscode').val()),register_token:jQuery.trim($('#register_token').val())}, function(msg) {
			if(msg=='ok'){
				setTimeout("window.location.href='/mobile/login/forgetpassword2'", 0 ); 
				return true;
			}else{
				xcsoft.error(msg,3000);
				document.getElementById("loginbtn").disabled = false;
				return false;
			}
		});
	}
}
function resetpwd2(){
	var ok_login = false;
	document.getElementById("loginbtn").disabled = true;
	if(isPasswordOK && isPassword2OK){
		var ok_login = true;
	}else{
		chkPassword();
		chkPassword2();
		if(!(isPasswordOK && isPassword2OK)){
			ok_login = false;
			document.getElementById("loginbtn").disabled = false;
			xcsoft.error('请正确填写符合要求的密码',3000);
			return false;
		}else{
			ok_login = true;
		}
	}
	if(ok_login){
		$.post('/mobile/login/resetPassword', {password:jQuery.trim($('#password').val()),register_token:jQuery.trim($('#register_token').val())}, function(msg) {
			if(msg=='ok'){
				xcsoft.success('密码已经重设，请重新登录',2000);
				setTimeout("window.location.href='/mobile/login'", 2000 ); //3秒后跳转
				return true;
			}else{
				xcsoft.error(msg,3000);
				document.getElementById("loginbtn").disabled = false;
				return false;
			}
		});
	}
}
function yesNoImg(imgname,isok){
	var field = document.getElementById(imgname);
	if(isok == 'ok'){
		field.src = "/static/images/yes.jpg";
	}else{
		field.src = "/static/images/no.jpg";
	}
	field.style.display = "block";
}
function get_mobile_code(){
	if(isMobileOK && isImgcodeOK){
		$.post('/mobile/register/sendSms', {mobile:jQuery.trim($('#mobile').val()),register_token:jQuery.trim($('#register_token').val())}, function(msg) {
			if(msg=='提交成功'){
				RemainTime();
			}else{
				xcsoft.error(msg,3000);
				return false;
			}
		});
	}else{
		xcsoft.error('请先完善上方所有的信息',3000);
		return false;
	}
};
var iTime = 59;
var Account;
function RemainTime(){
	document.getElementById('zphone').disabled = true;
	var iSecond,sSecond="",sTime="";
	if (iTime >= 0){
		iSecond = parseInt(iTime%60);
		iMinute = parseInt(iTime/60)
		if (iSecond >= 0){
			if(iMinute>0){
				sSecond = iMinute + "分" + iSecond + "秒";
			}else{
				sSecond = iSecond + "秒";
			}
		}
		sTime=sSecond;
		if(iTime==0){
			clearTimeout(Account);
			sTime='获取验证码';
			iTime = 59;
			document.getElementById('zphone').disabled = false;
		}else{
			Account = setTimeout("RemainTime()",1000);
			iTime=iTime-1;
		}
	}else{
		sTime='没有倒计时';
	}
	document.getElementById('zphone').innerText = sTime;
}	
