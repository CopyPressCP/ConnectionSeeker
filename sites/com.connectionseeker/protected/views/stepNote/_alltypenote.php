<div><h4>Idea Notes:</h4>
    <?php
    if (!empty($notes["type1"])) {
        foreach ($notes["type1"] as $row) { ?>
        <div class="row">
            <div><?php echo nl2br(CHtml::encode($row->notes)); ?></div>
            <div><?php echo $row->created; ?> Created by <?php echo $row->rcreatedby->username ?></div>
        </div>
    <?php }
    }?>
</div>
<div><h4>Writer Notes:</h4>
    <?php
    if (!empty($notes["type2"])) {
        foreach ($notes["type2"] as $row) { ?>
        <div class="row">
            <div><?php echo nl2br(CHtml::encode($row->notes)); ?></div>
            <div><?php echo $row->created; ?> Created by <?php echo $row->rcreatedby->username ?></div>
        </div>
    <?php }
    }?>
</div>
<div><h4>Extra Writer Notes:</h4>
    <?php
    if (!empty($notes["type3"])) {
        foreach ($notes["type3"] as $row) { ?>
        <div class="row">
            <div><?php echo nl2br(CHtml::encode($row->notes)); ?></div>
            <div><?php echo $row->created; ?> Created by <?php echo $row->rcreatedby->username ?></div>
        </div>
    <?php }
    }?>
</div>
