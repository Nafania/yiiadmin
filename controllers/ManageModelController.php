<?php
/**
 * ManageModelController
 *
 * @uses YAdminController
 * @package yiiadmin
 * @version $id$
 * @copyright 2010 firstrow@gmail.com
 * @author Firstrow <firstrow@gmail.com>
 * @license BSD
 */

/**
 * Управление данными модели. Вывод, редактирование, удаление.
 **/
class ManageModelController extends YAdminController {

	public function filters () {
		return array(
			'ajaxOnly + toggle, reorder',

		);
	}

	/**
	 * Вывод списка записей модели.
	 *
	 * @access public
	 * @return void
	 */
	public function actionList ( $model_name ) {
		Yii::import( 'yiiadmin.extensions.yiiext.zii.widgets.grid.*' );

		$model = $this->module->loadModel( $model_name, 'adminSearch' );

		$model->unsetAttributes();

		if ( isset( $_GET[get_class( $model )] ) ) {
			$model->setAttributes( $_GET[get_class( $model )] );
		}

		$this->breadcrumbs = array(
			$this->module->getModelNamePlural( $model ),
		);

		$data1 = $model->adminSearch();

		$url_prefix = 'Yii::app()->createUrl("yiiadmin/manageModel/';
		$assetesUrl = Yii::app()->getModule( 'yiiadmin' )->getAssetsUrl();

		$data2 = array(
			'id' => 'objects-grid',
			'dataProvider' => $model->search(),
			'filter' => $model,
			'ajaxUrl' => Yii::app()->createUrl( 'yiiadmin/manageModel/list', array( 'model_name' => $model_name ) ),
			'columns' => array(
				array(
					'class' => 'YiiAdminButtonColumn',
					'updateButtonImageUrl' => $assetesUrl . '/img/admin/icon_changelink.gif',
					'updateButtonUrl' => $url_prefix . 'update",array("model_name"=>"' . get_class( $model ) . '","pk"=>$data->primaryKey))',

					'deleteButtonImageUrl' => $assetesUrl . '/img/admin/icon_deletelink.gif',
					'deleteButtonUrl' => $url_prefix . 'delete",array("model_name"=>"' . get_class( $model ) . '","pk"=>$data->primaryKey))',
					'viewButtonUrl' => $url_prefix . 'view",array("model_name"=>"' . get_class( $model ) . '","pk"=>$data->primaryKey))',
					'viewButtonOptions' => array(
						'style' => 'display:none;',
					),
				),
			),
		);

		$listData = array_merge_recursive( $data1, $data2 );

		Ajax::renderAjax( 'list_objects', array(
			'title' => $this->module->getModelNamePlural( $model ),
			'model' => $model,
			'listData' => $listData,
		));
	}

	public function actionToggle () {
		$pk = Yii::app()->getRequest()->getParam( 'pk', array() );
		$model_name = Yii::app()->getRequest()->getParam( 'model_name', '' );
		$attribute = Yii::app()->getRequest()->getParam( 'attribute', '' );
		$val = Yii::app()->getRequest()->getParam( 'val', '' );

		$models = $this->module->loadModel( $model_name, 'adminToggle' )->findAllByPk( $pk );
		$errors = array();
		foreach ( $models AS $model ) {
			if ( $val !== '' ) {
				$model->attributes = array( $attribute => $val );
			}
			else {
				$model->attributes = array( $attribute => (int) !$model->$attribute );
			}
			if ( !$model->save() ) {
				$errors[] = implode( ', ', $model->getErrors());
			}
		}
		list( $images, $titles ) = Yii::app()->getModule( 'yiiadmin' )->getToggleImages( $model, $attribute );


		if ( $errors ) {
			Ajax::send(
				'error',
				YiiadminModule::t( 'При сохранении возникли ошибки: {errors}', array( '{errors}' => implode( ', ', $errors ) ) ),
				array(
					'image' => $images[!$model->$attribute],
					'imageTitle' => $titles[!$model->$attribute],
				)
			);
		}
		else {
			Ajax::send(
				'success',
				YiiadminModule::t( 'Запись сохранена' ),
				array(
					'image' => $images[$model->$attribute],
					'imageTitle' => $titles[$model->$attribute],
				)
			);
		}
	}

	/**
	 * Создание новой записи.
	 *
	 * @access public
	 * @return void
	 */
	public function actionCreate () {
		$model_name = Yii::app()->request->getQuery( 'model_name' );
		$model = $this->module->loadModel( $model_name, 'adminCreate' );

		$relationsToSave = $this->module->getRelatedModels( $model );

		if ( Yii::app()->request->isPostRequest ) {
			if ( isset( $_POST[$model_name] ) ) {
				$model->attributes = $_POST[$model_name];

				YiiadminModule::assignFileFields( $model );
			}

			if ( $model->validate() ) {
				$model->save( false );
				Yii::app()->user->setFlash( 'flashMessage', YiiadminModule::t( 'Запись создана.' ) );
				$this->redirectUser( $model_name, $model->primaryKey );
			}
		}

		$attributes = YiiadminModule::getModelAttributes( $model );

		$title = YiiadminModule::t( 'Создать' ) . ' ' . $this->module->getObjectPluralName( $model, 0 );
		$this->breadcrumbs = array(
			$this->module->getModelNamePlural( $model ) => $this->createUrl( 'manageModel/list', array( 'model_name' => $model_name ) ),
			$title
		);

		$this->render( 'create', array(
			'title' => $title,
			'model' => $model,
			'attributes' => $attributes,
			'relatedModels' => $relationsToSave
		) );
	}

	public function actionUpdate () {
		$model_name = Yii::app()->request->getQuery( 'model_name' );
		$pk = Yii::app()->request->getQuery( 'pk' );
		$model = $this->module->loadModel( $model_name, 'adminUpdate' )->findByPk( $pk );

		if ( !$model ) {
			throw new CHttpException( 404 );
		}

		$relationsToSave = $this->module->getRelatedModels( $model );

		if ( Yii::app()->request->isPostRequest ) {
			Yii::import( 'yiiadmin.extensions.yiiext.WithRelatedBehavior.*' );

			$model->attachBehavior( 'withRelated', new WithRelatedBehavior() );

			if ( isset( $_POST[$model_name] ) ) {
				$model->attributes = $_POST[$model_name];
				YiiadminModule::assignFileFields( $model );
			}

			foreach ( $relationsToSave AS $relationName => $relatedModelsAry ) {

				$model->$relationName = array();

				foreach ( $relatedModelsAry AS $num => $relatedModel ) {
					$relatedModelName = get_class( $relatedModel );

					if ( isset( $_POST[$relatedModelName][$num] ) && sizeof( array_filter( $_POST[$relatedModelName][$num] ) ) ) {

						$relatedModel->attributes = $_POST[$relatedModelName][$num];
						YiiadminModule::assignFileFields( $relatedModel );

						$model->$relationName = CMap::mergeArray( $model->$relationName, array( $relatedModel ) );
					}
					else {
						unset( $_POST[$relatedModelName][$num] );
						if ( is_array( $_POST[$relatedModelName] ) && !sizeof( array_filter( $_POST[$relatedModelName] ) ) ) {
							unset( $relationsToSave[$relationName] );
						}
					}
				}
			}
			if ( $model->withRelated->save( true, array_keys( $relationsToSave ) ) ) {
				$model->detachBehavior( 'withRelated' );
				Yii::app()->user->setFlash( 'flashMessage', YiiadminModule::t( 'Изменения сохранены.' ) );
				$this->redirectUser( $model_name, $model->primaryKey );
			}
			else {
				$relationsToSave = $this->module->getRelatedModels( $model );
			}

		}

		$attributes = YiiadminModule::getModelAttributes( $model );

		$title = YiiadminModule::t( 'Редактировать' ) . ' ' . $this->module->getObjectPluralName( $model, 0 );
		$this->breadcrumbs = array(
			$this->module->getModelNamePlural( $model ) => $this->createUrl( 'manageModel/list', array( 'model_name' => $model_name ) ),
			$title
		);

		$this->render( 'create', array(
			'title' => YiiadminModule::t( 'Редактировать' ) . ' ' . $this->module->getObjectPluralName( $model, 0 ),
			'model' => $model,
			'attributes' => $attributes,
			'relatedModels' => $relationsToSave
		) );
	}

	public function actionDelete () {
		$model_name = Yii::app()->request->getQuery( 'model_name' );
		$pk = Yii::app()->request->getQuery( 'pk' );

		$this->module->loadModel( $model_name, 'adminDelete' )->findByPk( $pk )->delete();

		if ( Yii::app()->request->getIsAjaxRequest() ) {
		}
		else {
			$this->redirect( $this->createUrl( 'manageModel/list', array( 'model_name' => $model_name ) ) );
		}
	}

	public function actionDeleteSelected () {
		$model_name = Yii::app()->getRequest()->getPost( 'model_name', '' );
		$selectedItems = Yii::app()->getRequest()->getPost( 'pk', array() );

		if ( $selectedItems ) {
			$model = new $model_name;
			$pK = $model->getPrimaryKey();

			$keys = array();
			if ( is_array( $pK ) ) {
				foreach ( $selectedItems AS $i => $item ) {
					$items = explode( ',', $item );
					$j = 0;
					foreach ( $pK AS $key => $_x ) {
						$keys[$i][$key] = $items[$j];
						++$j;
					}
				}
			}
			else {
				$keys = $selectedItems;
			}

			$models = $this->module->loadModel( $model_name, 'adminDelete' )->findAllByPk( $keys );
			//use findByPk - foreach - delete here instead of deleteByPk, cause want to afterDelete call.
			foreach ( $models AS $model ) {
				$model->delete();
			}

			if ( !Yii::app()->request->getIsAjaxRequest() ) {
				$this->redirect( $this->createUrl( 'manageModel/list', array( 'model_name' => $model_name ) ) );
			}
		}
	}

	public function actionReOrder () {

		$model = Yii::app()->getRequest()->getPost( 'model', '' );
		$orderField = Yii::app()->getRequest()->getPost( 'orderField', '' );
		$pk = Yii::app()->getRequest()->getPost( 'pk', array() );

		if ( !$model || !$orderField || !sizeof( $pk ) ) {
			throw new CHttpException( 404 );
		}

		$order = 0;
		foreach ( $pk AS $_pk ) {
			$model::model()->updateByPk( $_pk, array( $orderField => $order ) );
			++$order;
		}

		Yii::app()->end();
	}

	/**
	 * Redirect after editing model data.
	 *
	 * @param string $model_name
	 * @param integer $pk
	 * @access protected
	 * @return void
	 */
	protected function redirectUser ( $model_name, $pk ) {
		if ( isset( $_POST['_save'] ) )
			$this->redirect( $this->createUrl( 'manageModel/list', array( 'model_name' => $model_name ) ) );
		if ( isset( $_POST['_addanother'] ) ) {
			Yii::app()->user->setFlash( 'flashMessage', YiiadminModule::t( 'Изменения сохранены. Можете создать новую запись.' ) );
			$this->redirect( $this->createUrl( 'manageModel/create', array( 'model_name' => $model_name ) ) );
		}
		if ( isset( $_POST['_continue'] ) )
			$this->redirect( $this->createUrl( 'manageModel/update', array( 'model_name' => $model_name, 'pk' => $pk ) ) );
	}
}
