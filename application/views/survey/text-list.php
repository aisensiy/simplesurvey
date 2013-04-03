<?php include(__DIR__ . '/../base.php') ?>
<?php startblock('container') ?>
	<?php startblock('message') ?>
	
	<?php endblock() ?>
        
	<div class="row">
		<div class="span10 offset1" id="result_container">
			<h2 class="title"><?=$content?> <a href="<?=$url?>">返回</a> </h2>
			<hr />
      <section>
        <table class="table table-bordered table-striped">
          <thead>
          <tr>
            <th class="index">序号</th>
            <th class="content">内容</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach($results as $index => $answer): ?>
            <tr>
              <td># <?=($index+1)?></td>
              <td><?=$answer?></td>
            </tr>
          <?php endforeach;?>
          </tbody>
        </table>
      </section>
		</div>
    <?=$paginate?>
	</div>
	
  
<?php endblock() ?>
