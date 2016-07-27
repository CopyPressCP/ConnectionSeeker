<?php
$this->breadcrumbs=array(
	'Announcement',
);
?>

<h1>New Features</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$model->search(),
	'itemView'=>'/announcement/_view',
)); ?>

<style>
div.announcement{
    padding:0px 15px;
}
div.announcement ul li {
    list-style:none;
}
</style>