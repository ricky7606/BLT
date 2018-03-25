document.getElementById('createWallet').addEventListener('click', function () {
	if(true){
		$.post('/index/userwallet/createWallet', {recommend:jQuery.trim($('#recommend').val(),wallet_tag:"纸钱包",wallet_address:jQuery.trim($('#key').val())}, function(msg) {
			if(msg=='ok'){
				alert('钱包创建成功！');
				window.location.href='/index';
				return true;
			}else{
				alert(msg);
				return false;
			}
		});
	}else{
		document.getElementById("submitbtn").disabled = false;
		xcsoft.error('您输入的钱包信息有误~',3000);
		return false;
	}
}, false)