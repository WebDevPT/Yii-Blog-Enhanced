<?php if($dataProvider): ?>
<div class="summary"><?php echo Yii::t('app', 'Displaying results for "{query}"', array('{query}'=>CHtml::encode($query))) ?></div>
	<?php $this->widget($widget, CMap::mergeArray(array(
		'dataProvider'=>$dataProvider,
		'itemView'=>$this->action->getResultView(),
	), $widgetParams)); ?>
<?php else: ?>
	<?php echo Yii::t('app', 'No results found.'); ?>
<?php endif ?>