<?php include(__DIR__ . '/../base.php') ?>
<?php startblock('container') ?>
	<?php startblock('message') ?>
	<!--
	<div class="row">
	  <div class="message-panel span10 offset1">
	    <div class="alert">
	      <a class="close" data-dismiss="alert">×</a>
	      在编辑模式下，双击问卷的文本内容即可进行编辑，拖动问题的标题可更改问题的位置，拖动问题的选项可更改选项的位置
	    </div>
	  </div>
	</div>
	-->
	<?php endblock() ?>
        
	<div class="row">
		<div class="span10 offset1" id="result_container">
			<h2 class="title"></h2>
			<hr />
		</div>
	</div>
	
  <script type="text/template" id="result_text_tmp">
    <section>
      <h3>#{this.content} <a href="#{url}">查看全部</a></h3>
      <table class="table table-bordered table-striped">
        <thead>
        <tr>
          <th class="index">序号</th>
          <th class="content">内容</th>
        </tr>
        </thead>
        <tbody>
        $.each(this.results, function(key, elem) {
          <tr>
            <td># #{key + 1}</td>
            <td>#{elem}</td>
          </tr>
        });
        </tbody>
      </table>
    </section>
  </script>
	<script type="text/template" id="result_tmp">
    <section>
      var name = this.name;
      <h3>#{this.content}</h3>
      <table class="table table-bordered table-striped">
      	<thead>
        <tr>
          <th>选项</th>
          <th>百分比</th>
          <th>数量</th>
          <th>筛选</th>
        </tr>
        </thead>
        var sum = 0;
        $.each(this.results, function(key, elem) {sum += elem.count;});
        //console.log(sum);
        <tbody>
        $.each(this.results, function(key, elem) {
          var percentage = sum != 0 ? Math.round(elem.count / sum * 1000) / 10 : 0;
          <tr>
            <td>#{elem.content}</td>
            <td class="percentage"><div>#{percentage}%<div class="bar" style="width: #{percentage}%"></div></div></td>
            <td>#{elem.count}</td>
            <td><input name="#{name}" value="#{key+1}" type="checkbox" /></td>
          </tr>
        });
        </tbody>
      </table>
    </section>
	</script>
<?php endblock() ?>
<?php startblock('js') ?>
<?php superblock() ?>
<script>
	//var id = /\d+$/.exec(location.pathname)[0];
	window.result = new ResultView;
</script>
<?php endblock() ?>