<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'ID'); ?>
		<?php echo $form->textField($model,'ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'USERNAME'); ?>
		<?php echo $form->textField($model,'USERNAME',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'EMAIL'); ?>
		<?php echo $form->textField($model,'EMAIL',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'PASSWORD'); ?>
		<?php echo $form->passwordField($model,'PASSWORD',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'LAST_LOGIN_TIME'); ?>
		<?php echo $form->textField($model,'LAST_LOGIN_TIME'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'CREATE_TIME'); ?>
		<?php echo $form->textField($model,'CREATE_TIME'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'CREATE_USER_ID'); ?>
		<?php echo $form->textField($model,'CREATE_USER_ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'UPDATE_TIME'); ?>
		<?php echo $form->textField($model,'UPDATE_TIME'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'UPDATE_USER_ID'); ?>
		<?php echo $form->textField($model,'UPDATE_USER_ID'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->