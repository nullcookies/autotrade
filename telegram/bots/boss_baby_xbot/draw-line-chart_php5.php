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
	require_once __DIR__ . '/draw-line-chart.php';
	exit;
}

/* CAT:Area Chart */

/* Set the default timezone */
date_default_timezone_set('Etc/GMT');

/* pChart library inclusions */
include(LIB_DIR . "/pChart2.1.4/class/pData.class.php");
include(LIB_DIR . "/pChart2.1.4/class/pDraw.class.php");
include(LIB_DIR . "/pChart2.1.4/class/pImage.class.php");

$width = 1000;
$height = 650;

/* Create and populate the pData object */
$MyData = new pData();

// $BaseTs = mktime(0,0,0,12,25,2011);
// $LastIn = 0; $LastOut = 0;
// for($i=0; $i<= 1440; $i++)
// {
//     $LastIn  = abs($LastIn + rand(-1000,+1000));
//     $LastOut = abs($LastOut + rand(-1000,+1000));
//     $MyData->addPoints($LastIn,"Inbound");
//     $MyData->addPoints($LastOut,"Outbound");

//     $MyData->addPoints($BaseTs+$i*60,"TimeStamp");
// }
// $MyData->setAxisName(0,"Bandwidth");
// $MyData->setAxisDisplay(0,AXIS_FORMAT_TRAFFIC);
// $MyData->setSerieDescription("TimeStamp","time");
// $MyData->setAbscissa("TimeStamp");
// $MyData->setXAxisDisplay(AXIS_FORMAT_TIME,"H:00"); 

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

// $MyData->addPoints($LastInArr,"Price");
$MyData->addPoints($arr_price,"Price");
// $MyData->addPoints($LastOutArr,"Outbound");
// $MyData->addPoints($BaseTsArr,"Labels");
$MyData->addPoints($arr_time,"Labels");

// $MyData->setAxisName(0,"Bandwidth");
// $MyData->setAxisDisplay(0,AXIS_FORMAT_TRAFFIC);
$MyData->setSerieDescription("Labels","time");
$MyData->setAbscissa("Labels");
// $MyData->setXAxisDisplay(AXIS_FORMAT_TIME,"H:00");

// $MyData->addPoints($arr_price, "Price");
// $MyData->addPoints($arr_time, "Labels");

// $MyData->setSerieDescription("Labels", "Time"); 
// $MyData->setAbscissa("Labels");
$MyData->setAxisPosition(0,AXIS_POSITION_BOTTOM);

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

/* Create the pChart object */
$myPicture = new pImage($width,$height,$MyData);

/* Turn of Antialiasing */
$myPicture->Antialias = FALSE;

/* Draw a background */
$Settings = array("R"=>90, "G"=>90, "B"=>90, "Dash"=>1, "DashR"=>120, "DashG"=>120, "DashB"=>120);
$myPicture->drawFilledRectangle(0,0,$width,$height,$Settings); 

/* Overlay with a gradient */ 
$Settings = array("StartR"=>255, "StartG"=>255, "StartB"=>255, "EndR"=>255, "EndG"=>250, "EndB"=>250, "Alpha"=>250);
$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_VERTICAL,$Settings); 
$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_HORIZONTAL,$Settings); 

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
$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"RemoveSkippedAxis"=>TRUE,"DrawSubTicks"=>FALSE,/*"Mode"=>SCALE_MODE_START0,*/"LabelingMethod"=>LABELING_DIFFERENT,"LabelRotation"=>60);
$myPicture->drawScale($scaleSettings);

/* Turn on Antialiasing */
$myPicture->Antialias = TRUE;

/* Draw the line chart */
$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
$myPicture->drawLineChart();

/* Write a label over the chart */ 
// $myPicture->writeLabel("Inbound",720);

/* Write the chart legend */
// $myPicture->drawLegend(580,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

/* Write the thresholds */
$myPicture->drawThreshold($last_price,array("WriteCaption"=>TRUE,"Caption"=>$last_price.' USD',"Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));

/* Render the picture (choose the best way) */
$myPicture->autoOutput($ChartFile);

// Show file name in CLI
if (php_sapi_name() == "cli") die($ChartFile);
