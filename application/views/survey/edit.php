<?php include(__DIR__ . '/survey-base.php') ?>
<?php startblock('js') ?>
<?php superblock() ?>
<script>
	$(window).bind('load', function() {
	  	if(/msie/i.test(navigator.userAgent)) {
	      alert('simplesuvery对IE内核的浏览器支持的不好哦');
	    }
	});
	$('.subnav').autoFix(40);
	var id = /\d+$/.exec(location.pathname)[0];
	window.SurveyPanel.fetch('/index.php/survey/get/' + id, function(data) {
		window.tag_panel = new SurveyTagPanel;
		window.tag_panel.fetch(data.tags);	
		window.survey_panel = new SurveyPanel({
			editable : true,
			model : new Survey(data.survey),
			tag_panel: tag_panel
		});
		window.control_panel = new ControlPanel({survey_panel: survey_panel});
		$(':submit').attr('disabled', true);
	});
</script>
<?php endblock() ?>