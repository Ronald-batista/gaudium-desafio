<?php
/* @var $this MotoristaController */
/* @var $model Motorista */

$this->breadcrumbs=array(
	'Motoristas'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Motorista', 'url'=>array('index')),
	array('label'=>'Manage Motorista', 'url'=>array('admin')),
);
?>

<h1>Create Motorista</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>