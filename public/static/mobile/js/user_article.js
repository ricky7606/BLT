//为 tips 提示框自定义 CSS,以下为默认
xcsoft.tipsCss = {
	height: '60px',
	fontSize: '16px'
};
//隐藏、显示速度 ，默认 fast
xcsoft.tipsHide=xcsoft.tipsShow=300;

function delArticle(articleid){
	if(confirm('您确认要删除该发布吗？')){
		$.post('/mobile/userarticles/delArticle', {articleid:articleid}, function(msg) {
			if(msg=='ok'){
				xcsoft.success('删除成功！',2000);
				setTimeout("window.location.reload(true)", 2000 ); //3秒后刷新
				return true;
			}else{
				xcsoft.error(msg,3000);
				return false;
			}
		});
	}
}

function showContent(){
	$("#loading").css({"display":"none"});
}

setTimeout("showContent()", 1000 ); //1秒后显示