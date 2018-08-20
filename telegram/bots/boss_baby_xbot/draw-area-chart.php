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
	require_once __DIR__ . '/draw-area-chart_php5.php';
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
// for($i=0;$i<=30;$i++) 
// { 
//     $myPicture->myData->addPoints([rand(1,15)],"Probe 1"); 
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

$myPicture->myData->addPoints($arr_price, "Price");
$myPicture->myData->addPoints($arr_time, "Labels");

$myPicture->myData->setSerieDescription("Labels", "Time"); 
$myPicture->myData->setAbscissa("Labels");
$myPicture->myData->setAxisPosition(0,AXIS_POSITION_BOTTOM);

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

/* Momchil: setSerieTicks only sets it if Serie is defined. Probe 2 isn't
$myPicture->myData->setSerieTicks("Probe 2",4);
*/

// $myPicture->myData->setAxisName(0,"Temperatures");

/* Turn off Anti-aliasing */
$myPicture->Antialias = FALSE;

/* Add a border to the picture */
$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_VERTICAL,["StartColor"=>new pColor(240,240,240,100), "EndColor"=>new pColor(180,180,180,100)]);
$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_HORIZONTAL,["StartColor"=>new pColor(240,240,240,20), "EndColor"=>new pColor(180,180,180,20)]);

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
$myPicture->drawScale(["XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridColor"=>new pColor(240,240,240,100),"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,"LabelRotation"=>60]);

/* Write the chart legend */
// $myPicture->drawLegend(640,20,["Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL]);

/* Turn on Anti-aliasing */
$myPicture->Antialias = TRUE;

/* Enable shadow computing */
$myPicture->setShadow(TRUE,["X"=>1,"Y"=>1,"Color"=>new pColor(0,0,0,10)]);

/* Draw the area chart */
$Threshold = [
    array("Min"=>10000,"Max"=>20000,"Color"=>new pColor(187,220,0,100)),
    array("Min"=>5000,"Max"=>10000,"Color"=>new pColor(240,132,20,100)),
    array("Min"=>0,"Max"=>5000,"Color"=>new pColor(240,91,20,100))
];

$myPicture->setShadow(TRUE,["X"=>1,"Y"=>1,"Color"=>new pColor(0,0,0,20)]);

/* Create the pCharts object */
$pCharts = new pCharts($myPicture);

$pCharts->drawAreaChart(["Threshold"=>$Threshold]);

/* Draw a line chart over */
$pCharts->drawLineChart(["UseForcedColor"=>TRUE,"ForceColor"=>new pColor(0,0,0,100)]);

/* Draw a plot chart over */
$pCharts->drawPlotChart(["PlotBorder"=>TRUE,"BorderSize"=>1,"Surrounding"=>-255,"BorderColor"=>new pColor(50,50,50,80)]);

/* Write the thresholds */
$myPicture->drawThreshold([20000],["WriteCaption"=>TRUE,"Caption"=>"Very Happy","Ticks"=>2,"Color"=>new pColor(0,0,255,70)]);
$myPicture->drawThreshold([10000],["WriteCaption"=>TRUE,"Caption"=>"Happy","Ticks"=>2,"Color"=>new pColor(0,0,255,70)]);
$myPicture->drawThreshold([$last_price],["WriteCaption"=>TRUE,"Caption"=>$last_price.' USD',"Ticks"=>2,"Color"=>new pColor(0,0,255,70)]);

/* Render the picture (choose the best way) */
$myPicture->autoOutput($ChartFile);

// Show file name in CLI
if (php_sapi_name() == "cli") die($ChartFile);
