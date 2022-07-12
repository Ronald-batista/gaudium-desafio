<?php
/* @var $this PassageiroController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Passageiros',
);

$this->menu=array(
	array('label'=>'Create Passageiro', 'url'=>array('create')),
	array('label'=>'Manage Passageiro', 'url'=>array('admin')),
);
?>

<h1>Passageiros</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
