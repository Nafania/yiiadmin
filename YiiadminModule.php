<?php

/**
 * YiiadminModule
 *
 * @uses CWebModule
 * @package YiiAdmin
 * @version $id$
 * @copyright 2010
 * @author Firstrow <firstrow@gmail.com>
 * @license BSD
 */
class YiiadminModule extends CWebModule {
	public $db;

	private $_assetsUrl;
	protected $model;
	public $attributesWidgets = null;
	public $_modelsList = array();
	public static $fileExt = '.php';
	private $controller;
	public $password;
	public $registerModels = array();
	public $excludeModels = array();

	private $_subModulesPath;

	public function init () {

		Yii::app()->clientScript->registerCoreScript( 'jquery' );
		Yii::app()->clientScript->registerCoreScript( 'jquery.ui' );

		$this->_subModulesPath = Yii::getPathOfAlias( 'application.modules.yiiadmin.modules' );

		Yii::setPathOfAlias( 'application.modules.yiiadmin', 'yiiadmin' );

		Yii::app()->setComponents( array(
			'errorHandler' => array(
				'errorAction' => 'yiiadmin/default/error',
			),
			'user' => array(
				'class' => 'CWebUser',
				'stateKeyPrefix' => 'yiiadmin',
				'loginUrl' => Yii::app()->createUrl( 'yiiadmin/default/login' ),
			),
		) );

		$this->setImport( array(
			'yiiadmin.models.*',
			'yiiadmin.modules.*',
			'yiiadmin.components.*',
			'yiiadmin.extensions.*',
			'yiiadmin.helpers.*',
		) );

		//default cgridview for all widgets in admin
		Yii::app()->widgetFactory->widgets['CGridView'] = array(
			'itemsCssClass' => 'table',
			'enablePagination' => true,
			'pagerCssClass' => 'pagination',
			'selectableRows' => 2,
			'pager' => array(
				'cssFile' => false,
				'htmlOptions' => array( 'class' => 'pagination' ),
				'header' => false,
			),
			'template' => '
		<div class="module pagination">
		{pager}{summary}<br clear="all" />
		</div> 
                <div id="changelist" class="module changelist-results">
                    {items}
                </div>
                <div class="module pagination">
                    {pager}{summary}<br clear="all" />
                </div> 
            ',
		);

		$this->setModules( $this->getSubModules() );
	}

	public function getSubModules () {
		$modules = array();

		if ( is_dir( $this->_subModulesPath ) ) {
			$_modules = scandir( $this->_subModulesPath );

			foreach ( $_modules AS $key => $module ) {
				if ( $module == '.' || $module == '..' ) {
					continue;
				}
				$modules[$module] = $this->loadSubModuleConfig( $module );
			}

		}
		return $modules;
	}

	private function loadSubModuleConfig ( $moduleName ) {
		if ( @is_file( $this->_subModulesPath . '/' . $moduleName . '/config/config.php' ) ) {
			$config = new CConfiguration( $this->_subModulesPath . '/' . $moduleName . '/config/config.php' );
			return $config->toArray();
		}
		return array();
	}

	/**
	 * Получение списка моделей
	 *
	 * @access public
	 * @return void
	 */
	public function getModelsList () {
		$models = $this->registerModels;

		if ( !empty( $models ) ) {
			foreach ( $models as $model ) {
				// Импорт всех моделей(модели)
				Yii::import( $model );

				if ( substr( $model, -1 ) == '*' ) {
					// Если импортируем директорию с моделями,
					// Получим список моделей
					$files = CFileHelper::findFiles( Yii::getPathOfAlias( $model ) );
					if ( $files ) {
						foreach ( $files as $file ) {
							$class_name = str_replace( self::$fileExt, '', substr( strrchr( $file, DIRECTORY_SEPARATOR ), 1 ) );
							$this->addModel( $class_name );
						}
					}
				}
				else {
					$class_name = substr( strrchr( $model, "." ), 1 );
					$this->addModel( $class_name );
				}
			}
		}

		return array_unique( $this->_modelsList );
	}

	/**
	 * Добавление модели в список.
	 *
	 * @param mixed $name
	 * @access protected
	 * @return void
	 */
	protected function addModel ( $name ) {
		if ( !in_array( $name, $this->excludeModels ) && method_exists( $name, 'adminSearch' ) )
			$this->_modelsList[] = $name;
	}

	/**
	 * Загрузка модели
	 *
	 * @param string $name
	 * @access public
	 * @return object
	 */
	public function loadModel ( $model = '', $scenario = '' ) {
		$model = ( $model ? $model : (string) $_GET['model_name'] );
		$this->model = new $model( $scenario );

		return $this->model;
	}

	public function createWidget ( $form, $model, $attribute ) {
		$widgetName = $widget = $this->getAttributeWidget( $attribute );
		if ( is_object( $widget ) ) {
			$widgetName = get_class( $widget );
		}
		switch ( $widgetName ) {
			case 'textArea':
				return $form->textArea( $model, $attribute, array( 'class' => 'vLargeTextField' ) );
				break;

			case 'textField':
				return $form->textField( $model, $attribute, array( 'class' => 'vTextField' ) );
				break;

			case 'CFileValidator':
				return $form->fileField( $model, $attribute, array( 'class' => 'vFileBrowseField' ) );
				break;

			case 'dropDownList':
				return $form->dropDownList( $model, $attribute, $this->getAttributeChoices( $attribute ), array( 'empty' => '- select -' ) );
				break;

			case 'CDateValidator':
				$dateFormat = $widget->format;
				$dateFormat = strtolower( $dateFormat );
				$dateFormat = str_replace( 'yyyy', 'yy', $dateFormat );

				$data = array(
					'name' => get_class( $model ) . '[' . $attribute . ']',
					'value' => $model->$attribute,
					'language' => Yii::app()->getLocale()->getId(),
					'htmlOptions' => array( 'class' => 'vDateField' ),
					'options' => array(
						'showAnim' => 'fold',
						'dateFormat' => $dateFormat,
					),
				);

				$this->controller->widget( 'zii.widgets.jui.CJuiDatePicker', $data );
				break;

			case 'CBooleanValidator':
				return $form->checkBox( $model, $attribute );
				break;

			default:
				return $form->textField( $model, $attribute, array( 'class' => 'vTextField' ) );
				break;
		}
	}

	protected function getAttributeWidget ( $name ) {
		$return = false;

		if ( method_exists( $this->model, 'attributeWidgets' ) ) {
			$attributeWidgets = $this->model->attributeWidgets();

			$temp = array();

			if ( !empty( $attributeWidgets ) ) {
				foreach ( $attributeWidgets as $key => $val ) {
					if ( isset( $val[0] ) && isset( $val[1] ) ) {
						if ( $name == $val[0] ) {
							$return = $val[1];
						}
						$temp[$val[0]] = $val[1];
						$temp[$val[0] . 'Data'] = $val;
					}
				}
			}

			$this->attributesWidgets = (object) $temp;
		}
		else {
			$validators = $this->model->getValidators( $name );

			foreach ( $validators AS $validator ) {
				$validatorName = get_class( $validator );
				switch ( $validatorName ) {
					case 'CDateValidator':
						return $validator;
						break;
					case 'CFileValidator':
						return $validator;
						break;
					case 'CBooleanValidator':
						return $validator;
						break;
				}
			}

			$dbType = $this->model->tableSchema->columns[$name]->dbType;
			if ( $dbType == 'text' ) {
				return 'textArea';
			}
		}

		return $return;
	}

	protected function getAttributeData ( $attribute ) {
		$attribute .= 'Data';
		if ( isset( $this->attributesWidgets->$attribute ) )
			return $this->attributesWidgets->$attribute;
		else
			return null;
	}

	/**
	 * Получение массива значений атрибута.
	 * Имя переменной массива с значениями должно быть: attributeNameChoices.
	 * Например categoryChoices.
	 *
	 * @param mixed $attribute
	 * @access private
	 * @return array
	 */
	private function getAttributeChoices ( $attribute ) {
		$data = array();
		$choicesName = (string) $attribute . 'Choices';
		if ( isset( $this->model->$choicesName ) && is_array( $this->model->$choicesName ) )
			$data = $this->model->$choicesName;

		return $data;
	}

	public function getModelNamePlural ( $model ) {
		if ( is_string( $model ) )
			$model = new $model;

		if ( isset( $model->adminName ) )
			return $model->adminName;
		else
			return get_class( $model );
	}

	public function getObjectPluralName ( $model, $pos = 0 ) {
		if ( is_string( $model ) )
			$model = new $model;

		if ( !isset( $model->pluralNames ) )
			return get_class( $model );
		else
			return $model->pluralNames[$pos];
	}

	/**
	 * @return string the base URL that contains all published asset files.
	 */
	public function getAssetsUrl () {
		if ( $this->_assetsUrl === null )
			$this->_assetsUrl = Yii::app()->getAssetManager()->publish( Yii::getPathOfAlias( 'application.modules.yiiadmin.assets' ) );
		return $this->_assetsUrl;
	}

	/**
	 * @param string the base URL that contains all published asset files.
	 */
	public function setAssetsUrl ( $value ) {
		$this->_assetsUrl = $value;
	}

	public static function createActionUrl ( $action, $pk ) {
		$a = new CController;
		return $a->createUrl( 'manageModel', $data->primaryKey );
	}

	public static function t ( $message, $params = array() ) {
		return Yii::t( 'YiiadminModule.yiiadmin', $message, $params );
	}

	public function beforeControllerAction ( $controller, $action ) {
		if ( parent::beforeControllerAction( $controller, $action ) ) {
			$this->controller = $controller;
			$route = $controller->id . '/' . $action->id;

			$publicPages = array(
				'default/login',
				'default/error',
			);
			if ( $this->password !== false && Yii::app()->user->isGuest && !in_array( $route, $publicPages ) ) {
				Yii::app()->user->loginRequired();
			}
			else {
				return true;
			}
		}
		return false;
	}

	public static function getModelAttributes ( $model ) {
		$attrs = array();
		foreach ( $model->tableSchema->columns as $column ) {
			if ( !$column->isPrimaryKey ) {
				$attrs[] = $column->name;
			}
		}
		return array_filter( array_unique( array_map( 'trim', $attrs ) ) );
	}

	public static function assignFileFields ( $model ) {
		foreach ( $model->tableSchema->columns AS $attr ) {
			$attr = $attr->name;
			$validators = $model->getValidators( $attr );
			foreach ( $validators AS $validator ) {
				if ( $validator instanceof CFileValidator ) {
					$model->$attr = CUploadedFile::getInstance( $model, $attr );
				}
			}
		}
	}

	public static function getRelatedModels ( $model ) {
		$relatedModelsAry = $model->relations();
		$relationsToSave = array();
		foreach ( $relatedModelsAry AS $relName => $relData ) {
			if ( $relData[0] == 'CStatRelation' ) {
				continue;
			}
			$relatedModel = $model->getRelated( $relName );
			if ( !$relatedModel ) {
				$relatedModel = new $relData[1];
			}

			if ( !is_array( $relatedModel ) ) {
				$relatedModel = array( $relatedModel );
			}
			$relationsToSave[$relName] = $relatedModel;
		}
		return $relationsToSave;
	}

	/**
	 * return db component for yiiadmin module
	 * @return object
	 */
	public static function getDbConnection () {
		$db = Yii::app()->getModule( 'yiiadmin' )->db;
		return Yii::createComponent( $db );
	}

	/**
	 * return toggle images for model. Using for toggle column and ajax changes
	 * @param $model object instanceof CActiveRecord
	 * @param $attribute string name of attribute field
	 * @return array images for yes and no toggle. Return default images if not setted in model
	 */

	public function getToggleImages ( $model, $attribute ) {
		$images = $titles = $filter = '';

		$gridConfig = $model->adminSearch();
		foreach ( $gridConfig['columns'] AS $column ) {
			if ( isset( $column['class'] ) && isset( $column['name'] ) && $column['class'] == 'DToggleColumn' && $column['name'] == $attribute ) {
				$images = $column['images'];
				$titles = $column['titles'];
				$filter = $column['filter'];
			}
		}
		if ( !$images ) {
			$images = array(
				0 => Yii::app()->getModule( 'yiiadmin' )->getAssetsUrl() . '/img/icons/icon-no.png',
				1 => Yii::app()->getModule( 'yiiadmin' )->getAssetsUrl() . '/img/icons/icon-yes.png',
			);
		}

		if ( !$titles ) {
			if ( !$filter ) {
				$titles = array( 0 => 'Not active', 1 => 'Active' );
			}
			else {
				$titles = $filter;
			}
		}
		return array( $images, $titles );
	}
}
