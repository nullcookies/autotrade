<?php
if (!function_exists("dump")) {
	function dump($arr)
	{
		echo "<pre>";
		var_dump($arr);
		echo "</pre>";
	}
}

function check_login($user = null, $pass = null)
{
	$arr_allowed = array(
		'admin' => md5(base64_encode('admin')), 
	);

	if (!$arr_allowed) return null;
	foreach ($arr_allowed as $key => $value) {
		if ($user == $key and md5(base64_encode($pass)) == $value) return $key;
	}
	return null;
}

function redirect($url = '/', $time = 0)
{
	echo '<meta http-equiv="refresh" content="' . (int) $time . ';url=' . (string) trim($url) . '"/>';
	exit;
}

function print_arr1_to_table($arr = null, $title = '', $options = null) 
{
	if (!$arr) return null;
?>
	<?php /*<div class="panel <?php if (isset($options['panel'])){echo $options['panel'];}else echo 'panel-default';?>">
		<?php if ($title): ?><div class="panel-heading"><h3 class="panel-title"><?php echo $title; ?></h3></div><?php endif; ?>
		<div class="panel-body">*/ ?>
			<table class="table table-bordered table-condensed" <?php if (isset($options['style'])){echo 'style="' . $options['style'] . '"';}?>>
				<?php
				if (trim(strlen($title)) > 0) echo '<tr><td class="bg-info" colspan="2">' . $title . '</td></tr>';
				$i=0;
				foreach ($arr as $key => $value) {
					// echo '<tr><td><strong>' . $key . '</strong></td><td><strong>' . ((!is_array($value)) ? $value : var_export($value)) . '</strong></td></tr>';
					echo '<tr><td' . (($i == 0) ? ' class="col-title' . ((trim(strlen($title)) <= 0) ? ' bg-info':'') . '"' : '') . '>' . $key . '</td><td' . (($i == 0) ? ' class="col-info' . ((trim(strlen($title)) <= 0) ? ' bg-info':'') . '"' : '') . '><strong>' . ((!is_array($value)) ? $value : json_encode($value)) . '</strong></td></tr>';
					$i++;
				}
				?>
			</table>
		<?php /*</div>
	</div>*/ ?>
<?php
}

function replace_by_star($str = null)
{
	$str_new = '';
	for ($i=0; $i < strlen($str) - 1; $i++) { 
		$str_new .= '*';
	}
	return $str_new;
}

// ============================================================ //
