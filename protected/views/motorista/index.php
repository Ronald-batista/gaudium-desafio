<?php
/* @var $this MotoristaController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Motoristas',
);

$this->menu=array(
	array('label'=>'Create Motorista', 'url'=>array('create')),
	array('label'=>'Manage Motorista', 'url'=>array('admin')),
);
?>

<h1>Motoristas</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
