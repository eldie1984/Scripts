set linesize 800
Set pagesize 200


Set markup html on spool on
HEAD <title>Example</title> <style type="text/css"> body {font:10pt Arial,Helvetica,sans-serif; color:black; background:red;} </style>


spool &1



select 'Análisis del ' || sysdate as FECHA from dual
;

Ttitle CENTER "REPORTE Total de avisos publicados ayer " skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT DECODE
          (COUNT (*),
           0, 'No hubo publicacion ayer. Está andando la publicacion?',
           'En SAC_AVISO figuran ' || COUNT (*)
           || ' avisos dados de alta ayer.'
          ) AS publicaciones
  FROM sacar.sac_aviso
 WHERE TRUNC (av_fecha_creacion) = TRUNC (SYSDATE - 1);


Ttitle CENTER "REPORTE Avisos publicados por canal y tipo" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT   av_creado_por, tipo,
            'En SAC_AVISO figuran '
         || COUNT (*)
         || ' avisos tipo '
         || tipo
         || ' dados de alta ayer por '
         || av_creado_por
         || '.' AS publicaciones_por_origen
    FROM sacar.sac_aviso
   WHERE TRUNC (av_fecha_creacion) = TRUNC (SYSDATE - 1)
GROUP BY av_creado_por, tipo
ORDER BY 1, 2;

/*-- Actividad de los avisos el día de ayer*/
/*-- La cantidad de "AVISO ALTA" debería coincidir con la del query*/
/*-- del total de avisos publicados*/

Ttitle CENTER "REPORTE Actividad de los avisos el día de ayer" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT   esav_creado_por evento, COUNT (*)
    FROM sacar.sac_estado_aviso
   WHERE TRUNC (esav_fecha_creacion) = TRUNC (SYSDATE - 1)
GROUP BY esav_creado_por;

/*-- Cantidad de compras en el día de ayer: */
/*-- hubo web y sms? hubo compras inmediatas y subastas?*/

Ttitle CENTER "REPORTE Cantidad de compras en el día de ayer: " skip 1 -
Ttitle OFF

/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT   origen, COUNT (1) cantidadoperaciones, SUM (monto) montocomisiones
    FROM sacar.cce_cargo
   WHERE tipo_cargo_id = 1                                      --COMISION = 1
     AND TRUNC (generacion) = TRUNC (SYSDATE - 1)
GROUP BY origen;


Ttitle CENTER "REPORTE Hubo ofertas en subastas ayer?" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT   tipo tiposubasta, COUNT (1) cantidadofertasensubastas,
         COUNT (DISTINCT subasta_id) cantidadsubastasconofertas
    FROM sacar.sbt_oferta
   WHERE TRUNC (generacion) = TRUNC (SYSDATE - 1)
GROUP BY tipo;

/*-- Hubo pagos de NPS ayer AUTORIZADOS?
-- Hay pagos ANULADOS y ENVIADOS? Eso es inconsistente...*/

Ttitle CENTER "REPORTE Hubo pagos de NPS ayer AUTORIZADOS?" skip 1 -
Ttitle OFF



/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT   estado estadopagonps, estado_envio, origen origenpagonps,
         COUNT (1) cantidadpagos, SUM (monto) montopagos
    FROM sacar.cce_pago_nps
   WHERE TRUNC (generacion) = TRUNC (SYSDATE - 1)
GROUP BY estado, estado_envio, origen
ORDER BY estado, estado_envio, origen;


Ttitle CENTER "REPORTE -- Hay pagos pendientes de NPS hace mas de 3 días?" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT   estado estadopagonps, origen origenpagonps, COUNT (1) cantidadpagos,
         SUM (monto) montopagos
    FROM sacar.cce_pago_nps
   WHERE TRUNC (generacion) < TRUNC (SYSDATE - 3) AND estado = 'PENDIENTE'
GROUP BY estado, origen
ORDER BY estado, origen;

Ttitle CENTER "REPORTE -- Pagos de pago seguro de ayer" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT   estado estadopagodm, estado_envio, COUNT (1) cantidadpagos,
         SUM (monto) montopagos
    FROM sacar.cce_pago_dm
   WHERE TRUNC (generacion) = TRUNC (SYSDATE - 1)
GROUP BY estado, estado_envio
ORDER BY estado, estado_envio;

Ttitle CENTER "REPORTE -- hubo calificaciones ayer? por si y por no?" skip 1 -
Ttitle OFF

/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT   estado, resultado_venta, valoracion, COUNT (*)
    FROM sacar.cal_calificacion cal
   WHERE TRUNC (cal.actualizacion) = TRUNC (SYSDATE - 1)
GROUP BY estado, resultado_venta, valoracion
ORDER BY estado, resultado_venta, valoracion;


Ttitle CENTER "REPORTE -- ayer hubo la misma cantidad de calificaciones que de cargos?" skip 1 -
Ttitle OFF



/* Formatted on 2009/03/30 14:04 (Formatter Plus v4.8.8) */
SELECT calificaciones, cargos_comision
  FROM (SELECT COUNT (*) calificaciones
          FROM sacar.cal_calificacion comprador,
               sacar.cal_calificacion vendedor
         WHERE comprador.origen = 'COMPRADOR'
           AND vendedor.origen = 'VENDEDOR'
           AND comprador.transaccion_id = vendedor.transaccion_id
           AND TRUNC (comprador.generacion) = TRUNC (SYSDATE - 1)),
       (SELECT COUNT (*) cargos_comision
          FROM sacar.cce_cargo
         WHERE TRUNC (generacion) = TRUNC (SYSDATE - 1) AND tipo_cargo_id = 1);


Ttitle CENTER "REPORTE -- Se está grabando el motivo de no realizacion de las transacciones?" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:05 (Formatter Plus v4.8.8) */
SELECT DECODE
          (COUNT (*),
           0, 'Todas las calificaciones de tx no realizadas de ayer tienen motivos de no realizacion',
           'No se está grabando elmotivo de no realizacion de las transacciones!'
          )
  FROM sacar.cal_calificacion cal
 WHERE TRUNC (cal.actualizacion) = TRUNC (SYSDATE - 1)
   AND resultado_venta = 'TRANSACCION NO REALIZADA'
   AND motivo_no_realizacion_id IS NULL;


Ttitle CENTER "REPORTE -- se está guardando el precio en pesos (con la cotizacion del dolar del dia) del aviso?" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:05 (Formatter Plus v4.8.8) */
SELECT   origen, COUNT (*),
         DECODE (COUNT (*),
                 0, 'ok',
                 'No se está granado el precio en pesos de ' || origen
                )
    FROM sacar.cce_cargo
   WHERE aviso_precio_pesos IS NULL
     AND TRUNC (generacion) = TRUNC (SYSDATE - 1)
     AND tipo_cargo_id = 1
GROUP BY origen;

/*-- hay avisos con estado activo cuya fecha de expiracion haya pasado?*/
/*-- funciona el proceso de cierre?*/

Ttitle CENTER "REPORTE hay avisos con estado activo cuya fecha de expiracion haya pasado?" skip 1 -
Ttitle OFF

/* Formatted on 2009/03/30 14:05 (Formatter Plus v4.8.8) */
SELECT DECODE (COUNT (*),
               0, 'El proceso de cierre de avisos funciona',
               'Hay errores con el proceso de cierre de avisos!!!'
              )
  FROM sacar.sac_aviso
 WHERE av_estado = 3 AND av_fecha_expiracion < TRUNC (SYSDATE);

Ttitle CENTER "REPORTE -- Hay subastas con fecha de expiración en el aviso distinta a la fecha de expiración de la subasta?" skip 1 -
Ttitle OFF



/* Formatted on 2009/03/30 14:05 (Formatter Plus v4.8.8) */
SELECT DECODE
          (COUNT (*),
           0, 'Ok. Las fechas de finalizacion de las subastas son consistentes.',
              'Las fechas de finalizacion de '
           || COUNT (*)
           || ' subastas son inconsistentes.'
          )
  FROM sacar.sac_aviso a, sacar.sbt_subasta s
 WHERE a.tipo = 'SUBASTA'
   AND a.av_id = s.aviso_id
   AND a.av_fecha_expiracion <> s.finalizacion
   AND estado <> 'FINALIZADA';

Ttitle CENTER " REPORTE -- Hay usuarios suspendidos graves con publicaciones activas?" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:05 (Formatter Plus v4.8.8) */
SELECT u.usuario_id, u.usu_apodo, u.tius_id
  FROM sacar.sac_usuario u
 WHERE EXISTS (
          SELECT 1
            FROM sacar.crm_suspension_usuario su
           WHERE su.usuario_id = u.usuario_id
             AND tipo_suspension = 'GRAVE'
             AND activa = 'Y'
             AND vigencia IS NULL)
   AND EXISTS (SELECT 1
                 FROM sacar.sac_aviso a
                WHERE a.usuario_id = u.usuario_id AND a.av_estado = 3);

Ttitle CENTER "REPORTE -- hubo logins ayer?" skip 1 -
Ttitle OFF

select to_char(creacion,'hh24') hora,
        decode(count(*),
            0,'Anda el login? No se logueó nadie',
            count(*)||' logins ok') status
from sacar.SAC_REG_USRIP
where trunc(creacion) = trunc(sysdate-1)
group by to_char(creacion,'hh24')
order by to_char(creacion,'hh24')
;

Ttitle CENTER "REPORTE -- existen usuarios que se loguean multiples veces en un dia? en un numero "normal"?" skip 1 -
Ttitle OFF

/* Formatted on 2009/03/30 14:05 (Formatter Plus v4.8.8) */
SELECT   usuario_ip, usuario_id, COUNT (*)
    FROM sacar.sac_reg_usrip
   WHERE TRUNC (creacion) = TRUNC (SYSDATE - 1)
GROUP BY usuario_ip, usuario_id
  HAVING COUNT (*) > 20;

Ttitle CENTER "REPORTE -- hubo registraciones ayer?" skip 1 -
Ttitle OFF

/* Formatted on 2009/03/30 14:05 (Formatter Plus v4.8.8) */
SELECT DECODE (COUNT (*),
               0, 'No hubo registraciones ayer. Funciona la registración?',
               'Ayer hubo ' || COUNT (*) || ' registraciones exitosas.'
              )
  FROM sacar.sac_usuario
 WHERE TRUNC (usu_fecha_ingreso) = TRUNC (SYSDATE - 1);


/*-- No tengo en cuenta las busquedas por vendedor */
Ttitle CENTER "REPORTE -- Por qué buscan los usuarios? Categoria y/o texto?" skip 1 -
Ttitle OFF


(SELECT 'BUSQUEDAS POR TEXTO', COUNT (*)
   FROM sac_log_busqueda
  WHERE lobu_fecha_inicio IS NOT NULL
    AND TRUNC (lobu_fecha_creacion) = TRUNC (SYSDATE - 1)
    AND lobu_titulo IS NOT NULL
    AND lobu_categoria_id IS NULL
    AND lobu_vendedor_id IS NULL)
UNION
(SELECT 'BUSQUEDAS POR CATEGORIA', COUNT (*)
   FROM sac_log_busqueda
  WHERE lobu_fecha_inicio IS NOT NULL
    AND TRUNC (lobu_fecha_creacion) = TRUNC (SYSDATE - 1)
    AND lobu_titulo IS NULL
    AND lobu_categoria_id IS NOT NULL
    AND lobu_vendedor_id IS NULL)
UNION
(SELECT 'BUSQUEDAS POR CATEGORIA Y TEXTO', COUNT (*)
   FROM sac_log_busqueda
  WHERE lobu_fecha_inicio IS NOT NULL
    AND TRUNC (lobu_fecha_creacion) = TRUNC (SYSDATE - 1)
    AND lobu_titulo IS NOT NULL
    AND lobu_categoria_id IS NOT NULL
    AND lobu_vendedor_id IS NULL);

/*-- usuarios registrados ayer con nombre igual a su apellido*/
/*-- son usuarios truchos?*/

Ttitle CENTER "REPORTE -- usuarios registrados ayer con nombre igual a su apellido" skip 1 -
Ttitle OFF

/* Formatted on 2009/03/30 14:06 (Formatter Plus v4.8.8) */
SELECT usu_apodo, usu_nombre, usu_apellido
  FROM sacar.sac_usuario u, sacar.sac_usuario_registro ur
 WHERE TRUNC (u.usu_fecha_ingreso) = TRUNC (SYSDATE - 1)
   AND u.usuario_id = ur.usuario_id
   AND estado_registro_id = 2
   AND TRIM (u.usu_apellido) = TRIM (u.usu_nombre);


Ttitle CENTER "REPORTE -- reporte de GNR_LOG" skip 1 -
Ttitle OFF

/* Formatted on 2009/03/30 14:06 (Formatter Plus v4.8.8) */
SELECT *
  FROM sacar.gnr_log
 WHERE nivel = 'ERROR' AND TRUNC (generacion) = TRUNC (SYSDATE - 1);


Ttitle CENTER "REPORTE -- reporte de archivador" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:06 (Formatter Plus v4.8.8) */
SELECT *
  FROM sacar_h.historico_proceso
 WHERE TRUNC (fecha) = TRUNC (SYSDATE - 1);


Ttitle CENTER "REPORTE -- consistencia de bsq_busqueda" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:06 (Formatter Plus v4.8.8) */
SELECT 'Hay ' || COUNT (*)
       || ' avisos inactivos en el resultado de busqueda!!!'
  FROM sacar.bsq_busqueda b, sacar.sac_aviso a, sacar.bsq_actualizacion ba
 WHERE b.aviso_id = a.av_id
   AND b.actualizacion_id = ba.ID
   AND ba.estado = 'ACTIVA'
   AND a.av_estado <> 3;


Ttitle CENTER "REPORTE -- consistencia de bsq_busqueda" skip 1 -
Ttitle OFF


/* Formatted on 2009/03/30 14:06 (Formatter Plus v4.8.8) */
SELECT    'Hay '
       || COUNT (*)
       || ' avisos activos que faltan en el resultado de busqueda!!!'
  FROM sacar.sac_aviso a
 WHERE av_estado = 3 AND NOT EXISTS (SELECT 1
                                       FROM sacar.bsq_busqueda b
                                      WHERE b.aviso_id = a.av_id);


Ttitle CENTER "REPORTE -- usuarios que anulan todo!" skip 1 -
Ttitle OFF

/* Formatted on 2009/03/30 14:06 (Formatter Plus v4.8.8) */
/* Formatted on 2009/04/07 09:58 (Formatter Plus v4.8.8) */
SELECT   usuario.usu_apodo,
         DECODE (usuario.tius_id, 1, 'Broker', 2, 'Particular') tipousuario,
         usuario.usuario_id, generado.monto monto_generado,
         generado.cant cantidad_generada, anulado.monto monto_anulado,
         anulado.cant cantidad_anulada, anulado.monto / generado.monto pmonto,
         anulado.cant / generado.cant pcant
    FROM (SELECT   cuenta_id, SUM (monto) monto, SUM (aviso_cantidad) cant
              FROM sacar.cce_cargo
             WHERE TRUNC (generacion) > TRUNC (SYSDATE - 30)
          GROUP BY cuenta_id
            HAVING SUM (monto) > 0) generado,
         (SELECT   cuenta_id, SUM (monto) monto, SUM (aviso_cantidad) cant
              FROM sacar.cce_cargo
             WHERE TRUNC (generacion) > TRUNC (SYSDATE - 30)
               AND estado = 'ANULADO'
            HAVING SUM (monto) > 0
          GROUP BY cuenta_id) anulado,
         sacar.sac_usuario usuario,
         sacar.cce_cuenta cuenta
   WHERE generado.cuenta_id = anulado.cuenta_id
     AND anulado.cant / generado.cant > 0.5
     AND generado.cant > 5
     AND generado.cuenta_id = cuenta.ID
     AND cuenta.usuario_id = usuario.usuario_id
ORDER BY anulado.cant / generado.cant DESC,
         anulado.monto / generado.monto DESC,
         anulado.monto DESC,
         anulado.cant DESC;

Ttitle CENTER "REPORTE -- Usuarios con mas de una suspensión activa: no debería ocurrir" skip 1 -
Ttitle OFF

SELECT *
  FROM (SELECT   COUNT (*) cant, a.usuario_id, a.activa
            FROM sacar.crm_suspension_usuario a
           WHERE a.activa = 'Y'
        GROUP BY usuario_id, activa)
 WHERE cant > 1;


/*Ttitle CENTER "REPORTE -- Busquedas sin resultados" skip 1 -
Ttitle OFF

SELECT   lobu_titulo, lobu_categoria_id, lobu_vendedor_id, COUNT (*)
    FROM sac_log_busqueda
   WHERE lobu_fecha_inicio IS NOT NULL
     AND lobu_ocurrencias = 0
     AND TRUNC (lobu_fecha_creacion) = TRUNC (SYSDATE - 1)
GROUP BY lobu_titulo, lobu_categoria_id, lobu_vendedor_id
ORDER BY 2 DESC;*/


Ttitle CENTER "REPORTE -- Tiempo promedio de resolucion de busqueda" skip 1 -
Ttitle OFF


SELECT AVG (lobu_fecha_fin - lobu_fecha_inicio) * 1000 segundos
  FROM sac_log_busqueda
 WHERE lobu_fecha_inicio IS NOT NULL
   AND TRUNC (lobu_fecha_creacion) = TRUNC (SYSDATE - 1);


Ttitle CENTER "REPORTE -- Tiempo promedio de resolucion de busqueda por hora" skip 1 -
Ttitle OFF

SELECT   TO_CHAR (lobu_fecha_creacion, 'hh24') hora,
         AVG (lobu_fecha_fin - lobu_fecha_inicio) * 1000 segundos
    FROM sac_log_busqueda
   WHERE lobu_fecha_inicio IS NOT NULL
     AND TRUNC (lobu_fecha_creacion) = TRUNC (SYSDATE - 1)
GROUP BY TO_CHAR (lobu_fecha_creacion, 'hh24');


Ttitle CENTER "REPORTE -- Busquedas de más de 20 segundo2 de duracion" skip 1 -
Ttitle OFF

SELECT   lobu_titulo, lobu_categoria_id, lobu_vendedor_id, COUNT (*)
    FROM sac_log_busqueda
   WHERE lobu_fecha_inicio IS NOT NULL
     AND TRUNC (lobu_fecha_creacion) = TRUNC (SYSDATE - 1)
     AND ((lobu_fecha_fin - lobu_fecha_inicio) * 1000) > 20
GROUP BY lobu_titulo, lobu_categoria_id, lobu_vendedor_id;

Ttitle CENTER "REPORTE -- Hay pagos pendientes de NPS hace mas de 3 días?" skip 1 -
Ttitle OFF

select estado estadoPagoNPS, origen origenPagoNPS, count(1) cantidadPagos, sum(monto) montoPagos
from sacar.cce_pago_nps
where trunc(generacion) < trunc(sysdate-3)
and trunc(generacion) > trunc(sysdate-30)
and estado = 'PENDIENTE'
group by estado, origen
order by estado, origen
;


Ttitle CENTER "REPORTE Motivos de no realizacion de las operaciones" skip 1 -
Ttitle OFF


SELECT   cal.origen, mot.descripcion, mot.origen, COUNT (*)
    FROM sacar.cal_calificacion cal, sacar.cal_motivo_no_realizacion mot
   WHERE TRUNC (cal.actualizacion) = TRUNC (SYSDATE-1)
     AND motivo_no_realizacion_id IS NOT NULL
     AND cal.motivo_no_realizacion_id = mot.ID
GROUP BY cal.origen, mot.descripcion, mot.origen
ORDER BY 1, 2;

Ttitle CENTER "REPORTE cantidad de avisos pendientes de estatificacion" skip 1 -
Ttitle OFF

SELECT COUNT (*)
  FROM sacar_h.sac_aviso_h ah
 WHERE aviso_estatificado = 'N'
   AND av_creado_por <> 'MIGRADOR'
   AND EXISTS (SELECT 1
                 FROM sacar.avi_estatificado ae
                WHERE ah.av_id = ae.aviso_id);

Ttitle CENTER " Cantidad de avisos republicados HOY con el Republicador masivo" skip 1 -
Ttitle OFF

select count(*)
from sacar.sac_aviso
where av_creado_por = 'REPUBLICACION_MASIVA_POPUP'
and trunc(actualizacion) = trunc(sysdate)
;

Ttitle CENTER "Cantidad de avisos en estado 14 (esperando a ser republicados) en SAC_AVISO " skip 1 -
Ttitle OFF

select count(*)
from sacar.sac_aviso
where av_estado = 14
;

Ttitle CENTER "Cantidad de avisos en estado 1 (esperando a ser republicados) en SAC_REPUBLICACION_INTERMEDIA" skip 1 -
Ttitle OFF

select count(*)
from sacar.sac_republicacion_intermedia
where id_estado = 1
;

Ttitle CENTER "Cantidad de avisos con error en SAC_REPUBLICACION_INTERMEDIA" skip 1 -
Ttitle OFF

select count(*)
from sacar.sac_republicacion_intermedia
where id_estado = 3
;

spool off;
exit;
