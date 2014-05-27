<h2>Create a Capacities item</h2>

<?php echo validation_errors(); ?>

<?php echo form_open('capacity/modify') ?>

	<label for="host">Nombre del Host : </label>
	<input type="input" name="host" <?php echo 'value="'.$host.'"' ?> /><br />

	<label for="userbk">FS de Backup : </label>
	<input type="input" name="userbk"  <?php echo 'value="'.$userbk.'"' ?> /><br />

	<label for="app">Aplicacion : </label>
	<input type="input" name="app"  <?php echo 'value="'.$app.'"' ?> /><br />

	<label for="tpu">TPU : </label>
	<input type="input" name="tpu"  <?php echo 'value="'.$tpu.'"' ?> /><br />

	<label for="tpumin">TPU minimo : </label>
	<input type="input" name="tpumin"  <?php echo 'value="'.$tpumin.'"' ?> /><br />

	<label for="ram">Memoria : </label>
	<input type="input" name="ram"  <?php echo 'value="'.$ram.'"' ?> /><br />

	<label for="tipe">Tipo : </label>
	<input type="input" name="tipe"  <?php echo 'value="'.$tipe.'"' ?> /><br />

	<label for="server">Servidor : </label>
	<input type="input" name="server"  <?php echo 'value="'.$server.'"' ?> /><br />

	<label for="clase">Clase : </label>
	<input type="input" name="clase"  <?php echo 'value="'.$clase.'"' ?> /><br />

	<label for="os">Sistema Operativo : </label>
	<input type="input" name="os"  <?php echo 'value="'.$os.'"' ?> /><br />

	<label for="swap">SWAP : </label>
	<input type="input" name="swap"  <?php echo 'value="'.$swap.'"' ?> /><br />

	<label for="base">Tiene Base : </label>
	<select name="base">
	<option value="0" selected>(please select:)</option>
	<option value="SI"  <?php if ($base == 1) echo "selected" ?> >SI</option>
	<option value="NO" <?php if ($base == 1) echo "selected" ?> >NO</option>
	</select>
	<br />


	<input type="submit" name="submit" value="Modify capacity item" />

</form>