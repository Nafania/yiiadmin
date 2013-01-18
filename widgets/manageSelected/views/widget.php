<div class="module grid-view">
    <ul class="submit-row" style="margin-left: 10px">
        <li class="left submit-button-container"><a class="selectAll cancel-link"><?php echo Yii::t( 'Site', 'Select All' )?></a></li>
        <li class="left submit-button-container"><a class="selectNone cancel-link"><?php echo Yii::t( 'Site', 'Select None' )?></a></li>

	    <?php
	    foreach ( $this->buttons AS $buttonData ) {
		    echo '<li class="left submit-button-container"><a class="' . $buttonData['class'] . '-link submit-link" href="' . $buttonData['url'] . '">' . Yii::t( 'main', $buttonData['title'] ) . '</a></li>';
	    }
		?>
    </ul>	
    <br clear="all" />
</div>