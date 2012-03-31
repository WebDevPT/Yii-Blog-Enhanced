<?php
/**
 * @author CGeorge
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * 
 * Usage example:
 * 
 * controllers/SiteController.php
 * <pre>
 * 	public function actions(){
 * 		return array(
 * 			'search'=>array(
 *					'class'=>'ext.esearch.SearchAction',
 *                  'model'=>'Post',
 *					'attributes'=>array('title', 'tags', 'content'),
 *			)
 * 		);
 * 	}
 * </pre>
 * 	
 * 
 * views/layout/main.php
 * <pre>
 * 	$this->widget('ext.search.SearchBoxPortlet');
 * </pre>
 * or
 * <pre>
 * 	SearchAction::renderInputBox();
 * </pre>
 * or
 * <pre>
 * 	$this->renderPartial('extensions/esearch/views/inputBox.php');
 * </pre>
 * 
 * 
 */
class SearchAction extends CAction {

        public $layout;
	public $view;
	public $resultView;
	public $viewPath;
	public $model;
	public $attributes = array('title', 'tags', 'content');
	public $titleAttribute;
	public $showAttributes;
	public $urlAttribute;
	public $urlCallback;
	public $urlExpression = 'array(lcfirst(get_class($result))."/view", "id"=>$result->id)';
	public $queryParameter = 'q';
	public $highlightTag = 'strong';
	public $cssFile;
	public $showAttributeNames = false;
	public $minTermLength = 3;
	public $maxHighlightedLength = 255;
	public $resultViewExpression;
	public $widget='zii.widgets.CListView';
	public $widgetParams=array();

	protected $_stopWords;
	protected $_criteria;
	protected $_dataProvider;
	protected $_view;
	protected $_q;
	protected $_qsw;

	public function getStopWords() {
		if (!$this->_stopWords)
			return array();
		else if (is_string($this->_stopWords))
			$this->_stopWords = require($this->_stopWords . '/' . Yii::app()->language . '.php');
		return $this->_stopWords;
	}

	public function setStopWords($value) {
		$this->_stopWords = $value;
	}

	public function init() {

		$this->attributes = (array) $this->attributes;
		$this->showAttributes = (array) $this->showAttributes;

		if ($this->_stopWords === null)
			$this->_stopWords = dirname(__FILE__) . '/stopwords';

		if ($this->view === null) {
			$this->viewPath = 'ext.esearch.views.';
			$this->view = 'results';
			$this->resultView = '_result';
		}
                
                if($this->layout !== null)
                        Yii::app()->getController()->layout = $this->layout;

		if ($this->titleAttribute === null) {
			$this->titleAttribute = reset($this->attributes);
		}
		if ($this->showAttributes === null) {
			$attributes = $this->attributes;
			$key = array_search($this->titleAttribute, $attributes);
			unset($attributes[$key]);
			$this->showAttributes = $attributes;
		}



		$searched = $this->getSearched(true);
		if ($searched) {
			$criteria = $this->getCriteria();
			$conditions = new CDbCriteria();
			foreach ($this->attributes as $attribute) {
				if (strpos($attribute, '.') === false)
					$attribute = ($criteria->alias ? $criteria->alias : 't') . '.' . $attribute;
				foreach ($searched as $term) {
					$conditions->addSearchCondition($attribute, $term, true, 'OR');
				}
			}

			$criteria->mergeWith($conditions);

			if (empty($criteria->order)) {
				$orders = array();
				$select = array('*');
				$searched = $this->getSearched(true);
				$weight = count($this->attributes) * count($searched);
				foreach ($this->attributes as $attribute) {
					if (strpos($attribute, '.') === false)
						$attribute = ($criteria->alias ? $criteria->alias : 't') . '.' . $attribute;
					foreach ($searched as $n => $searchterm) {
						$searchtermTag = array_search('%'.$searchterm.'%', $criteria->params);
						$orders[] = $weight . '*(length(' . $attribute . ')-length(replace(' . $attribute . ',' . $searchtermTag . ',\'\')))/length(' . $searchtermTag . ')';
						$weight--;
					}
				}
				$criteria->order = implode('+', $orders) . ' desc';
			}
		}

		$cs = Yii::app()->clientScript;
		if ($this->cssFile === null)
			$cs->registerCss($this->id . '-results-css', 'div.search-result { margin: 1em 0 } ' .
					'div.search-result div { margin: 0.2em 0 }'
			);
		else
			$cs->registerCssFile($this->cssFile);
	}

	public function run() {
		$this->init();
		$this->controller->render($this->viewPath . $this->view, array('query' => $this->getQuery(), 'dataProvider' => $this->getDataProvider(), 'widget'=>$this->widget, 'widgetParams'=>$this->widgetParams));
	}

	public function getDataProvider() {
		if (!$this->getSearched(true))
			return false;

		if ($this->_dataProvider === null)
			$this->_dataProvider = new CActiveDataProvider($this->model, array(
						'criteria' => $this->getCriteria(),
					));
		return $this->_dataProvider;
	}

	public function getResultView($result=null) {
		if ($this->_view === null || ($result !== null && $this->resultViewExpression))
			if ($this->resultViewExpression)
				$this->resultView = eval('return ' . $this->resultViewExpression . ';');
			else
				$this->_view=$this->viewPath . $this->resultView;

		return $this->_view;
	}

	public function getUrl($result) {
		if ($this->urlCallback !== null)
			return call_user_func($this->urlCallback, $result);
		else if ($this->urlAttribute !== null)
			return $result->{$this->urlAttribute};
		return eval('return ' . $this->urlExpression . ';');
	}

	public function getQuery() {
		return isset($_REQUEST[$this->queryParameter]) ? $_REQUEST[$this->queryParameter] : null;
	}

	protected function getSearched($stopWords=false) {
		if ($this->_q === null) {
			$parameter = $this->getQuery();
			$this->_q = preg_split('/[,\s]+/', $parameter);
			$this->_q = array_merge(array($parameter), $this->_q);
			$this->_q = array_unique($this->_q);
			foreach ($this->_q as $i => $term)
				if (strlen($term) < $this->minTermLength)
					unset($this->_q[$i]);
		}

		if ($stopWords) {
			if ($this->_qsw === null)
				$this->_qsw = array_diff($this->_q, $this->getStopWords());
			return $this->_qsw;
		}

		return $this->_q;
	}

	public function getCriteria() {
		if ($this->_criteria === null || is_array($this->_criteria)) {
			$criteria = new CDbCriteria(is_array($this->_criteria) ? $this->_criteria : array());
			$this->_criteria = $criteria;
		}
		return $this->_criteria;
	}

	public function setCriteria($value) {
		$this->_criteria = $value;
	}

	public function highlight($text) {
		$text = CHtml::encode($text);
		foreach ($this->getSearched(true) as $term) {
			$term = CHtml::encode($term);
			if (stripos($text, $term) !== false)
				$text = preg_replace('/(' . addslashes($term) . ')/i', "<{$this->highlightTag}>$0</{$this->highlightTag}>", $text);
			if ($this->maxHighlightedLength && strlen($text) > $this->maxHighlightedLength)
				$text = substr($text, 0, $this->maxHighlightedLength) . '...';
		}
		return $text;
	}

	public static function renderInputBox($options=array()) {
		extract(array_merge(array('url' => array('site/search'), 'htmlOptions' => array(), 'view' => 'ext.esearch.views.inputBox'), $options));
		if (!isset($htmlOptions['class']))
			$htmlOptions['class'] = 'search';
		if (!isset($htmlOptions['label']))
			$htmlOptions['label'] = Yii::t('app', 'Find');
		Yii::app()->getController()->renderPartial($view, array('url' => $url, 'htmlOptions' => $htmlOptions));
	}

}

