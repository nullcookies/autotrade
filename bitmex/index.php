<!DOCTYPE html>
<html lang="en">
<head>
	<title>Trade via API</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="refresh" content="601">
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet">
	<link href="assets/css/styles.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<!-- <script src="assets/js/htmlminifier.min.js"></script> -->
	<script src="assets/js/scripts.js"></script>
	<!--
	-->
</head>
<body>
	<div class="container container-fluid">
		<!-- <h2>Auto Trade Bitmex</h2> -->
		<!-- <p>The panel-group class clears the bottom-margin. Try to remove the class and see what happens.</p> -->
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#main">Main</a></li>
			<li><a data-toggle="tab" href="#account">Account</a></li>
			<li><a data-toggle="tab" href="#margin">Margin</a></li>
			<!-- <li><a data-toggle="tab" href="#list-order">List Open Order</a></li> -->
			<li><a data-toggle="tab" href="#open-positions">Open Positions</a></li>
			<li><a data-toggle="tab" href="#orderbook">OrderBook</a></li>
			<li><a data-toggle="tab" href="#orders">Orders</a></li>
			<li><a data-toggle="tab" href="#order">Order</a></li>
			<li class="bg-light text-dark"><a href="logout.php">Logout</a></li>
		</ul>

		<div class="tab-content">
			<div id="main" class="tab-pane fade in active">
				<div class="panel-group">
					<div class="panel panel-primary panel-current-price">
						<div class="panel-heading"><h3 class="panel-title">Current Price</h3></div>
						<div class="panel-body">
							Loading ...
						</div>
					</div>

					<?php /*
					<div class="panel panel-info panel-wallet">
						<div class="panel-heading"><h3 class="panel-title">Current Wallet</h3></div>
						<div class="panel-body">
							Loading ...
						</div>
					</div>
					*/ ?>

					<div class="panel panel-danger panel-actions">
						<div class="panel-heading"><h3 class="panel-title">Actions</h3></div>
						<div class="panel-body">
							Loading ...
						</div>
					</div>
				</div>
			</div>

			<div id="account" class="tab-pane fade">
				<div class="panel-group row">
					<div id="column-left" class="col-sm-6">
						<div class="panel panel-primary panel-account">
							<div class="panel-heading"><h3 class="panel-title">Account</h3></div>
							<div class="panel-body">
								Loading ...
							</div>
						</div>
						<div class="panel panel-info panel-wallet">
							<div class="panel-heading"><h3 class="panel-title">Wallet</h3></div>
							<div class="panel-body">
								Loading ...
							</div>
						</div>
						<div class="panel panel-info panel-open-order">
							<div class="panel-heading"><h3 class="panel-title">Open Order</h3></div>
							<div class="panel-body">
								Loading ...
							</div>
						</div>
					</div>
					<div id="column-right" class="col-sm-6"> 
						<div class="panel panel-primary panel-account2">
							<div class="panel-heading"><h3 class="panel-title">Account 2</h3></div>
							<div class="panel-body">
								Loading ...
							</div>
						</div>
						<div class="panel panel-info panel-wallet2">
							<div class="panel-heading"><h3 class="panel-title">Wallet 2</h3></div>
							<div class="panel-body">
								Loading ...
							</div>
						</div>
						<div class="panel panel-info panel-open-order2">
							<div class="panel-heading"><h3 class="panel-title">Open Order 2</h3></div>
							<div class="panel-body">
								Loading ...
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="margin" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-margin">
						<div class="panel-heading"><h3 class="panel-title">Margin</h3></div>
						<div class="panel-body">
							Loading ...
						</div>
					</div>
				</div>
			</div>

			<!-- <div id="list-order" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-list-order">
						<div class="panel-heading"><h3 class="panel-title">List Open Order</h3></div>
						<div class="panel-body">
							Loading ...
						</div>
					</div>
				</div>
			</div> -->

			<div id="open-positions" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-open-positions">
						<div class="panel-heading"><h3 class="panel-title">Open Positions</h3></div>
						<div class="panel-body">
							Loading ...
						</div>
					</div>
				</div>
			</div>
			
			<div id="orderbook" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-orderbook">
						<div class="panel-heading"><h3 class="panel-title">OrderBook</h3></div>
						<div class="panel-body">
							Loading ...
						</div>
					</div>
				</div>
			</div>

			<div id="orders" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-orders">
						<div class="panel-heading"><h3 class="panel-title">Orders</h3></div>
						<div class="panel-body">
							Loading ...
						</div>
					</div>
				</div>
			</div>

			<div id="order" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-order">
						<div class="panel-heading"><h3 class="panel-title">Order</h3></div>
						<div class="panel-body">
							Loading ...
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>