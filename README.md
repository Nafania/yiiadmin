<h3>Instalation</h3>
<ul>
    <li>Clone this repo in protected/modules/yiiadmin.(git clone git@github.com:Nafania/yiiadmin.git)</li>
    <li>
        Edit yii main config file protected/config/main.php. 
        <br/>Enable yiimodule, set password and enter models you want to manage.

        <pre>
        'modules'=>array(
         ...
			'yiiadmin' => array(
				'password'=>'PASSWORD',
				'registerModels'=>array(
					'application.models.*',
				),
				'db' => array(
					'class'=>'CDbConnection',
					'connectionString'=>'sqlite:protected/modules/yiiadmin/data/yiiadmin.db',
				),        
			),
        ),
        ...
        </pre>

    </li>
    <li>Open in browser http://your-project/index.php/yiiadmin and enter your password.</li>
</ul>

<h3>Auto-generated fields based on validators</h3>
<p>There are some auto generated fields based on validators:
<ul>
	<li>CDateValidator will return calendar widget</li>
	<li>CFileValidator will return file input field</li>
	<li>CBooleanValidator will return checkbox field</li>
</ul>
so you don't need to setup them manually
</p>

<h3>Auto generated relation models</h3>
<p>If you have some realtions in your model, they will be auto included to edit or add forms</p>

<h3>Admin scenarios</h3>
<p>There some special scenarios in yii admin.
<ul>
	<li>adminUpdate will set when updating model</li>
	<li>adminSearch will set when searching model</li>
	<li>adminCreate will set when creating model</li>
	<li>adminToggle will set when using toggle function of model (see below)</li>
</ul>
</p>

<h3>Special columns</h3>
<p>
<ul>
	<li>ESortableColumn, column which enable drag&drop sorting of grid, example:
		<pre>            
		//column class
		'class' => 'ESortableColumn',
		//name of column
        'name' => 'catOrder',
		//sort attribute
        'value' =>  '$data->catOrder',
		</pre>
	</li>
	
	<li>EImageColumn (see https://github.com/yiiext/zii-image-column/blob/master/EImageColumn.php), column which allow set image for row of grid, example:
		<pre>          
			//column class		
			'class' => 'EImageColumn',
			//name of column
			'name' => 'image',			
			//expression to get image
			'imagePathExpression' => '$data->getImage()',
		</pre>
	</li>	
	
	<li>EIconColumn, allow to insert some icons from yiiadmin, full list of icons see in assets\img\icons dir, example:
		<pre>            
			//column class	
			'class' => 'EIconColumn',
			//name of column
			'name' => 'video',
			//expression to set icon (may be a single icon without expression), icon name it's all after icon- in filename if icons
			'value' => '( $data->video ? "yes" : "no" )',
		</pre>
	</li>	
	
	<li>DToggleColumn, allow toggle status of model:
		<pre>            
			//column class
			'class' => 'DToggleColumn',
			//name of column
			'name' => 'moderated',
			//confirmation, which show when toggle clicked
			'confirmation' => 'Are you sure',
			//filter data
			'filter' => array( self::MODERATED => Yii::t( 'Site', 'Moderated' ), self::NOT_MODERATED => Yii::t( 'Site', 'Not moderated' ) ),
			//icons, will be set automaticly if you will not setup them
			'icons' => array( self::MODERATED => 'img/yes.png', self::NOT_MODERATED => 'img/no.png' ),
			//titles, will be set automaticly based on filter values, if you will not setup them
			'titles' => array( self::MODERATED => Yii::t( 'Site', 'Moderated' ), self::NOT_MODERATED => Yii::t( 'Site', 'Not moderated' ) ),
		</pre>
	</li>	
</ul>
</p>

Example model config from my testing project:
<pre>
    // ./application/models/Contests.php
    // Contests model file

    // Model plural names
    public $adminName='Contests'; // will be displayed in main list
    public $pluralNames=array('Contest','Contests');   

    // Config for attribute widgets
    public function attributeWidgets()
    {
        return array(
            array('proffesion_id', 'dropDownList'), // For choices create variable name proffesion_idChoices
            array('date_start','calendar', 'language'=>'ru','options'=>array('dateFormat'=>'yy-mm-dd')),
            array('date_stop','calendar'),
            array('active','boolean'),
        );
    }

    // Config for CGridView class
    public function adminSearch()
    {
        return array(
                // Data provider, by default is "search()"
                //'dataProvider'=>$this->search(),
                'columns'=>array(
                'id',
                'name',
                'date_start',
                'date_stop',
                array(
                    'name'=>'active',
                    'value'=>'$data->active==1 ? CHtml::encode("Yes") : CHtml::encode("No")',
                    'filter'=>array(1=>'Да',0=>'Нет'),
                ),
                array(
                    'name'=>'proffesion_id',
                    'value'=>'User_profile::getProfName($data->proffesion_id)',
                    'filter'=>$this->proffesion_idChoices,
                ),
            ),
        );
    }
</pre>
