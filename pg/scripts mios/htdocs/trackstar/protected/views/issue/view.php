<?php
/* @var $this IssueController */
/* @var $model Issue */

$this->breadcrumbs=array(
	'Issues'=>array('index'),
	$model->NAME,
);

$this->menu=array(
	array('label'=>'List Issues', 'url'=>array('index', 'pid'=>$model->project->ID)),
	array('label'=>'Create Issue', 'url'=>array('create', 'pid'=>$model->project->ID)),
	array('label'=>'Update Issue', 'url'=>array('update', 'id'=>$model->ID)),
	array('label'=>'Delete Issue', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->ID),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Issues', 'url'=>array('admin', 'pid'=>$model->project->ID)),
);
?>

<h1>View Issue #<?php echo $model->ID; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
'data'=>$model,
'attributes'=>array(
'ID',
'NAME',
'DESCRIPTION',
array(
'name'=>'type_id',
'value'=>CHtml::encode($model->getTypeText())
),
array(
'name'=>'status_id',
'value'=>CHtml::encode($model->getStatusText())
),
array(
'name'=>'owner_id',
'value'=>isset($model->owner)?CHtml::encode($model->owner->USERNAME):"unknown"
),
array(
'name'=>'requester_id',
'value'=>isset($model->requester)?CHtml::encode($model->requester->USERNAME):"unknown" ),
),
)); ?>
