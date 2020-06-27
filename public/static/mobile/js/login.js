//为 tips 提示框自定义 CSS,以下为默认
xcsoft.tipsCss = {
	height: '60px',
	fontSize: '16px'
};
//隐藏、显示速度 ，默认 fast
xcsoft.tipsHide=xcsoft.tipsShow=300;

var isMobileOK=false, isEmailOK=false, isPasswordOK=false, isPassword2OK=false, isImgcodeOK=false, isSmscodeOK=false, isEmailcodeOK=false;
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
function chkEmail(){
	var email = document.getElementById('email').value;
    var pattern = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;  
    if (!pattern.test(email)) { 
		xcsoft.error('电子邮件格式错误',3000); 
		yesNoImg('check_email','no');
		isEmailOK = false;
        return false;  
    }else{
		yesNoImg('check_email','ok');
		isEmailOK = true;
		return true;
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
function chkEmailExist(){
	var email = document.getElementById('email').value;
	if(email.length > 0){
		if(chkEmail()){
			$.post('/mobile/register/chkEmail', {email:jQuery.trim($('#email').val())}, function(msg) {
				if(msg=='exists'){
					yesNoImg('check_email','ok');
					isEmailOK = true;
					return true;
				}else{
					xcsoft.error('该电子邮箱并未注册',3000);
					yesNoImg('check_email','no');
					return false;
				}
			});		
		}else{
			xcsoft.error('电子邮箱错误',3000);
			yesNoImg('check_email','no');
			return false;
		}
	}else{
		isEmailOK = false;
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
        $.post('/mobile/register/chkSmscode', {smscode:jQuery.trim($('#smscode').val()),mobile:jQuery.trim($('#mobile').val()), t:new Date()}, function(msg) {
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
function chkEmailcode(){
	var emailcode = document.getElementById('emailcode').value;
	if(emailcode.length > 0){
        $.post('/mobile/register/chkEmailcode', {emailcode:jQuery.trim($('#emailcode').val()),email:jQuery.trim($('#email').val()), t:new Date()}, function(msg) {
			if(msg=='error'){
				xcsoft.error('邮箱验证码错误或者超时！',3000);
				yesNoImg('check_emailcode','no');
				return false;
			}else{
				yesNoImg('check_emailcode','ok');
				isEmailcodeOK = true;
				return true;
			}
        });		
	}else{
		isEmailcodeOK = false;
	}
}
function submitForm(){
	var ok_login = false;
	document.getElementById("loginbtn").disabled = true;
	if(isEmailOK && isPasswordOK && isImgcodeOK){
		var ok_login = true;
	}else{
		chkImgcode();
		chkPassword();
		chkEmail();
		if(!(isEmailOK && isPasswordOK && isImgcodeOK)){
			ok_login = false;
			document.getElementById("loginbtn").disabled = false;
			xcsoft.error('请正确填写电子邮件地址、密码及图形验证码',3000);
			return false;
		}else{
			ok_login = true;
		}
	}
	if(ok_login){
		$.post('/mobile/login/getLogin', {email:jQuery.trim($('#email').val()),pwd:jQuery.trim($('#password').val()),imgcode:jQuery.trim($('#imgcode').val()),isonemonth:jQuery.trim($('#isOneMonthLogin').val())}, function(msg) {
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
function submitFormMobile(){
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
		$.post('/mobile/login/getMobileLogin', {mobile:jQuery.trim($('#mobile').val()),pwd:jQuery.trim($('#password').val()),imgcode:jQuery.trim($('#imgcode').val()),isonemonth:jQuery.trim($('#isOneMonthLogin').val()), t:new Date()}, function(msg) {
			if(msg=='ok'){
				xcsoft.success('登录成功！2秒后跳转',2000);
				setTimeout("window.location.href='/mobile'", 2000 ); //3秒后跳转
				return true;
			}else{
				if(msg=='ok#noEmail'){
					xcsoft.info('登录成功！请您完善邮箱地址信息',2000);
					setTimeout("window.location.href='/mobile/userprofile'", 2000 ); //3秒后跳转
					return true;
				}else{
					xcsoft.error(msg,3000);
					document.getElementById("loginbtn").disabled = false;
					return false;
				}
			}
		});
	}
}
function resetpwd1(){
	var ok_login = false;
	document.getElementById("loginbtn").disabled = true;
	if(((isMobileOK && isSmscodeOK) || (isEmailOK && isEmailcodeOK)) && isImgcodeOK){
		ok_login = true;
	}else{
		if(((isMobileOK && isSmscodeOK) || (isEmailOK && isEmailcodeOK)) && isImgcodeOK){
			ok_login = false;
			document.getElementById("loginbtn").disabled = false;
			xcsoft.error('请正确填写注册信息/图形验证码及验证码',3000);
			return false;
		}else{
			ok_login = true;
		}
	}
	if(ok_login){
		$.post('/mobile/login/checkReset', {mobile:jQuery.trim($('#mobile').val()),imgcode:jQuery.trim($('#imgcode').val()),smscode:jQuery.trim($('#smscode').val()),register_token:jQuery.trim($('#register_token').val()),email:jQuery.trim($('#email').val()),emailcode:jQuery.trim($('#emailcode').val()), t:new Date()}, function(msg) {
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
	chkPassword();
	chkPassword2();
	if(!(isPasswordOK && isPassword2OK)){
		xcsoft.error('请正确填写符合要求的密码',3000);
		return false;
	}
	document.getElementById("loginbtn").disabled = true;
	$.post('/mobile/login/resetPassword', {password:jQuery.trim($('#password').val()),register_token:jQuery.trim($('#register_token').val()), t:new Date()}, function(msg) {
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
	document.getElementById("zphone").disabled = true;
	if(isMobileOK && isImgcodeOK){
		$.post('/mobile/register/sendSms', {mobile:jQuery.trim($('#mobile').val()),register_token:jQuery.trim($('#register_token').val())}, function(msg) {
			if(msg=='提交成功'){
				xcsoft.success('短信发送成功',2000);
				RemainTimeMobile();
			}else{
				document.getElementById("zphone").disabled = false;
				xcsoft.error(msg,3000);
				return false;
			}
		});
	}else{
		document.getElementById("zphone").disabled = false;
		xcsoft.error('请填写注册手机号码及图形验证码',3000);
		return false;
	}
}
function get_email_code(){
	document.getElementById("zemail").disabled = true;
	if(isEmailOK && isImgcodeOK){
		$.post('/mobile/register/sendEmail', {email:jQuery.trim($('#email').val()),register_token:jQuery.trim($('#register_token').val())}, function(msg) {
			if(msg){
				xcsoft.success('邮件发送成功',2000);
				RemainTimeEmail();
			}else{
				document.getElementById("zemail").disabled = false;
				xcsoft.error(msg,3000);
				return false;
			}
		});
	}else{
		document.getElementById("zemail").disabled = false;
		xcsoft.error('请填写注册电子邮箱及图形验证码',3000);
		return false;
	}
}
var iTime = 59;
var Account;
function RemainTimeMobile(){
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
			Account = setTimeout("RemainTimeMobile()",1000);
			iTime=iTime-1;
		}
	}else{
		sTime='没有倒计时';
	}
	document.getElementById('zphone').innerText = sTime;
}	
function RemainTimeEmail(){
	document.getElementById('zemail').disabled = true;
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
			document.getElementById('zemail').disabled = false;
		}else{
			Account = setTimeout("RemainTimeEmail()",1000);
			iTime=iTime-1;
		}
	}else{
		sTime='没有倒计时';
	}
	document.getElementById('zemail').innerText = sTime;
}	
