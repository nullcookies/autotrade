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
if (PHP_VERSION_ID < 70100) {
	require_once __DIR__ . '/draw-line-chart_php5.php';
	exit;
}

/* CAT:Area Chart */

/* pChart library inclusions */
// require_once(LIB_DIR . "/pChart2.0/vendor/autoload.php");
require_once(LIB_DIR . "/pChart2.0/vendor/bootstrap.php");

use pChart\pColor;
use pChart\pDraw;
use pChart\pCharts;

$width = 1000;
$height = 650;

/* Create the pChart object */
$myPicture = new pDraw($width,$height);

/* Populate the pData object */
// $BaseTs = mktime(0,0,0,12,25,2011);
// $LastIn = 0; 
// $LastOut = 0;
// $LastInArr = []; 
// // $LastOutArr = [];
// $BaseTsArr = [];

// for($i=0; $i<= 1440; $i++)
// {
//     $LastIn  = abs($LastIn + rand(-1000,+1000));
//     $LastOut = abs($LastOut + rand(-1000,+1000));
//     $LastInArr[]  = $LastIn;
//     // $LastOutArr[] = $LastOut;
//     $BaseTsArr[]  = $BaseTs+$i*60;
// }

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

// dump($arr_time);
// dump($arr_price);
// dump($LastInArr);
// dump($BaseTsArr);
// die;

// $myPicture->myData->addPoints($LastInArr,"Price");
$myPicture->myData->addPoints($arr_price,"Price");
// $myPicture->myData->addPoints($LastOutArr,"Outbound");
// $myPicture->myData->addPoints($BaseTsArr,"Labels");
$myPicture->myData->addPoints($arr_time,"Labels");

// $myPicture->myData->setAxisName(0,"Bandwidth");
// $myPicture->myData->setAxisDisplay(0,AXIS_FORMAT_TRAFFIC);
$myPicture->myData->setSerieDescription("Labels","time");
$myPicture->myData->setAbscissa("Labels");
// $myPicture->myData->setXAxisDisplay(AXIS_FORMAT_TIME,"H:00");

// $myPicture->myData->addPoints($arr_price, "Price");
// $myPicture->myData->addPoints($arr_time, "Labels");

// $myPicture->myData->setSerieDescription("Labels", "Time"); 
// $myPicture->myData->setAbscissa("Labels");
$myPicture->myData->setAxisPosition(0,AXIS_POSITION_BOTTOM);

// Check cache
$coin = 'BTC';
$ChartHash = md5(date('YmdHi').__FILE__);
@shell_exec('rm ' . LOGS_DIR . '/' . $coin . '-*-Line.png');
$ChartFile = LOGS_DIR . '/' . $coin . '-' . $ChartHash . '-Line.png';
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

/* Turn off Anti-aliasing */
$myPicture->Antialias = FALSE;

/* Draw a background */
$myPicture->drawFilledRectangle(0,0,$width,$height,["Color"=>new pColor(90,90,90), "Dash"=>TRUE, "DashColor"=>new pColor(120,120,120)]); 

/* Overlay with a gradient */ 
$Settings = ["StartColor"=>new pColor(255,255,255,255), "EndColor"=>new pColor(255,255,255,255)];
$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_VERTICAL,$Settings);
$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_HORIZONTAL,$Settings);

/* Add a border to the picture */
$myPicture->drawRectangle(0,0,$width-1,$height-1,["Color"=>new pColor(0,0,0)]);

/* Write the chart title */ 
$myPicture->setFontProperties(array("FontName"=>LIB_DIR . "/pChart2.0/pChart/fonts/Forgotte.ttf","FontSize"=>11));
$myPicture->drawText(190,35,"BTC Price (" . $first_time . ' - ' . $last_time . ')',array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

/* Set the default font */
$myPicture->setFontProperties(array("FontName"=>LIB_DIR . "/pChart2.0/pChart/fonts/pf_arma_five.ttf","FontSize"=>6));

/* Define the chart area */
$myPicture->setGraphArea(30,40,$width-50,$height-60);

/* Draw the scale */
$myPicture->drawScale([
    "XMargin"=>10,
    "YMargin"=>10,
    "Floating"=>TRUE,
    "GridColor"=>new pColor(240,240,240),
    "RemoveSkippedAxis"=>TRUE,
    "DrawSubTicks"=>FALSE,
    // "Mode"=>SCALE_MODE_START0,
    "LabelingMethod"=>LABELING_DIFFERENT,
    "LabelRotation"=>60
]);

/* Turn on Anti-aliasing */
$myPicture->Antialias = TRUE;

/* Draw the line chart */
$myPicture->setShadow(TRUE,["X"=>1,"Y"=>1,"Color"=>new pColor(0,0,0,10)]);
(new pCharts($myPicture))->drawLineChart(["UseForcedColor"=>TRUE,"ForceColor"=>new pColor(255,0,0,0)]);

/* Write a label over the chart */ 
// $myPicture->writeLabel(["Price"],[720]);

/* Write the chart legend */
// $myPicture->drawLegend(580,20,["Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL]);

/* Write the thresholds */
$myPicture->drawThreshold([$last_price],["WriteCaption"=>TRUE,"Caption"=>$last_price.' USD',"Ticks"=>2,"Color"=>new pColor(0,0,255,70)]);

/* Render the picture (choose the best way) */
$myPicture->autoOutput($ChartFile);

// Show file name in CLI
if (php_sapi_name() == "cli") die($ChartFile);
