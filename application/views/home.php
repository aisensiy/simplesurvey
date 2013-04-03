<?php include "base.php"?>
<?php startblock('title') ?>
简单问卷 -- 用最简单的方式构建你的调查问卷
<?php endblock() ?>
<?php startblock('container') ?>
<div class="row">
	<div class="span10 offset1">
		<div class="hero-unit">
		  <h2>欢迎使用简单问卷</h2>
		  <p>这是一个非常简单的调查问卷构建系统，你可以以最简单的方式构建起来你的问卷并轻松的获取分析结果。</p>
		</div>
	</div>
</div>
<div class="row">
	<div class="span10 offset1">
		<h2>公开问卷</h2>
		<?php if(isset($entities)): ?>
		<ul class="unstyled survey-list">
			<?php foreach($entities as $entity): ?>
				<li>
					<div>
						<h3><a href="<?=site_url('/survey/show/'.$entity->id)?>"><?=$entity->title?></a><span>(<?=$entity->answer_num?>)</span></h3>
						<div class="description"><?=$entity->description?></div>
						<div class="date">创建时间 <?=$entity->created_at?></div>
						<div class="tags">
							<?php foreach($entity->tags as $et): ?>
							<span><a href="<?=site_url('/page/tag/'.$et->id)?>"><?=$et->content?></a></span>
							<?php endforeach; ?>
						</div>
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