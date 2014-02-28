set heading on
set pagesize 0
set linesize 300


spool &1

/* Formatted on 2008/11/05 12:49 (Formatter Plus v4.8.8) */

select  'FECHA,'||'USUARIO,'||'NOMBRE,'||'APELLIDO,'||'APODO,'||'EMAIL,'||'COD_AREA,'||
        'TELEFONO,'||'COD_AREA2,'||'TELEFONO2,'||'DNI,'||'FECHA_NAC,'||'LOCALIDAD'
        FROM DUAL
        UNION ALL
SELECT   To_char (u.usu_fecha_acepta_condiciones,'dd/mm/yyyy')  || ',' || u.usuario_id ||','||
         u.usu_nombre ||','|| u.usu_apellido ||','|| u.usu_apodo ||','||
         u.usu_email ||','|| u.usu_cod_area_tel ||','|| u.usu_telefono ||','||
         ur.usu_cod_area_tel2 ||','|| ur.usu_telefono2 ||','|| ur.usu_dni ||','||
         ur.usu_fecha_nac ||','|| u.usu_localidad
    FROM sacar.sac_usuario u,
         sacar.sac_usuario_registro ur
   WHERE 0 = 0
     AND u.usuario_id = ur.usuario_id
     AND TRUNC (u.usu_fecha_acepta_condiciones) BETWEEN TRUNC (SYSDATE) - 7
                                                    AND TRUNC (SYSDATE) - 1
     AND ur.estado_registro_id = 2;

spool off;
exit;
