 <div class="flashes">

	<?php if( Yii::app()->user->hasFlash('MenusSuccess')===true ):?>

	    <div class="flash success">

	        <?php echo Yii::app()->user->getFlash('MenusSuccess'); ?>

	    </div>

	<?php endif; ?>

	<?php if( Yii::app()->user->hasFlash('MenusError')===true ):?>

	    <div class="flash error">

	        <?php echo Yii::app()->user->getFlash('MenusError'); ?>

	    </div>

	<?php endif; ?>

 </div>