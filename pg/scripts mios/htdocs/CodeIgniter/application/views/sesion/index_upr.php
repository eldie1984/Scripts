<?php echo "<div id=\"".$sesion.$host."\" class=\"popbox\">
    <table width=\"612\" border=\"0\">
              <tr bgcolor=\"#FFFF99\">
                <td align=\"center\"><strong>UPROC</strong></td>
                <td align=\"center\"><strong>Estado</strong></td>
                <td align=\"center\" ><strong>Inicio</strong></td>
                <td align=\"center\"><strong>Fin</strong></td>
                
              </tr>"; ?>

<?php foreach ($uprs as $upr_item): ?>

    <?php

    echo "<tr><td align=\"center\">".$upr_item['UPROC']."</td>";
                if (( $upr_item['ESTADO'] == 3 )){
                    echo '<td align="center" bgcolor="#00FF00">TERMINE</td>';
                }
                elseif (( $upr_item['ESTADO'] == 5)){
                    echo '<td align="center" bgcolor="#0066FF">EXECUTION_EN_COURS</td>';
                }
                elseif (( $upr_item['ESTADO'] == 4)){
                    echo '<td align="center" bgcolor="#FF9933">HORAIRE_DEPASSE</td>';
                }
                elseif (( $upr_item['ESTADO'] == 2)){
                    echo '<td align="center" bgcolor="#FF0000">INCIDENTE</td>';
                }
                elseif (( $upr_item['ESTADO'] == 1)){
                    echo '<td align="center" bgcolor="#FFFF00">ATTENTE_EVENEMENT</td>';
                }
                else{
                    echo '<td align="center" >'.$upr_item['ESTADO']."</td>";
                }
                echo "<td align=\"center\">".$upr_item['INICIO']."</td>
                 <td align=\"center\">".$upr_item['FIN']."</td>
                  </tr>";
                  ?>
<?php endforeach ?>

</table>
    </div>