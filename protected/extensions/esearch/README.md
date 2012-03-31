Introduction
------------
ESearch provides an action and some default views for search and order by relevance in MySQL without FULLTEXT.

[![Flattr this git repo](http://api.flattr.com/button/flattr-badge-large.png)](https://flattr.com/submit/auto?user_id=jorgebg&url=https://github.com/jorgebg/yii-esearch&title=yii-esearch&language=en_GB&tags=github&category=software) 

Documentation
-------------

###Requirements
* Yii 1.0 or above
* MySQL 5.0 or above

###Installation
* Extract the release file under `protected/extensions/esearch`

###Usage

~~~
[php]
//controllers/SiteController.php

	public function actions(){
		return array(
			// ...
			'search'=>array(
                            'class'=>'ext.esearch.SearchAction',
                            'model'=>'Post',
							'attributes'=>array('title', 'tags', 'content'),
                        )
  		);
  	}

~~~

~~~
[php]
//views/layout/main.php
 	$this->widget('ext.search.SearchBoxPortlet');
//or
 	SearchAction::renderInputBox();
//or
 	$this->renderPartial('extensions/esearch/views/inputBox.php');
~~~

~~~
[php]
'import'=>array(
		// ...
        'ext.esearch.*',
),
~~~

