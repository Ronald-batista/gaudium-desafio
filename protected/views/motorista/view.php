<?php
/* @var $this MotoristaController */
/* @var $model Motorista */

$this->breadcrumbs=array(
	'Motoristas'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Motorista', 'url'=>array('index')),
	array('label'=>'Create Motorista', 'url'=>array('create')),
	array('label'=>'Update Motorista', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Motorista', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Motorista', 'url'=>array('admin')),
);
?>

<h1>View Motorista #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'nome',
		'email',
		'telefone',
		'status',
		'data',
		'placa',
		'observacao',
	),
)); ?>
