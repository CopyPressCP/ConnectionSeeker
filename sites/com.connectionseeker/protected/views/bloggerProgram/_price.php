<div class="row">
    <div style="font:italic bold 18px arial,sans-serif;color:red;"><?php echo nl2br(CHtml::encode($model->price)); ?></div>
    <div><?php echo $model->created; ?> Created by <?php echo $model->rcreatedby->username ?></div>
</div>