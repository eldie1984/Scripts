<?php
/* @var $this EjecucionesController */

$this->breadcrumbs=array(
	'Ejecuciones',
);
?>
<h1>Hosts</h1>

<?php  $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>Ejecuciones::model()->getHostOnly(),
	'itemView'=>'_view',
	'enablePagination' => false,
	//, array(

    //'dataProvider'=>Ejecuciones::model()->getHostOnly(),
    //'enablePagination' => false,
    //'columns'=>array(
    //    'HOST',
     //   array(
      //      'class'=>'CButtonColumn',
       // ),
    //),
)); ?>

