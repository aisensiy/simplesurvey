<?php include(__DIR__ . '/../base.php') ?>
<?php startblock('container') ?>

	<?php startblock('control_panel') ?>
	<div class="row" id="control_panel">
	  
	  <!-- 编辑面板 | 次级导航-->
	  <div class="subnav span10 offset1">
	    <ul class="nav nav-pills">
	      <li class="dropdown">
	        <a href="#" data-toggle="dropdown" class="dropdown-toggle">单选题 <b class="caret"></b></a>
	        <ul class="dropdown-menu">
	          <li>
	            <a href="#" data-type="single-choice" class="create-btn">列表单选</a>
	          </li>
	          <li>
	            <a href="#" data-type="single-dropdown" class="create-btn">下拉单选</a>
	          </li>
	        </ul>
	      </li>
	      <li class="dropdown">
	        <a href="#" data-toggle="dropdown" class="dropdown-toggle">多选题 <b class="caret"></b></a>
	        <ul class="dropdown-menu">
	          <li>
	            <a href="#" data-type="multi-choice" class="create-btn">列表多选</a>
	          </li>
	          <li>
	            <a href="#" data-type="multi-dropdown" class="create-btn">下拉多选</a>
	          </li>
	        </ul>
	      </li>
	      <li class="dropdown">
	        <a href="#" data-toggle="dropdown" class="dropdown-toggle">矩阵题 <b class="caret"></b></a>
	        <ul class="dropdown-menu">
	          <li>
	            <a href="#" data-type="single-matrix" class="create-btn">矩阵单选</a>
	          </li>
	          <li>
	            <a href="#" data-type="multi-matrix" class="create-btn">矩阵多选</a>
	          </li>
	        </ul>
	      </li>
	      <li>
	        <a href="#" data-type="text" class="create-btn">文本框</a>
	      </li>
	    </ul>
	    <ul class="nav pull-right nav-pills">
	      <!--
    	  <li>
	        <a href="#" data-type="text" class="mode" data-mode="edit-mode">预览模式</a>
	      </li>
	  	  -->
	      <li>
	        <a href="#" data-type="text" class="save btn btn-primary">保存</a>
	      </li>
	      <!--
	      <li>
	        <a href="#" data-type="text" class="publish">发布</a>
	      </li>
	  	  -->
	    </ul>
	  </div>
	  <!-- End 编辑面板 | 次级导航-->
	</div>
	<?php endblock() ?>
	<?php startblock('message') ?>
	<?php 
		if(!empty($message)):
	?>
	<div class="alert alert-success">
		<?=$message?>
	</div>
	<?php endif; ?>
	<?php 
		if(!empty($error)):
	?>
	<div class="alert alert-error">
		<?=$error?>
	</div>
	<?php endif; ?>
	<div class="row">
	  <div class="message-panel span10 offset1">
	    <div class="alert">
	      <a class="close" data-dismiss="alert">×</a>
	      在编辑模式下，双击问卷的文本内容即可进行编辑，拖动问题的标题可更改问题的位置，拖动问题的选项可更改选项的位置
	    </div>
	  </div>
	</div>
	<?php endblock() ?>
	<?php startblock('tag-panel') ?>
	<div class="row" id="tag-panel">
		<div class="span8 offset2">
			<h3>问卷标签</h3>
			<div class="input-append">
                <input class="span2" id="add-tag" size="16" type="text"><button id="add-tag-btn" class="btn" type="button">添加</button>
            </div>
			<div class="tags">
			</div>
		</div>
	</div>
	<?php endblock() ?>
	<?php startblock('survey_panel') ?>
	<div class="row" id="survey_panel" style="display: none">
		<div class="span8 offset2">
			<!--form>section.survey>header>h2+div.description-->
			<form action="">
			  <div class="alert alert-error" style="display: none "></div>
			  <!-- Survey 主界面 -->
				<section class="survey">
					<header>
						<h2></h2>
						<div class="description"></div>
					</header>
					<!--ul.questions>li-->
					<ul class="questions unstyled">
						
					</ul><!--end questions-->
					<footer class="clear">
						<div class="pull-right">
							<button type="submit" class="btn btn-primary">提交</button>
			        <button type="reset" class="btn">Cancel</button>
						</div>
					</footer>
				</section>
				<!-- End Survey 主界面 -->
			</form>
		</div>
	</div><!--end row-->
	<?php endblock() ?>
	<?php emptyblock('extra') ?>

<!-- template -->
<script type="text/template" id="choice_tmp">
  <div class="alert alert-error" style="display: none "></div>
  <fieldset>
	<legend class="content">#{this.content}</legend>
	<ul class="unstyled choice">
		var type = /single/i.test(this.type) ? 'radio' : 'checkbox',
		      id = this.id;
		$.each(this.choices, function(i, elem) {
			if(!elem.other) {
			<li>
				<span><label class="#{type}"><input name="field#{id}" type="#{type}" value="#{i+1}"/>#{elem.content}</label></span>
			</li>
			}
			else {
			<li class="form-inline other">
			  <span class=""><label class="#{type}"><input name="field#{id}" type="#{type}" value="#{i+1}"/>#{elem.content}</label></span>
			  <input name="other#{id}" type="text" class="input-small"/>
			</li>	
			}
		});
	</ul>
	</fieldset>
	
</script>

<script type="text/template" id="text_tmp">
  <div class="alert alert-error" style="display: none "></div>
  <fieldset>
	<legend class="content">#{this.content}</legend>
	<div class="choice">
		<textarea rows="5" class="large" name="field#{this.id}"></textarea>
	</div>
	</fieldset>
</script>

<script type="text/template" id="matrix_tmp">
  <div class="alert alert-error" style="display: none "></div>
	var type = /single/i.test(this.type) ? 'radio' : 'checkbox';
	<fieldset>
	<legend class="content">#{this.content}</legend>
	<div class="choice">
		<table border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-striped matrix">
			<thead>
				<tr>
					<td></td>
					$.each(this.vals, function(i, elem) {
						<td><div>#{elem}</div></td>
					});
				</tr>
			</thead>
			<tbody>
			  var self = this;
				$.each(this.questions, function(i, val) {
					<tr>
						<th><div>#{val[0]}</div></th>
						$.each(self.vals, function(j) {
							<td><input type="#{type}" name="field#{val[1]}" value="#{j+1}"/></td>
						});
					</tr>
				});
			</tbody>
		</table>
	</div>
	</fieldset>
</script>
<script type="text/template" id="dropdown_tmp">
  <div class="alert alert-error" style="display: none "></div>
  var type = /single/i.test(this.type) ? '' : 'multiple="multiple"';
  <fieldset>
    <legend class="content">#{this.content}</legend>
    <div class="choice">
      <select class="span4" #{type} name="field#{this.id}">
        $.each(this.choices, function(index, elem) {
          <option value="#{index+1}">#{elem}</option>
        });
      </select>
    </div>
  </fieldset>
</script>
<script type="text/template" id="edit_panel_tmp">
  var map = {
    'pencil': '编辑',
    'remove': '删除',
    'arrow-up': '移至最前',
    'chevron-up': '向上一个位置',
    'chevron-down': '向下一个位置',
    'arrow-down': '移至最后',
    'plus': '添加',
    'ok': '完成'
  };
  <span class="edit-panel">
    $.each(this, function(i, e) {
      <span class="icon-#{e}" title="#{map[e]}"></span>
    });
  </span>
</script>
<script type="text/template" id="tooltip_tmp">
  <div class="tooltip fade left in">
    <div class="tooltip-arrow"></div>
    <div class="tooltip-inner">
      #{this.content}
    </div>
  </div>
</script>
<?php endblock() ?>

<?php startblock('js') ?>
<?php superblock() ?>
<script>
  
  /*
  $(window).bind('beforeunload', function(){
	return 'Are you sure you want to leave?';
  });
  */
</script>
<?php endblock() ?>
