<?php
/* @var $this IssueController */
/* @var $model Issue */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'issue-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'NAME'); ?>
		<?php echo $form->textField($model,'NAME',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'NAME'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'DESCRIPTION'); ?>
		<?php echo $form->textField($model,'DESCRIPTION',array('size'=>60,'maxlength'=>4000)); ?>
		<?php echo $form->error($model,'DESCRIPTION'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'TYPE_ID'); ?>
		<?php echo $form->dropDownList($model,'TYPE_ID', $model->getTypeOptions()); ?>
		<?php echo $form->error($model,'TYPE_ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'STATUS_ID'); ?>
		<?php echo $form->dropDownList($model,'STATUS_ID', $model->getStatusOptions()); ?>
		<?php echo $form->error($model,'STATUS_ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'OWNER_ID'); ?>
		<?php echo $form->dropDownList($model,'OWNER_ID', $model->project->getUserOptions()); ?>
		<?php echo $form->error($model,'OWNER_ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'REQUESTER_ID'); ?>
		<?php echo $form->dropDownList($model,'REQUESTER_ID', $model->project->getUserOptions()); ?>
		<?php echo $form->error($model,'REQUESTER_ID'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->