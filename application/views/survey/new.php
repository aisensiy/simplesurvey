<?php include(__DIR__ . '/survey-base.php') ?>
<?php startblock('title') ?>简单问卷 -- 创建问卷<?php endblock() ?>
<?php startblock('js') ?>
<?php superblock() ?>
<script>
	$(window).bind('load', function() {
	  	if(/msie/i.test(navigator.userAgent)) {
	      alert('simplesuvery对IE内核的浏览器支持的不好哦');
	    }
	  });
	$('.subnav').autoFix(40);
	window.survey_panel = new SurveyPanel({
		editable : true,
		model : new Survey()
	});
	window.control_panel = new ControlPanel({survey_panel: survey_panel});
	window.tag_panel = new SurveyTagPanel;
	survey_panel.tag_panel = window.tag_panel;
	$(':submit').attr('disabled', true);
</script>
<?php endblock() ?>