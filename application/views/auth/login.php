<?php include(__DIR__ . '/../base.php') ?>
<?php startblock('title') ?>
简单问卷 -- 用户登录
<?php endblock() ?>
<?php startblock('container') ?>
	<dir class="row">
		<div class="form-panel login-panel span8 offset2">
			<form action="/index.php/auth/read">
				<div class="row header">
					<h1>Simple Survey</h1>
				</div>
				<div class="row" style="display: none" id="error">
					<div class="span8">
						<div class="alert alert-error"></div>
					</div>
				</div>
				<div class="row input-box">
					<div class="span4"><input name="email" type="text" placeholder="电子邮件" class="span4 email" autofocus /></div>
					<div class="span4"><input name="password" type="password" placeholder="密码" class="span4 password"/></div>
				</div>
				<div class="row">
					<div class="span8">
						<input name="submit" type="submit" value="登录" class="btn btn-large" />
						<!--<input name="register" type="button" value="注册" class="btn btn-large" />-->
					</div>
				</div>
				<div class="row">
					<div class="span8">
						<script id='denglu_login_js' type='text/javascript' charset='utf-8'></script>
						<script type='text/javascript' charset='utf-8'>
							(function() {
								var time = new Date().getTime();
								var $login = document.getElementById('denglu_login_js');
								$login.id = $login.id + '_' + time;
								$login.src = 'http://open.denglu.cc/connect/logincode?appid=18128denvQIqh2d0axktwlq1fojgA6&v=1.0.2&widget=3&styletype=1&size=588_154&asyn=true&time=' + time;
							})();
						</script>
					</div>
				</div>	
			</form>
		</div>
	</dir>
<?php endblock() ?>

<?php startblock('js') ?>
<?php superblock() ?>
<script type="text/javascript">
	window.login = new LoginView;
</script>
<?php endblock() ?>