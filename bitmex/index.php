<!DOCTYPE html>
<html lang="en">
<head>
	<title>Trade via API</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<style type="text/css">
		body,table{font-size: 14px;}
		.container{width: 100%; padding: 20px;}
		.table td, .table th{padding: 5px;}
		table{width: auto !important; margin-bottom: 0!important;}
		td{border: 1px solid #eee;}
		h3{font-size: 14px !important;}
		.panel{/*float: left;*/margin-bottom: 20px!important;}
		.panel-group{margin-top: 20px;}
		.panel-current-price,.panel-wallet{width: 49%; min-height: 320px; float: left;}
		.panel-wallet{float: right;}
		.panel-actions{clear: both;}
		.panel-group .panel+.panel{margin-top: 0;}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<!-- <script src="htmlminifier.min.js"></script> -->
	<script src="scripts.js"></script>
	<!--
	-->
</head>
<body>
	<div class="container">
		<h2>Auto Trade Bitmex</h2>
		<!-- <p>The panel-group class clears the bottom-margin. Try to remove the class and see what happens.</p> -->
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#main">Main</a></li>
			<li><a data-toggle="tab" href="#margin">Margin</a></li>
			<li><a data-toggle="tab" href="#list-order">List Open Order</a></li>
			<li><a data-toggle="tab" href="#open-positions">Open Positions</a></li>
			<li><a data-toggle="tab" href="#orderbook">OrderBook</a></li>
			<li><a data-toggle="tab" href="#orders">Orders</a></li>
			<li><a data-toggle="tab" href="#order">Order</a></li>
			<li><a data-toggle="tab" href="#account">Account</a></li>
		</ul>

		<div class="tab-content">
			<div id="main" class="tab-pane fade in active">
				<div class="panel-group">
					<!-- All content load here -->
					<div class="panel panel-primary panel-current-price">
						<div class="panel-heading"><h3 class="panel-title">Current Price</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>

					<div class="panel panel-info panel-wallet">
						<div class="panel-heading"><h3 class="panel-title">Current Wallet</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>

					<div class="panel panel-danger panel-actions">
						<div class="panel-heading"><h3 class="panel-title">Actions</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>
				</div>
			</div>

			<div id="margin" class="tab-pane fade">
				<div class="panel-group">
					<!-- All content load here -->
					<div class="panel panel-default panel-margin">
						<div class="panel-heading"><h3 class="panel-title">Margin</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>
				</div>
			</div>

			<div id="list-order" class="tab-pane fade">
				<div class="panel-group">
					<!-- All content load here -->
					<div class="panel panel-default panel-list-order">
						<div class="panel-heading"><h3 class="panel-title">List Open Order</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>
				</div>
			</div>

			<div id="open-positions" class="tab-pane fade">
				<div class="panel-group">
					<!-- All content load here -->
					<div class="panel panel-default panel-open-positions">
						<div class="panel-heading"><h3 class="panel-title">Open Positions</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>
				</div>
			</div>
			
			<div id="orderbook" class="tab-pane fade">
				<div class="panel-group">
					<!-- All content load here -->
					<div class="panel panel-default panel-orderbook">
						<div class="panel-heading"><h3 class="panel-title">OrderBook</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>
				</div>
			</div>

			<div id="orders" class="tab-pane fade">
				<div class="panel-group">
					<!-- All content load here -->
					<div class="panel panel-default panel-orders">
						<div class="panel-heading"><h3 class="panel-title">Orders</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>
				</div>
			</div>

			<div id="order" class="tab-pane fade">
				<div class="panel-group">
					<!-- All content load here -->
					<div class="panel panel-default panel-order">
						<div class="panel-heading"><h3 class="panel-title">Order</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>
				</div>
			</div>

			<div id="account" class="tab-pane fade">
				<div class="panel-group">
					<!-- All content load here -->
					<div class="panel panel-primary panel-main-info">
						<div class="panel-heading"><h3 class="panel-title">Main Info</h3></div>
						<div class="panel-body">
							<!-- Content load here -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>