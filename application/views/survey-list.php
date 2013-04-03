<?php include "base.php"?>

<?php startblock('container') ?>
<div class="row">
	<div class="span10 offset1">
		<?php if($entities): ?>
		<ul class="unstyled survey-list">
			<?php foreach($entities as $entity): ?>
				<li>
					<div>
						<?php if($entity->status == 0): ?>
						<h3><a href="/survey/edit/<?=$entity->id?>"><?=$entity->title?></a></h3>
						<?php else: ?>
						<h3><a href="/survey/show/<?=$entity->id?>"><?=$entity->title?></a><span>(<?=$entity->answer_num?>)</span></h3>
						<?php endif; ?>
						<div class="description"><?=$entity->description?></div>
						<div class="date">创建时间 <?=$entity->created_at?></div>
						<div class="tags">
							<?php foreach($entity->tags as $et): ?>
							<span><a href="<?=site_url('/page/tag/'.$et->id)?>"><?=$et->content?></a></span>
							<?php endforeach; ?>
						</div>
						<?php if($login): ?>
						<div class="panel">
							<?php if($entity->status == '0'): ?>
							<a href="/survey/edit/<?=$entity->id?>">编辑问卷</a>
							<a class="update-status" href="/survey/update_status/<?=$entity->id?>" data-status='1'>发布问卷</a>
							<?php endif; ?>
							<?php if($entity->status == '1'): ?>
							<a href="/result/show/<?=$entity->id?>">查看结果</a>
							<a class="update-status" href="/survey/update_status/<?=$entity->id?>"  data-status='0'>关闭问卷</a>
							<?php endif; ?>
							<a href="/survey/delete/<?=$entity->id?>" class="delete">删除问卷</a>
						</div>
						<?php endif;?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php else: ?>
		<p>没有自己的问卷唉</p>
		<?php endif; ?>
		<?=$paginate?>
	</div>
</div>
<?php endblock() ?>

<?php startblock('js') ?>
<?php superblock() ?>
<script type="text/javascript">
	var survey_list = new SurveyListView;
</script>
<?php endblock() ?>