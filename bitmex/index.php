<!DOCTYPE html>
<html lang="en">
<head>
	<title>Trade via API</title>
	<meta charset="utf-8" />
    <meta name="robots" content="noindex,nofollow" />
    <meta name="googlebot" content="noindex" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="HandheldFriendly" content="true" />
    <meta name="renderer" content="webkit" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="viewport" content="user-scalable=no,width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0" />
    <link rel="icon" href="process.php?img=favicon" type="image/png" />
    <link rel="shortcut icon" href="process.php?img=favicon" type="image/png" />
	<meta http-equiv="refresh" content="3601" />
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet">
	<link href="assets/css/styles.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="assets/js/scripts.js"></script>
	<script type="text/javascript">
		document.addEventListener('gesturestart', function (e) {
			e.preventDefault();
		});
	</script>
</head>
<body>
	<div class="container">
		<!-- <h2>Auto Trade Bitmex</h2> -->
		<!-- <p>The panel-group class clears the bottom-margin. Try to remove the class and see what happens.</p> -->
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/">Home</a>
				</div>

				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li role="presentation" class="active"><a data-toggle="tab" href="#main">Main</a></li>
						<li role="presentation"><a data-toggle="tab" href="#chart">Chart</a></li>
						<li role="presentation"><a data-toggle="tab" href="#account">Account</a></li>
						<li role="presentation"><a data-toggle="tab" href="#open-orders">Open Orders</a></li>
						<li role="presentation"><a data-toggle="tab" href="#margin">Margin</a></li>
						<!-- <li role="presentation"><a data-toggle="tab" href="#list-order">List Open Order</a></li> -->
						<li role="presentation"><a data-toggle="tab" href="#orderbook">OrderBook</a></li>
						<li role="presentation"><a data-toggle="tab" href="#orders">Orders</a></li>
						<li role="presentation"><a data-toggle="tab" href="#order">Order</a></li>
						<li role="presentation" class="bg-light text-dark"><a href="logout.php">Logout</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="tab-content">
			<div id="main" class="tab-pane fade in active">
				<div class="panel-group row">
					<div id="column-left" class="col-sm-6">
						<div class="panel panel-primary panel-current-price">
							<div class="panel-heading"><h3 class="panel-title">Current Price</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
					</div>
					<div id="column-right" class="col-sm-6"> 
						<div class="panel panel-danger panel-actions">
							<div class="panel-heading"><h3 class="panel-title">Actions</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="chart" class="tab-pane fade in active">
				<div class="panel-group">
					<div class="panel panel-info panel-chart">
						<div class="panel-heading"><h3 class="panel-title">Chart</h3></div>
						<div class="panel-body chart-container tradingview-widget-container">
							<p class="message">Loading ...</p>
							<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
						</div>
					</div>
				</div>
			</div>

			<div id="account" class="tab-pane fade">
				<div class="panel-group row">
					<div id="column-left" class="col-sm-6">
						<div class="panel panel-primary panel-account">
							<div class="panel-heading"><h3 class="panel-title">Account</h3></div>
							<div class="panel-body panel-body-min">
								<p class="message">Loading ...</p>
							</div>
						</div>
						<div class="panel panel-info panel-wallet">
							<div class="panel-heading"><h3 class="panel-title">Wallet</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
					</div>
					<div id="column-right" class="col-sm-6"> 
						<div class="panel panel-primary panel-account2">
							<div class="panel-heading"><h3 class="panel-title">Account</h3></div>
							<div class="panel-body panel-body-min">
								<p class="message">Loading ...</p>
							</div>
						</div>
						<div class="panel panel-info panel-wallet2">
							<div class="panel-heading"><h3 class="panel-title">Wallet</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="open-orders" class="tab-pane fade">
				<div class="panel-group row">
					<div id="column-left" class="col-sm-6">
						<div class="panel panel-info panel-open-positions">
							<div class="panel-heading"><h3 class="panel-title">Open Positions</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
						<div class="panel panel-info panel-open-order">
							<div class="panel-heading"><h3 class="panel-title">Open Order</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
					</div>
					<div id="column-right" class="col-sm-6"> 
						<div class="panel panel-info panel-open-positions2">
							<div class="panel-heading"><h3 class="panel-title">Open Positions</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
						<div class="panel panel-info panel-open-order2">
							<div class="panel-heading"><h3 class="panel-title">Open Order</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="margin" class="tab-pane fade">
				<div class="panel-group row">
					<div id="column-left" class="col-sm-6">
						<div class="panel panel-default panel-margin2">
							<div class="panel-heading"><h3 class="panel-title">Margin</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
					</div>
					<div id="column-right" class="col-sm-6"> 
						<div class="panel panel-default panel-margin2">
							<div class="panel-heading"><h3 class="panel-title">Margin</h3></div>
							<div class="panel-body">
								<p class="message">Loading ...</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- <div id="list-order" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-list-order">
						<div class="panel-heading"><h3 class="panel-title">List Open Order</h3></div>
						<div class="panel-body">
							<p class="message">Loading ...</p>
						</div>
					</div>
				</div>
			</div> -->
			
			<div id="orderbook" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-orderbook">
						<div class="panel-heading"><h3 class="panel-title">OrderBook</h3></div>
						<div class="panel-body">
							<p class="message">Loading ...</p>
						</div>
					</div>
				</div>
			</div>

			<div id="orders" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-orders">
						<div class="panel-heading"><h3 class="panel-title">Orders</h3></div>
						<div class="panel-body">
							<p class="message">Loading ...</p>
						</div>
					</div>
				</div>
			</div>

			<div id="order" class="tab-pane fade">
				<div class="panel-group">
					<div class="panel panel-default panel-order">
						<div class="panel-heading"><h3 class="panel-title">Order</h3></div>
						<div class="panel-body">
							<p class="message">Loading ...</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>