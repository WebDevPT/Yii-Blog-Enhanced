<h5>Post Menu</h5>
<ul>
	<li><?php echo CHtml::link('Create New Post',array('post/create')); ?></li>
	<li><?php echo CHtml::link('Manage Posts',array('post/admin')); ?></li>
	<li><?php echo CHtml::link('Approve Comments',array('comment/index')) . ' (' . Comment::model()->pendingCommentCount . ')'; ?></li>
	<!--<li><?php //echo CHtml::link('Logout',array('site/logout')); ?></li>-->
</ul>
<hr>
<h5>Statistics</h5>
<ul>
    <?php Yii::app()->counter->refresh(); ?>
    <li>online: <?php echo Yii::app()->counter->getOnline(); ?></li>
    <li>today: <?php echo Yii::app()->counter->getToday(); ?></li>
    <li>yesterday: <?php echo Yii::app()->counter->getYesterday(); ?></li>
    <li>total: <?php echo Yii::app()->counter->getTotal(); ?></li>
    <li>maximum: <?php echo Yii::app()->counter->getMaximal(); ?></li>
    <li>date for maximum: <?php echo date('d.m.Y', Yii::app()->counter->getMaximalTime()); ?></li>  
</ul>