<?php include(__DIR__ . '/survey-base.php') ?>
<?php startblock('control_panel') ?>
<?php endblock() ?>
<?php startblock('message') ?>
	<div class="row">
	  <div class="message-panel span10 offset1">
	  </div>
	</div>
<?php endblock() ?>

<?php startblock('extra') ?>
<!-- JiaThis Button BEGIN -->
<div class="row" id="meta-1">
	<div class="bshare-custom"><a title="分享到新浪微博" class="bshare-sinaminiblog" href="javascript:void(0);"></a><a title="分享到人人网" class="bshare-renren" href="javascript:void(0);"></a><a title="分享到豆瓣" class="bshare-douban" href="javascript:void(0);"></a><a title="分享到Facebook" class="bshare-facebook" href="javascript:void(0);"></a><a title="分享到Twitter" class="bshare-twitter" href="javascript:void(0);"></a><a title="分享到QQ空间" class="bshare-qzone" href="javascript:void(0);"></a><a title="分享到腾讯微博" class="bshare-qqmb" href="javascript:void(0);"></a><a title="更多平台" class="bshare-more bshare-more-icon more-style-addthis"></a><span class="BSHARE_COUNT bshare-share-count">0</span></div><script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/buttonLite.js#style=-1&amp;uuid=&amp;pophcol=2&amp;lang=zh"></script><script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/bshareC0.js"></script>
</div>

<!-- JiaThis Button END -->
<!--
<div class="row">
	<div class="span8 offset2">
		<script type='text/javascript' charset='utf-8' src='http://open.denglu.cc/connect/commentcode?appid=18128denvQIqh2d0axktwlq1fojgA6'></script>
	</div>
</div>
-->
<?php endblock() ?>

<?php startblock('js') ?>
<?php superblock() ?>
<script>
	var id = /\d+$/.exec(location.pathname)[0];
	window.tag_panel = new SurveyTagPanel({'mode': 'read'});
	window.SurveyPanel.fetch('/index.php/survey/get/' + id, function(data) {
		tag_panel.fetch(data.tags);
		window.survey_panel = new SurveyPanel({
			editable : false,
			tag_panel: window.tag_panel,
			model : new Survey(data.survey)
		});
		if(data.submited) {
			alert('已经填写过这个问卷了哦');
			$(':submit').attr('disabled', true);
		}
		window.control_panel = new ControlPanel({survey_panel: survey_panel});
	});
</script>
<?php endblock() ?>
