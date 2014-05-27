<?php
/* @var $this ProjectController */
/* @var $data Project */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('ID')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->ID), array('project/view', 'id'=>$data->ID)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('NAME')); ?>:</b>
	<?php echo CHtml::encode($data->NAME); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DESCRIPTION')); ?>:</b>
	<?php echo CHtml::encode($data->DESCRIPTION); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CREATE_TIME')); ?>:</b>
	<?php echo CHtml::encode($data->CREATE_TIME); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CREATE_USER_ID')); ?>:</b>
	<?php echo CHtml::encode($data->CREATE_USER_ID); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('UPDATE_TIME')); ?>:</b>
	<?php echo CHtml::encode($data->UPDATE_TIME); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('UPDATE_USER_ID')); ?>:</b>
	<?php echo CHtml::encode($data->UPDATE_USER_ID); ?>
	<br />


</div>