<?php
/* @var $this IssueController */
/* @var $data Issue */
?>

<div class="view">

	<div class="view">
<b><?php echo CHtml::encode($data->getAttributeLabel('NAME')); ?>:</b>

<?php echo CHtml::link(CHtml::encode($data->NAME), array('issue/view', 'id'=>$data->ID)); ?>

<br />


	<b><?php echo CHtml::encode($data->getAttributeLabel('DESCRIPTION')); ?>:</b>
	<?php echo CHtml::encode($data->DESCRIPTION); ?>
	<br />


<b>
	<?php echo CHtml::encode($data->getAttributeLabel('TYPE_ID'));?>:
</b>

<?php echo CHtml::encode($data->getTypeText()); ?>

<br />

<b>
	<?php echo CHtml::encode($data->getAttributeLabel('STATUS_ID'));?>:
</b>

<?php echo CHtml::encode($data->getStatusText()); ?>

</div>

</div>