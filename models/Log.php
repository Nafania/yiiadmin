<?php
class Log extends CActiveRecord
{
    public $filters = array();

	/**
	 * Returns the static model of the specified AR class.
	 * @return Tracker the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'log';
	}
	
	public function getDbConnection()
	{
	    return YiiadminModule::getDbConnection();
	}	

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('added, userId', 'numerical', 'integerOnly'=>true),
			array('route, userName, actionParams', 'length', 'max'=>255),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'added' => 'added',
			'userId' => 'userId',
			'route' => 'route',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('added',$this->added);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('route',$this->route);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	protected function beforeSave () {
	    $this->userId = (int) Yii::app()->getUser()->getId();
	    $this->userName = Yii::app()->getUser()->getName();
	    
	    $this->added = time();
	    return true;
	}
	
	public function scopes () {
	    return array(
		'recent' => array(
		    'limit' => 10,
		    'order' => 'added DESC'
		)
	    );
	}
	
	public function addFilter( $filter ) {
	    if ( is_array($filter) ) {
		$this->filters = CMap::mergeArray($this->filters, $filter);
	    }
	    else {
		$this->filters[] = $filter;
	    }
	}
	
	public function addLog () {
	    $this->route = Yii::app()->getController()->getRoute();
	    $this->actionParams = serialize(Yii::app()->getController()->getActionParams());
	    
	    $found = false;

	    foreach ( $this->filters AS $filter ) {
		if ( strpos($this->route, $filter) !== false ) {
		    $found = true;
		}
	    }
	    
	    if ( !$found ) {
		$this->save();
	    }
	}
	
	public function getUserId () {
	    return $this->userId;
	}
	
	public function getUserName () {
	    return $this->userName;
	}	
	
	public function getAdded () {
	    return $this->added;
	}
	
	public function getRoute () {
	    return $this->route;
	}	
	
	public function getUrl () {
	    $params = array();
	    if ( $this->actionParams ) {
		$params = unserialize($this->actionParams);
	    }
	    return Yii::app()->createUrl($this->route, $params);
	}
}