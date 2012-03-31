<?php
/*
 * @author CGeorge
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 * Usage example:
 * 
 * views/layout/main.php
 * <pre>
 * 	$this->widget('ext.search.SearchBoxPortlet');
 * </pre>
 */

Yii::import('zii.widgets.CPortlet');
class SearchBoxPortlet extends CPortlet {
	public $url=array('site/search');
	public $htmlOptions;
	public $cssFile;
	
	public function init(){
		parent::init();
		$htmlOptions = $this->htmlOptions;
		$cs=Yii::app()->clientScript;
		if($this->cssFile===null && $this->css===null && isset($htmlOptions['id']))
			$cs->registerCss($htmlOptions['id'].'-css',
				'#'.$htmlOptions['id'].' input.search-input { width:65% } '.
				'#'.$htmlOptions['id'].' input.search-button { width:25% }'
			);
		else if($this->css)
			$cs->registerCss($this->css);
		else if ($this->cssFile)
			$cs->registerCssFile($this->cssFile);
                
		if(!isset($htmlOptions['id']))
			$htmlOptions['id']='search-box';
		if(!isset($htmlOptions['class']))
			$htmlOptions['class']='search';
		if(!isset($htmlOptions['label']))
			$htmlOptions['label']=Yii::t('app', 'Find');
			
		$this->htmlOptions = $htmlOptions;
	}
	
	
	public function renderContent(){
		$this->render('inputBox', array('url'=>$this->url, 'htmlOptions'=>$this->htmlOptions));
	}
        
	
}
