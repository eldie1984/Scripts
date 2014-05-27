<h2>Create a Capacities item</h2>

<?php echo validation_errors(); ?>

<?php echo form_open('capacity/create') ?>

	<label for="host">Nombre del Host : </label>
	<input type="input" name="host" /><br />

	<label for="userbk">FS de Backup : </label>
	<input type="input" name="userbk" /><br />

	<label for="app">Aplicacion : </label>
	<input type="input" name="app" /><br />

	<label for="tpu">TPU : </label>
	<input type="input" name="tpu" /><br />

	<label for="tpumin">TPU minimo : </label>
	<input type="input" name="tpumin" /><br />

	<label for="ram">Memoria : </label>
	<input type="input" name="ram" /><br />

	<label for="tipe">Tipo : </label>
	<input type="input" name="tipe" /><br />

	<label for="server">Servidor : </label>
	<input type="input" name="server" /><br />

	<label for="clase">Clase : </label>
	<input type="input" name="clase" /><br />

	<label for="os">Sistema Operativo : </label>
	<input type="input" name="os" /><br />

	<label for="swap">SWAP : </label>
	<input type="input" name="swap" /><br />

	<label for="base">Tiene Base : </label>
	<select name="base">
	<option value="0" selected>(please select:)</option>
	<option value="SI">SI</option>
	<option value="NO">NO</option>
	</select>
	<br />


	<input type="submit" name="submit" value="Create capacity item" />

</form>