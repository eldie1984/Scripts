<div id="accordion">

<?php foreach ($hosts as $host_item): ?>

    <h3><?php echo $host_item['HOST'] ?></h3>

<div>
        <?php echo "Usuario de Backup : ".$host_item['USERBK'] ?> <br>
        <?php echo "Aplicacion : ".$host_item['APP'] ?> <br>
        <?php echo "TPU : ".$host_item['TPU'] ?> <br>
        <?php echo "Memoria : ".$host_item['RAM'] ?> <br>
        <?php echo "Tipo : ".$host_item['TIPE'] ?> <br>
        <?php echo "Servidor : ".$host_item['SERVER'] ?> <br>
        <?php echo "Clase : ".$host_item['CLASE'] ?> <br>
        <?php echo "Sistema Operativo : ".$host_item['OS'] ?> <br>
        <?php echo "SWAP : ".$host_item['SWAP'] ?> <br>
        <?php echo "TPU minimo : ".$host_item['TPUMIN'] ?> <br>
        <?php echo "Tiene Base : ".$host_item['BASE'] ?> <br>

        <input type="submit" name="submit" value="Modificar" />
    </div>
    

<?php endforeach ?>
   </div>