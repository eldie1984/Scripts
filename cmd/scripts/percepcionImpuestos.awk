BEGIN{FS="|"}
{

# Cantidad de campos
if (NF != 11) {print "Cantidad de campos incorrectos en la linea " NR ; exit 1}

# Campos obligatorios
if ($1 == "" || $2 == "" || $3 == "" || $4 == "" || $5 == "" || $6 == "" || $9 == "" || $10 == "") {print "Faltan campos obligatorios en la linea " NR ; exit 1}

# Largo del campo fecha
if (length ($4) != 8) {print "FECHA_PERCEPCION invalida en la linea " NR ; exit 1}

# Formato de los datos NUMERICO
if (($1 + 0) != $1) {print "Formato incorrecto de ID_USUARIO en la linea " NR ; exit 1}
if (($2 + 0) != $2) {print "Formato incorrecto de ID_IMPUESTO en la linea " NR ; exit 1}
if (($3 + 0) != $3) {print "Formato incorrecto de ID_PERCEPCION en la linea " NR ; exit 1}
if (($4 + 0) != $4) {print "Formato incorrecto de FECHA_PERCEPCION en la linea " NR ; exit 1}
if (($6 + 0) != $6) {print "Formato incorrecto de NUMERO_LOTE en la linea " NR ; exit 1}
if ($8 != "" && ($8 + 0) != $8) {print "Formato incorrecto de NUMERO_COMPROBANTE en la linea " NR ; exit 1}
if (($9 + 0) != $9) {print "Formato incorrecto de MONEDA en la linea " NR ; exit 1}
if (($10 + 0) != $10) {print "Formato incorrecto de MONTO_PERCEPCION en la linea " NR ; exit 1}
if ($11 != "" && ($11 + 0) != $11) {print "Formato incorrecto de PORCENTAJE en la linea " NR ; exit 1}

# Valores posibles del campo "Tipo de Cuenta"
if ($5 != "PS" && $5 != "MO") {print "TIPO_CUENTA invalida en la linea " NR ; exit 1}

# Valores posibles del campo "Detalle de tipo de comprobante"
if ($7 != "" && $7 != "FCA" && $7 != "FCB") {print "SUBTIPO_COMPROBANTE invalido en la linea " NR ; exit 1}

# Valores posibles del campo "Moneda"
if ($9 != "1" && $9 != "2") {print "MONEDA invalida en la linea " NR ; exit 1}

}
END{}
