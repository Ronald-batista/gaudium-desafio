<?php
/* @var $this PassageiroController */
/* @var $model Passageiro */

$this->breadcrumbs=array(
	'Motoristas'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Motorista', 'url'=>array('index')),
	array('label'=>'Create Motorista', 'url'=>array('create')),
	array('label'=>'View Motorista', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Motorista', 'url'=>array('admin')),
);
?>

<h1>Update Status Motorista <?php  echo $model->id; ?></h1>

<?php  $this->renderPartial('_status', array('model'=>$model)); ?>