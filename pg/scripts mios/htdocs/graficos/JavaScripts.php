<!-- LIBRARYS JS -->
<script src="../../js/highcharts.js"></script>
<script src="../../js/modules/data.js"></script>
<script src="../../js/modules/drilldown.js"></script>
<script language="javascript">
// FUNCTION TO AGREE A RECOMMENDATION LINE
function AgregarRecommendation() {
		 TextoLinea=prompt('Add recommendation:','');
         document.getElementById("recommendation").innerHTML +="<br><li>"+ TextoLinea + "</li>";  // Funcion para agregar comentario
}
// FUNCTION TO AGREE A TEXT LINE
function AgregarLineaDeTexto() {
		 TextoLinea=prompt('Add comment:','');
         document.getElementById("texto_chat").innerHTML +="<br><li>"+ TextoLinea + "</li>";  // Agrego nueva linea antes
}
function imprimirSelec(nombre)
{
 var ficha = document.getElementById(nombre);//almacenamos en variable los datos del div a imprimir
 var ventimp = window.open('', 'Impresion');//aqui se genera una pagina temporal 
 
 ventimp.document.write( ficha.innerHTML );//aqui cargamos el contenido del div seleccionado
 ventimp.document.close();//cerramos el documento
 ventimp.print( );//enviamos los datos a la impresora
 ventimp.cl
 ose();//cerramos ventana temporal
}
    $(function memoria() {
		$(<?php echo "'#MEM".$host."'";?>).highcharts({
            chart: {
                type: 'area'
            },
            title: {
                text: null
            },
            subtitle: {
                text: null
            },
            xAxis: {
                type: 'datetime',
                maxZoom: 14 * 24 * 3600000, // fourteen days
                title: {
                    text: null
                }
            },
            yAxis: {
                title: {
                    text: 'Memoria MB'
                },
                labels: {
                    formatter: function() {
                        return this.value / 1 +' GB';
                    }
                }
            },
            tooltip: {
                pointFormat: '{series.name} <br> {point.y:,.0f}'
            },
            plotOptions: {
                area: {
                    
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 0,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                }
            },
            series: [{
                name: 'MEM_ALLOC',
				pointInterval: 24 * 3600 * 1000,
                pointStart: <?php echo "Date.UTC(".$anio.", ".$mes.", ".$dia.", 0, 0, 0)"; ?>,
                data: [<?php 
						echo $M_ASIGNED;
						?>]
				}, {
                name: 'MEM_USED',
				pointInterval: 86400000,
                pointStart: <?php echo "Date.UTC(".$anio.", ".$mes.", ".$dia.", 0, 0, 0)"; ?>,
                data: [<?php 
						echo $MEM;
						?>]
            }]
        });
    });
    
</script>