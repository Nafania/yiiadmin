<?php
Yii::import('zii.widgets.CPortlet');

class userTools extends CPortlet
{
	public function init() {
		parent::init();
	}

	protected function renderContent() {
            $_items = array();

            if ( !Yii::app()->user->isGuest ) {
       
                $_items = array(
		    array(
			'label' => YiiadminModule::t('Главная'),
			'url' => array('/yiiadmin/default/'),
		    ),
		);

		$modules = Yii::app()->getModule('yiiadmin')->getSubModules();
		$cats = array();

		foreach ( $modules AS $moduleTitle => $data ) {

		    $module = Yii::app()->getModule('yiiadmin')->getModule($moduleTitle);
		    $cat = call_user_func(array($module, 'getModuleCategory'));

		    $cats[$cat][] = array('label' => Yii::t($moduleTitle, $moduleTitle), 'url' => array('/yiiadmin/' . $moduleTitle . '/'));
		}

		foreach ( $cats AS $cat => $items ) {
		    $_items[] = array(
			'label' => $cat,
			'items' => $items
		    );
		}

                $_items[] = array(
                    'label' => YiiadminModule::t('Выход'),
                    'url' => array('/yiiadmin/default/logout'),
                );
            }

            $this->render('userTools', array(
                'items' => $_items,
            ));
	}
}
?>