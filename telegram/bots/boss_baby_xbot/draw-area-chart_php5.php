<?php
/**
 * README
 * This configuration file is intended to run a list of commands with crontab.
 * Uncommented parameters must be filled
 */

if (!defined('STDIN')) die('Access denied.' . "\n");

// Error handle
require_once __DIR__ . '/../error-handle.php';

// Load composer
require_once LIB_DIR . '/telegram/vendor/autoload.php';

// Check PHP version
if (PHP_VERSION_ID >= 70100) {
	require_once __DIR__ . '/draw-area-chart.php';
	exit;
}

/* CAT:Area Chart */

/* pChart library inclusions */
include(LIB_DIR . "/pChart2.1.4/class/pData.class.php");
include(LIB_DIR . "/pChart2.1.4/class/pDraw.class.php");
include(LIB_DIR . "/pChart2.1.4/class/pImage.class.php");

/* Create and populate the pData object */
$myData = new pData();  
// for($i=0;$i<=30;$i++) { $myData->addPoints(rand(1,15),"Probe 1"); }

$width = 1000;
$height = 650;

$data = file_get_contents('https://api.coindesk.com/v1/bpi/historical/close.json');
// $data = '{"bpi":{"2018-07-09":6664.2063,"2018-07-10":6302.49,"2018-07-11":6381.8725,"2018-07-12":6245.6338,"2018-07-13":6217.6063,"2018-07-14":6247.2275,"2018-07-15":6349.0375,"2018-07-16":6726.405,"2018-07-17":7314.9425,"2018-07-18":7378.7575,"2018-07-19":7470.825,"2018-07-20":7330.5363,"2018-07-21":7404.2875,"2018-07-22":7396.2863,"2018-07-23":7717.5,"2018-07-24":8397.635,"2018-07-25":8166.76,"2018-07-26":7929.61,"2018-07-27":8183.025,"2018-07-28":8229.96,"2018-07-29":8215.56,"2018-07-30":8167.9988,"2018-07-31":7726.8913,"2018-08-01":7603.7488,"2018-08-02":7535.02,"2018-08-03":7415.5613,"2018-08-04":7009.0888,"2018-08-05":7026.9913,"2018-08-06":6937.0738,"2018-08-07":6717.2088,"2018-08-08":6280.58},"disclaimer":"This data was produced from the CoinDesk Bitcoin Price Index. BPI value data returned as USD.","time":{"updated":"Aug 9, 2018 00:03:00 UTC","updatedISO":"2018-08-09T00:03:00+00:00"}}';

if (!$json = json_decode($data) or json_last_error())
	die('Failed!');
$bpi = \BossBaby\Utility::object_to_array($json->bpi);
// dump($bpi);die;

$i = 0;
$arr_time = $arr_price = [];
$first_time = $last_time = $last_price = '';
// $arr_time[] = '07/07/2018'; $arr_price[] = 1500;
foreach ($bpi as $time => $price) {
	$time = date('d/m/Y', strtotime($time));
	if ($i == 0) $first_time = $time;
	if ($i == (count($bpi) - 1)) {
		$last_time = $time;
		$last_price = $price;
	}
	$arr_time[] = $time;
	$arr_price[] = $price;
	$i++;
}
// $arr_time[] = '11/08/2018'; $arr_price[] = 20000;

$myData->addPoints($arr_price, "Price");
$myData->addPoints($arr_time, "Labels");

$myData->setSerieDescription("Labels", "Time"); 
$myData->setAbscissa("Labels");
$myData->setAxisPosition(0,AXIS_POSITION_BOTTOM);

// $myData->setSerieTicks("Probe 2",4);
// $myData->setAxisName(0,"Temperatures");

// Check cache
$coin = 'BTC';
$ChartHash = md5(date('YmdHi').__FILE__);
@shell_exec('rm ' . LOGS_DIR . '/' . $coin . '-*-Area.png');
$ChartFile = LOGS_DIR . '/' . $coin . '-' . $ChartHash . '-Area.png';
if (is_file($ChartFile) and file_exists($ChartFile)) {
	if (php_sapi_name() == "cli")
		die($ChartFile);

	$handle = fopen($ChartFile, "rb");
	$contents = fread($handle, filesize($ChartFile));
	fclose($handle);
	header("content-type: image/png");
	echo $contents;
	exit;
}

/* Create the pChart object */
$myPicture = new pImage($width,$height,$myData);

/* Turn of Antialiasing */
$myPicture->Antialias = FALSE;

/* Add a border to the picture */
$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_VERTICAL,array("StartR"=>255,"StartG"=>255,"StartB"=>255,"EndR"=>250,"EndG"=>250,"EndB"=>250,"Alpha"=>100));
$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_HORIZONTAL,array("StartR"=>255,"StartG"=>255,"StartB"=>255,"EndR"=>250,"EndG"=>250,"EndB"=>250,"Alpha"=>20));

/* Add a border to the picture */
$myPicture->drawRectangle(0,0,$width-1,$height-1,array("R"=>0,"G"=>0,"B"=>0));

/* Write the chart title */ 
$myPicture->setFontProperties(array("FontName"=>LIB_DIR . "/pChart2.1.4/fonts/Forgotte.ttf","FontSize"=>11));
$myPicture->drawText(190,35,"BTC Price (" . $first_time . ' - ' . $last_time . ')',array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

/* Set the default font */
$myPicture->setFontProperties(array("FontName"=>LIB_DIR . "/pChart2.1.4/fonts/pf_arma_five.ttf","FontSize"=>6));

/* Define the chart area */
$myPicture->setGraphArea(30,40,$width-50,$height-60);

/* Draw the scale */
$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"GridAlpha"=>100,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,"LabelRotation"=>60);
$myPicture->drawScale($scaleSettings);

/* Write the chart legend */
// $myPicture->drawLegend(640,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

/* Turn on Antialiasing */
$myPicture->Antialias = TRUE;

/* Enable shadow computing */
$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

/* Draw the area chart */
$Threshold = "";
$Threshold[] = array("Min"=>10000,"Max"=>20000,"R"=>187,"G"=>220,"B"=>0,"Alpha"=>100);
$Threshold[] = array("Min"=>5000,"Max"=>10000,"R"=>240,"G"=>132,"B"=>20,"Alpha"=>100);
$Threshold[] = array("Min"=>0,"Max"=>5000,"R"=>240,"G"=>91,"B"=>20,"Alpha"=>100);

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
$myPicture->drawAreaChart(array("Threshold"=>$Threshold));

/* Draw a line chart over */
$myPicture->drawLineChart(array("ForceColor"=>TRUE,"ForceR"=>0,"ForceG"=>0,"ForceB"=>0));

/* Draw a plot chart over */
$myPicture->drawPlotChart(array("PlotBorder"=>TRUE,"BorderSize"=>1,"Surrounding"=>-255,"BorderAlpha"=>80));

/* Write the thresholds */
$myPicture->drawThreshold(20000,array("WriteCaption"=>TRUE,"Caption"=>"Very Happy","Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));
$myPicture->drawThreshold(10000,array("WriteCaption"=>TRUE,"Caption"=>"Happy","Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));
$myPicture->drawThreshold($last_price,array("WriteCaption"=>TRUE,"Caption"=>$last_price.' USD',"Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));

/* Render the picture (choose the best way) */
$myPicture->autoOutput($ChartFile);

// Show file name in CLI
if (php_sapi_name() == "cli") die($ChartFile);
