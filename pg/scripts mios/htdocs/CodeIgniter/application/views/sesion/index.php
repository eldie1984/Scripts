<h2><?php echo $host ?></h2>

 <div id="content">
        <table width="950" border="0">
              <tr bgcolor="#FFFF99">
                <td align="center"><strong>Sesion</strong></td>
                <td align="center"><strong>Descripcion</strong></td>
                <td align="center"><strong>Estado</strong></td>
                <td align="center" ><strong>Inicio</strong></td>
                <td align="center"><strong>Fin</strong></td>
                
              </tr>
<?php foreach ($sessions as $sesion_item): ?>

    <?php

    echo "<tr><td align=\"center\"><a href=\"#\" class=\"popper\" data-popbox=\"".$sesion_item['SESION'].$id."\">".$sesion_item['SESION']."</a></td>";
               echo '<td align="center">'.$sesion_item['DESCRIPCION'].'</td>';
                if (( $sesion_item['ESTADO'] == 3 )){
                    echo '<td align="center" bgcolor="#00FF00">TERMINE</td>';
                }
                elseif (( $sesion_item['ESTADO'] == 5)){
                    echo '<td align="center" bgcolor="#0066FF">EXECUTION_EN_COURS</td>';
                }
                elseif (( $sesion_item['ESTADO'] == 4)){
                    echo '<td align="center" bgcolor="#FF9933">HORAIRE_DEPASSE</td>';
                }
                elseif (( $sesion_item['ESTADO'] == 2)){
                    echo '<td align="center" bgcolor="#FF0000">INCIDENTE</td>';
                }
                elseif (( $sesion_item['ESTADO'] == 1)){
                    echo '<td align="center" bgcolor="#FFFF00">ATTENTE_EVENEMENT</td>';
                }
                else{
                    echo '<td align="center" >'.$sesion_item['ESTADO']."</td>";
                }
                echo "<td align=\"center\">".$sesion_item['INICIO']."</td>
                 <td align=\"center\">".$sesion_item['FIN']."</td>
                  </tr>";
                  ?>
<?php endforeach ?>

</table>
    </div>