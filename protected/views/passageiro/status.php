<?php
/* @var $this PassageiroController */
/* @var $model Passageiro */

$this->breadcrumbs=array(
	'Passageiros'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Passageiro', 'url'=>array('index')),
	array('label'=>'Create Passageiro', 'url'=>array('create')),
	array('label'=>'View Passageiro', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Passageiro', 'url'=>array('admin')),
	array('label'=>'Status Passageiro', 'url'=>array('status', 'id'=>$model->id)),
);
// ?>

<h1>Update Status Passageiro <?php  echo $model->id; ?></h1>

<?php $this->renderPartial('_status', array('model'=>$model)); ?>