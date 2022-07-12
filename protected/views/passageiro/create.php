<?php
/* @var $this PassageiroController */
/* @var $model Passageiro */

$this->breadcrumbs=array(
	'Passageiros'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Passageiro', 'url'=>array('index')),
	array('label'=>'Manage Passageiro', 'url'=>array('admin')),
);
?>

<h1>Create Passageiro</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>