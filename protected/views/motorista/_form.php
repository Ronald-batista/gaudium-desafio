<?php
/* @var $this MotoristaController */
/* @var $model Motorista */
/* @var $form CActiveForm */
?>

<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'motorista-form',
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableAjaxValidation' => false,
	)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'nome'); ?>
		<?php echo $form->textField($model, 'nome', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'nome'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'email'); ?>
		<?php echo $form->textField($model, 'email', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'telefone'); ?>
		<?php
		$this->widget('CMaskedTextField', array(
			'model' => $model,
			'attribute' => 'telefone',
			'mask' => '+99-99-99999-9999',
			'htmlOptions' => array('size' => 20, 'maxlength' => 13, 'placeholder' => '+99-99-999999999',)
		));
		?>

		<?php echo $form->error($model, 'telefone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'status'); ?>
		<?php echo $form->textField($model, 'status', array('size' => 1, 'maxlength' => 1)); ?>
		<?php echo $form->error($model, 'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'data ultima modificação'); ?>
		<?php
		date_default_timezone_set('America/Sao_Paulo');
		echo $form->textField($model, 'data', array('value' => date('d/h/Y - g:i a'), 'readonly' => true));
		?>
		<?php echo $form->error($model, 'data'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'placa'); ?>

		<?php
		// $this->widget('CMaskedTextField', array(
		// 	'model' => $model,
		// 	'attribute' => 'placa',
		// 	'mask' => 'AAA-9999',
		// 	'htmlOptions' => array('size' => 15, 'maxlength' => 8, 'placeholder'=>'AAA-9999',)
		// )); 
		// $this->widget("ext.maskedInput.MaskedInput", array(
		// 	"model" => $model,
		// 	"attribute" => "placa",
		// 	"mask" => "AAA9{1}A9{2}",
		// ));
		$this->widget("ext.maskedInput.MaskedInput", array(
			"model" => $model,
			"attribute" => "placa",
			"mask" => '[ (AAA9A99)|(AAA-9999) ]', //must use quotation when defining an array plugin requires this way /
			"definitions"=>array(
				// "X"=>array(
				// 	"validator"=>"[xX]",
				// 	"cardinality"=>1,
				// 	"casing"=>"upper"
				// )
		)));

		
		?>

		<?php echo $form->error($model, 'placa'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'observacao'); ?>
		<?php echo $form->textField($model, 'observacao', array('size' => 60, 'maxlength' => 200)); ?>
		<?php echo $form->error($model, 'observacao'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->