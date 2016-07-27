<?php $this->pageTitle=Yii::app()->name; ?>

<?php $roles =  Yii::app()->authManager->getRoles(Yii::app()->user->id);
if(isset($roles['Admin'])){
	header("Location: index.php?r=client");
}
elseif(isset($roles['Marketer'])){
	header("Location: index.php?r=campaign");
}
elseif(isset($roles['Outreach'])){
	header("Location: index.php?r=domain/index&touched=ture");
}
else{
echo "<h1>Welcome to <i>Dashboard</i></h1>";
}
?>


<?php
//phpinfo();
?>
<!-- 
<i><?php echo CHtml::encode(Yii::app()->name); ?></i>
<p>Congratulations! Dashboard Here.</p>

<p>You may change the content of this dashboard page by modifying the following two files:</p>
<ul>
	<li>View file: <tt><?php echo __FILE__; ?></tt></li>
	<li>Layout file: <tt><?php echo $this->getLayoutFile('main'); ?></tt></li>
</ul>
-->