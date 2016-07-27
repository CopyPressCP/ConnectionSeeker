<div class="row">
    <div class="target-rating" id="ratingstar"></div>
    <div id="ratinghint" class="ratinghint"></div><br />
    <div><?php echo nl2br(CHtml::encode($model->notes)); ?></div><br />
    <div><?php echo $model->created; ?> Created by <?php echo $model->rcreatedby->username ?></div>
</div>
