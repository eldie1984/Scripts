<?php 
$con = oci_connect('E438827','E438827','DWH.world');
if (!$con) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

class Cliente{
        var $con;
        function Cliente(){
                $this->con=new DBManager;
        }

        function insertar($campos){
                if($this->con->conectar()==true){
                        return mysql_query("INSERT INTO userbk (`HOST`,`USERBK`,`APP`,`TPU`,`RAM`,`TIPE`,`SERVER`,`CLASE`,` OS`,`SWAP`,`TPUMIN`,`BASE`) VALUES ('".$campos[0]."', '".$campos[1]."','".$campos[2]."','".$campos[3]."','".$campos[4]."','".$campos[5]."','".$campos[6]."','".$campos[7]."','".$campos[8]."','".$campos[9]."','".$campos[10]."','".$campos[11]."',)");
                }
        }
        
        function actualizar($campos,$idusuario){
                if($this->con->conectar()==true){
                        return mysql_query("UPDATE usuarios SET nombre = '".$campos[0]."', apellido = '".$campos[1]."', especialidad = '".$campos[2]."', email = '".$campos[4]."', telefono = '".$campos[3]."' WHERE idusuario = ".$idusuario);
                }
        }
        
        function mostrar_cliente($idusuario){
                if($this->con->conectar()==true){
                        return mysql_query("SELECT * FROM usuarios WHERE idusuario=".$idusuario);
                }
        }

        function mostrar_clientes(){
                if($this->con->conectar()==true){
                        return mysql_query("SELECT * FROM usuarios ORDER BY idusuario ASC");
                }
        }

        function eliminar($idusuario){
                if($this->con->conectar()==true){
                        return mysql_query("DELETE FROM usuarios WHERE idusuario=".$idusuario);
                }
        }
}
?>
