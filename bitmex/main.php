<?php
if (!function_exists("dump")) {
	function dump($arr)
	{
		echo "<pre>";
		var_dump($arr);
		echo "</pre>";
	}
}

function print_arr1_to_table($arr = null, $title = '', $options = null) 
{
	if (!$arr) return null;
?>
	<!-- <div class="panel <?php if (isset($options['panel'])){echo $options['panel'];}else echo 'panel-default';?>"> -->
		<?php /* if ($title): ?><div class="panel-heading"><h3 class="panel-title"><?php echo $title; ?></h3></div><?php endif; */ ?>
		<!-- <div class="panel-body"> -->
			<table class="table table-bordered table-condensed">
				<?php
				foreach ($arr as $key => $value) {
					// echo '<tr><td><strong>' . $key . '</strong></td><td><strong>' . ((!is_array($value)) ? $value : var_export($value)) . '</strong></td></tr>';
					echo '<tr><td>' . $key . '</td><td><strong>' . ((!is_array($value)) ? $value : json_encode($value)) . '</strong></td></tr>';
				}
				?>
			</table>
		<!-- </div> -->
	<!-- </div> -->
<?php
}

// ============================================================ //
