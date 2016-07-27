<div class="row<?php echo $model->isprivate ? " privateinfo" : ''; ?>">
    <div><?php echo nl2br(CHtml::encode($model->notes)); ?></div>
    <div><?php echo $model->created; ?> Created by <?php echo $model->rcreatedby->username ?></div>
</div>