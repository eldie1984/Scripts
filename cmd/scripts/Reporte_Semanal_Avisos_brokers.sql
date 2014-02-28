--set timing on
set heading on
set pagesize 3100
set linesize 190

column APODO format a18
column NOMBRE format a18
column APELLIDO format a18
column MAIL format a32
column TELEFONO format a13
column ESTADO_CUENTA format a13

spool &&1

prompt
prompt ==========================================================================================================================================================
prompt                                                                                  Listado de Avisos Activos de Brokers
prompt ==========================================================================================================================================================
-- Usuarios No Validados.

select u.usu_apodo APODO, u.USU_NOMBRE NOMBRE, u.USU_APELLIDO APELLIDO, u.USU_EMAIL MAIL, u.USU_TELEFONO TELEFONO
, NVL ((SELECT count(1) FROM sac_Aviso a WHERE a.AV_ESTADO = 3 and u.usuario_id = a.usuario_id), 0) AS AVISOS_ACTIVOS
, NVL ((SELECT c.ESTADO FROM cta_cuenta c WHERE u.usuario_id = c.usuario_id), 'NO TIENE') AS ESTADO_CUENTA
, NVL ((SELECT c.CONDICION FROM cta_cuenta c WHERE u.usuario_id = c.usuario_id), 'NO APLICA') AS DATOS_FIDEDIGNOS
from sac_usuario u
where tius_id = 1
order by usu_apodo;
spool off;
exit;
