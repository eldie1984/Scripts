<?php
/* @var $this IssueController */
/* @var $model Issue */
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
		<?php echo $form->label($model,'NAME'); ?>
		<?php echo $form->textField($model,'NAME',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DESCRIPTION'); ?>
		<?php echo $form->textField($model,'DESCRIPTION',array('size'=>60,'maxlength'=>4000)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'PROJECT_ID'); ?>
		<?php echo $form->textField($model,'PROJECT_ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'TYPE_ID'); ?>
		<?php echo $form->textField($model,'TYPE_ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'STATUS_ID'); ?>
		<?php echo $form->textField($model,'STATUS_ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'OWNER_ID'); ?>
		<?php echo $form->textField($model,'OWNER_ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'REQUESTER_ID'); ?>
		<?php echo $form->textField($model,'REQUESTER_ID'); ?>
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