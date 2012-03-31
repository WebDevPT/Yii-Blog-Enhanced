<div class="search-result">
	<?php echo CHtml::link($this->action->highlight($data->{$this->action->titleAttribute}), $this->action->getUrl($data)); ?>
	<?php foreach($this->action->showAttributes as $attribute): ?>
	<div>
		<?php if ($this->action->showAttributeNames): ?>
			<strong><?php echo $data->getAttributeLabel($attribute) ?>:</strong>
		<?php endif ?>
		<?php echo $this->action->highlight($data->$attribute) ?>
	</div>
	<?php endforeach ?>
</div>
