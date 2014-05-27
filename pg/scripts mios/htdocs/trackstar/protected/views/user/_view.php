<?php
/* @var $this UserController */
/* @var $data User */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('ID')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->ID), array('view', 'id'=>$data->ID)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('USERNAME')); ?>:</b>
	<?php echo CHtml::encode($data->USERNAME); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('EMAIL')); ?>:</b>
	<?php echo CHtml::encode($data->EMAIL); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('PASSWORD')); ?>:</b>
	<?php echo CHtml::encode($data->PASSWORD); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('LAST_LOGIN_TIME')); ?>:</b>
	<?php echo CHtml::encode($data->LAST_LOGIN_TIME); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CREATE_TIME')); ?>:</b>
	<?php echo CHtml::encode($data->CREATE_TIME); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('CREATE_USER_ID')); ?>:</b>
	<?php echo CHtml::encode($data->CREATE_USER_ID); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('UPDATE_TIME')); ?>:</b>
	<?php echo CHtml::encode($data->UPDATE_TIME); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('UPDATE_USER_ID')); ?>:</b>
	<?php echo CHtml::encode($data->UPDATE_USER_ID); ?>
	<br />

	*/ ?>

</div>