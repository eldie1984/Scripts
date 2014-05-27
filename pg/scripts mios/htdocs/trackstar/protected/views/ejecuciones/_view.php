<?php
/* @var $this EjecucionesController */
/* @var $data Ejecuciones */
?>

<div class="view">

	<div class="view">

<b><?php echo CHtml::encode($data->getAttributeLabel('HOST')); ?>:</b>

<?php echo CHtml::link(CHtml::encode($data->HOST), array('ejecuciones/view', 'host'=>$data->HOST)); ?>
 

<br />

</div>

</div>