<?php
/**
 * Macinville <macinville.blogspot.com>
 *
 * @author Morris Jencen Chavez <macinville@gmail.com>
 * @desc A wrapper for Sponsor Flip Wall <http://tutorialzine.com/2010/03/sponsor-wall-flip-jquery-css/>
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT license
 *
 * @example
 * The most basic usage of this is to pass an the array
 * $sponsors = array(
 * 	array('facebook','The biggest social network in the world.','http://www.facebook.com/','facebook title'),
 * 	array('adobe','The leading software developer targeted at web designers and developers.','http://www.adobe.com/'),
 * 	array('microsoft','One of the top software companies of the world.','http://www.microsoft.com/','microsoft title'),
 * 	array('sony','A global multibillion electronics and entertainment company ','http://www.sony.com/'),
 * 	array('dell','One of the biggest computer developers and assemblers.','http://www.dell.com/'),
 * 	array('ebay','The biggest online auction and shopping websites.','http://www.ebay.com/'),
 * 	array('digg','One of the most popular web 2.0 social networks.','http://www.digg.com/'),
 * 	array('google','The company that redefined web search.','http://www.google.com/'),
 * 	array('ea','The biggest computer game manufacturer.','http://www.ea.com/'),
 * 	array('mysql','The most popular open source database engine.','http://www.mysql.com/'),
 * 	array('hp','One of the biggest computer manufacturers.','http://www.hp.com/'),
 * 	array('yahoo','The most popular network of social media portals and services.','http://www.yahoo.com/'),
 * 	array('cisco','The biggest networking and communications technology manufacturer.','http://www.cisco.com/'),
 * 	array('vimeo','A popular video-centric social networking site.','http://www.vimeo.com/'),
 * 	array('canon','Imaging and optical technology manufacturer.','http://www.canon.com/')
 * );
 * $this->widget('application.extensions.mflip.MFlip',array(
 *     'sponsors'=>$sponsors,    //the items to be flipped
 *     'single'=>false,          //flip one item at a time;default is false
 *     'shuffle'=>true,          //shuffle the array?
 *     'title'=>'Click to flip' //default title for all items. Will be overriden if the 4th item of sponsors array is given
 *     'openNew'=>false          //opens the link in a new page (or tab,depending on the browser's setup)
 * ));
 *
 */
class MFlip extends CWidget {

    /**
     * @var string Path of the asset files after publishing.
     */
    private $assetsPath;

    /**
     * @var array Contains the data to be flipped
     */
    public $sponsors = array();

    /**
     * @var bool Determines if the array of items should be shuffled or not. Defaults to true.
     */
    public $shuffle=true;

    /**
     * @var bool Determines if the user can only flip one item at a time
     */
    public $single=false;

    /**
     * @var string Default title for the items to be flipped
     */
    public $title = 'Click to flip';

    /**
     * @var bool Determines whether the link should be opened in new window
     * (or tab,depending on your browser setup) or not
     */
    public $openNew = false;

    public function init() {
        $assets = dirname(__FILE__) . '/' . 'assets';
        $this->assetsPath = Yii::app()->getAssetManager()->publish($assets);
        Yii::app()->getClientScript()->registerCssFile($this->assetsPath . '/' . 'styles.css', 'screen, projection');
        Yii::app()->getClientScript()->registerScriptFile($this->assetsPath . '/' . 'script.js');
        Yii::app()->getClientScript()->registerScriptFile($this->assetsPath . '/' . 'jquery.flip.min.js');
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerCoreScript('jquery.ui');
    }

    public function run() {
        if (!empty($this->sponsors)) {
            $this->shuffle ? shuffle($this->sponsors): '';
            foreach ($this->sponsors as $company) {
                $title = isset($company[3]) ?  $company[3]: $this->title;
                $target = $this->openNew ? "target='_blank'":'';
                echo '<div class="sponsor" title="'.$title.'">
                            <div class="sponsorFlip">
                                    <img src="' . $this->assetsPath . '/img/sponsors/' . $company[0] . '.png" alt="More about ' . $company[0] . '" />
                            </div>
                            <div class="sponsorData">
                                <br/><center>'. $company[1] .'</center><br/><center><a href="' . $company[2] . '" '.$target.'>More info</a></center>
                            </div>
                    </div>
            ';
            }
            if($this->single){
                Yii::app()->clientScript->registerScript('flip','$(".sponsorFlip").bind("click",function(e,block){
                        if(block) return false;
                        $(".sponsorFlip").not($(this)).each(function(){

                                if($(this).data("flipped"))
                                        $(this).trigger("click",[true]);
                        });
                });');
            }
        }
    }

}

?>
