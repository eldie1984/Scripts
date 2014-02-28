/*********************************************************************
DESCRIPCION: Este query lo que hace es tomar los avisos crawleados por google
             que se encuentran en la tabla analisis_google_log, y de los mismos
             muestra un informe segun estado y fecha de creacion de los avisos.
             Esta informacion es tomada desde SAC_AVISO.
             Esta query es usada por el proceso analisisLog.sh.

AUTOR: PTAMBURRO
FECHA: 26/07/2010
PROYECTO:
**********************************************************************/
spool /tmp/reporteFormateado.txt
--SET LINESIZE 430;
--set pagesize 500;
column anio format a5 word_wrapped;
column estado format a10 word_wrapped;
set lines 180;
set pages 500;
set feedback off;

SELECT   anio, estado, SUM (CASE
                               WHEN mes = '01'
                                  THEN cant_avisos
                            END) AS enero,
         SUM (CASE
                 WHEN mes = '02'
                    THEN cant_avisos
              END) AS febrero,
         SUM (CASE
                 WHEN mes = '03'
                    THEN cant_avisos
              END) AS marzo,
         SUM (CASE
                 WHEN mes = '04'
                    THEN cant_avisos
              END) AS abril,
         SUM (CASE
                 WHEN mes = '05'
                    THEN cant_avisos
              END) AS mayo,
         SUM (CASE
                 WHEN mes = '06'
                    THEN cant_avisos
              END) AS junio,
         SUM (CASE
                 WHEN mes = '07'
                    THEN cant_avisos
              END) AS julio,
         SUM (CASE
                 WHEN mes = '08'
                    THEN cant_avisos
              END) AS agosto,
         SUM (CASE
                 WHEN mes = '09'
                    THEN cant_avisos
              END) AS septiembre,
         SUM (CASE
                 WHEN mes = '10'
                    THEN cant_avisos
              END) AS octubre,
         SUM (CASE
                 WHEN mes = '11'
                    THEN cant_avisos
              END) AS noviembre,
         SUM (CASE
                 WHEN mes = '12'
                    THEN cant_avisos
              END) AS diciembre, SUM (cant_avisos) total
    FROM (SELECT   '1' AS agrupador, anio, mes, estado, COUNT (*) cant_avisos
              FROM (SELECT sa.av_id,
                           TO_CHAR (sa.av_fecha_creacion, 'YYYY') AS anio,
                           TO_CHAR (sa.av_fecha_creacion, 'MM') AS mes,
                           DECODE (sa.av_estado,
                                   3, 'ACTIVO',
                                   'NO ACTIVO'
                                  ) AS estado
                      FROM SACAR.analisis_google_log agl LEFT JOIN SACAR.sac_aviso sa
                           ON agl.aviso_id = sa.av_id
                           )
             WHERE av_id IS NOT NULL
          GROUP BY (anio, mes, estado)
          ORDER BY anio, mes, estado ASC)
GROUP BY CUBE (anio), estado
ORDER BY anio, estado ASC;
spool off;
