set heading on
set pagesize 3100
set linesize 200

column APODO format a20
column MAIL format a40
column CODIGO_AREA_1 format a13
column TELEFONO_1 format a10
column CODIGO_AREA_2 format a13
column TELEFONO_2 format a10
column LOCALIDAD format a20
column PROVINCIA format a20
column FECHA_INGRESO format a13

set markup html on spool on
spool &1

TTITLE CENTER "Este listado comprende lapso de a Martes a Lunes" skip 1 -
CENTER "===============================================================" skip 2 -
LEFT " Listado de Usuarios No Validados " skip 1 -
LEFT "==================================" skip 2


select u.usu_apodo APODO, u.usu_email MAIL, u.USU_COD_AREA_TEL CODIGO_AREA_1, u.USU_TELEFONO TELEFONO_1,
ur.USU_COD_AREA_TEL2 CODIGO_AREA_2, ur.USU_TELEFONO2 TELEFONO_2, u.USU_LOCALIDAD LOCALIDAD,
p.PRV_NOMBRE PROVINCIA,trunc(u.USU_FECHA_INGRESO) FECHA_INGRESO
from sac_usuario u, sac_usuario_registro ur, sac_provincias p
where u.USUARIO_ID = ur.USUARIO_ID
and u.PRV_ID = p.PRV_ID
and ur.ESTADO_REGISTRO_ID <> 2
and trunc(u.USU_FECHA_INGRESO) >= to_date(sysdate) - 7
and trunc(u.USU_FECHA_INGRESO) < to_date(sysdate)
order by u.USU_FECHA_INGRESO;

TTITLE OFF

prompt
prompt ==========================================================================================================================================================
prompt                                                  Cantidad de Usuarios que se validaron esta semana, de la semana anterior
prompt ==========================================================================================================================================================
-- Usuarios Validados.
select count(*) Cantidad
from sac_usuario u, sac_usuario_registro ur
where u.USUARIO_ID = ur.USUARIO_ID
and ur.ESTADO_REGISTRO_ID = 2
and trunc(u.USU_FECHA_INGRESO) >= to_date(sysdate) - 14
and trunc(u.USU_FECHA_INGRESO) < to_date(sysdate) - 7
and trunc(u.USU_ULTIMA_ACTUALIZACION) >= to_date(sysdate) - 7
and trunc(u.USU_ULTIMA_ACTUALIZACION) < to_date(sysdate);

spool off;
exit;
