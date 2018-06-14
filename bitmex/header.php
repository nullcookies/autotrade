<html lang="en">
<head>
	<!--
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet">
	-->
	<style type="text/css">
		body,table{font-size: 14px;}
		.table td, .table th{padding: 5px;}
		td{border: 1px solid #eee;}
	</style>
	<!-- 
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	-->
</head>
<body>

<?php
if (!function_exists("dump")) {
	function dump($arr)
	{
		echo "<pre>";
		var_dump($arr);
		echo "</pre>";
	}
}

function print_arr1_to_table($title = '', $arr = null) 
{
	if (!$arr) return null;
?>
	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php echo $title; ?></h3></div>
		<div class="panel-body">
			<table class="table table-bordered table-condensed">
				<?php
				foreach ($arr as $key => $value) {
					// echo '<tr><td><strong>' . $key . '</strong></td><td><strong>' . ((!is_array($value)) ? $value : var_export($value)) . '</strong></td></tr>';
					echo '<tr><td><strong>' . $key . '</strong></td><td>' . ((!is_array($value)) ? $value : json_encode($value)) . '</td></tr>';
				}
				?>
			</table>
		</div>
	</div>
<?php
}

// ============================================================ //
