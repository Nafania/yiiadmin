<?php
$cs=Yii::app()->clientScript;
$baseUrl=$this->getModule()->getAssetsUrl();
$cs->registerCssFile($baseUrl.'/css/base.css');
$cs->registerScriptFile($baseUrl.'/js/toastmessage/javascript/jquery.toastmessage.js');
$cs->registerCssFile($baseUrl.'/js/toastmessage/resources/css/jquery.toastmessage.css');
$cs->registerScriptFile($baseUrl.'/js/common.js');
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('bbq');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="" xml:lang="" > 
<head> 
    <title>Yii Administration</title> 
    <meta name="robots" content="NONE,NOARCHIVE" /> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head> 
<body class="dashboard"> 
    <div id="container"> 

<div id="header"> 
    <div class="branding">&nbsp;</div> 
    <div class="admin-title"><?php echo CHtml::link(YiiadminModule::t('На сайт'), Yii::app()->createUrl('/site/index')) ?></div>
    <?php
    $this->widget('application.modules.yiiadmin.widgets.userTools');
    ?>
</div> 
 
    <?php 
        $message=Yii::app()->user->getFlash('flashMessage');
        if ($message): 
    ?> 
    <ul class="messagelist">
        <li><?php echo $message; ?></li>
    </ul>
    <?php endif; ?>

    <!-- BREADCRUMBS --> 
    <div id="breadcrumbs">
    <?php
    $this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link(YiiadminModule::t('Главная'),array('/yiiadmin')),
        'links'=>$this->breadcrumbs,
	'encodeLabel' => false
        )
    ); 
    ?>
    </div>
        

 
        <?php
            echo $content;
        ?>

        <!-- FOOTER --> 
        <div id="footer"></div> 
        
    </div> 
</body> 
</html> 
 
