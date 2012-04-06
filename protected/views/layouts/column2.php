<?php $this->beginContent('/layouts/main'); ?>
<div class="container">
	<div class="span-18">
		<div id="content">
			<?php echo $content; ?>
		</div><!-- content -->
	</div>
	<div class="span-6 last">
		<div id="sidebar">
                        <!-- sponsors -->
                        <?php
                        
                        
                            $sponsors = array(
                                //sample
                                //array('sponsora','Sponsor short text','http://www.yiiframework.com/','yii title'),   //this has custom title
                                array('sponsora','Sponsor short text',Yii::app()->request->baseUrl,'yii title'),   //this has custom title
                                array('sponsorb','Sponsor short text',Yii::app()->request->baseUrl),
                                array('sponsorc','Sponsor short text',Yii::app()->request->baseUrl),
                                array('sponsord','Sponsor short text',Yii::app()->request->baseUrl)
                            );
                            $this->widget('ext.mflip.MFlip',array(
                                'sponsors'=>$sponsors,    //the items to be flipped
                                'single'=>false,          //flip one item at a time;default is false
                                'shuffle'=>false,          //shuffle the array?
                                'title'=>'flip me', //default title for all items. Will be bypassed if the 4th item of sponsors array is present
                                'openNew'=>true           //opens the link in new tab (or window)
                            ));                        
                        
                        
                        ?>
                        <?php SearchAction::renderInputBox(); ?>
			<?php if(!Yii::app()->user->isGuest) $this->widget('UserMenu'); ?>

			<?php $this->widget('TagCloud', array(
				'maxTags'=>Yii::app()->params['tagCloudCount'],
			)); ?>

			<?php $this->widget('RecentComments', array(
				'maxComments'=>Yii::app()->params['recentCommentCount'],
			)); ?>
		</div><!-- sidebar -->
	</div>
</div>
<?php $this->endContent(); ?>