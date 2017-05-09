if [ -z ${SCRIPTS_HOME+x} ]; then export SCRIPTS_HOME="`dirname \"$0\"`"; else echo $SCRIPTS_HOME; fi
export KETTLE_HOME=$1
PENTAHO_HOME=$2
if [ -z ${KETTLE_HOME+x} ]; then exit 2; else echo $KETTLE_HOME; fi
if [ -z ${PENTAHO_HOME+x} ]; then exit 2; else echo $PENTAHO_HOME; fi
if [ -z ${PENTAHO_HOME+x} ]; then exit 2; else echo $PENTAHO_HOME; fi	
$PENTAHO_HOME/kitchen.sh -file=$KETTLE_HOME/Jobs/Sat.kjb -level=Basic
$PENTAHO_HOME/kitchen.sh -file=$KETTLE_HOME/Jobs/Chequeo.kjb -level=Basic > /var/log/Dac/pentaho.log 2>&1
$PENTAHO_HOME/kitchen.sh -file=$KETTLE_HOME/Jobs/Sat.kjb -level=Basic
python $SCRIPTS_HOME/pentaho/update_cord_pg.py > /var/log/Dac/cordenada.log 2>&1
python $SCRIPTS_HOME/daemon_barrio.py start > /var/log/Dac/salida_barrio.log 2>&1