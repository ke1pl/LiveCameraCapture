<?php

function url_param_to_date($url_param)
{
	$date = (int) $url_param;
	$timestamp = $date * 600;
	$date = new DateTime("@" . $timestamp);

	return $date;
}

function date_to_url_param($date)
{
	$timestamp = $date->getTimestamp();
	return ceil($timestamp / 600);
}

function date_to_url_param_alternative($date)
{
	$format = 'YmdHis'; //99% correct, check again

	$timestamp = ceil($date->getTimestamp() / 600) * 600; //analizr exisitng tags - rounding might be incorrect
	$date = new DateTime("@" . $timestamp);

	$date->setTimezone(new DateTimeZone('America/Winnipeg'));

	return $date->format($format);
}

function url_param_to_date_alternative($dateString)
{
	$format = 'YmdHis'; //99% correct, check again

	$date = DateTime::createFromFormat($format, $dateString, new DateTimeZone('America/Winnipeg'));

	$date->setTimezone(new DateTimeZone('UTC'));

	return $date;
}

function alternative_url_param_to_url_param($dateString)
{
	$date = url_param_to_date_alternative($dateString);

	return date_to_url_param($date);
}

function human_filesize($bytes, $decimals = 2)
{
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);

	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function get_files($dir)
{
	$weeds = array('.', '..');
	$output = [];

	$files = array_diff(scandir($dir, SCANDIR_SORT_DESCENDING), $weeds);

	foreach ($files as $filename) {
		$file_with_meta_data = (object) null;
		$file_with_meta_data->{'filename'} = $filename;
		$file_with_meta_data->{'dir'} = $dir;

		$filename_type = 0;
		if (strlen($filename) == 11) {
			$filename_type = 1;
		}
		
		if (strlen($filename) == 18) {
			$filename_type = 2;
		}
		
		// 2895929.jpg - 11
		// 20190502234634.jpg - 18
		$file_with_meta_data->{'filename_type'} = $filename_type;

		$number = str_replace(['.jpg'], '', $filename);

		$unified_number = $number;
		if ($filename_type == 2) {
			$unified_number = alternative_url_param_to_url_param($number);
		}
		
		$file_with_meta_data->{'unified_number'} = $unified_number;

		$full_path_to_the_file = $dir . $filename;
		$file_with_meta_data->{'path'} = $full_path_to_the_file;
		$file_with_meta_data->{'size'} = filesize($full_path_to_the_file);
		$file_with_meta_data->{'last_modify'} = filemtime($full_path_to_the_file);

		if ($filename_type == 0) {
			print ('<p> Exception found: ' . $dir . $filename . ' - not including to the list</p>');
		} else {
			array_push($output, $file_with_meta_data);
		}
		
	}

	return $output;
}

function is_it_a_duplicate($files, $file_size)
{
	$files_keys = array_keys((array) $files);

	$index = count($files_keys);

	while ($index) {
		$file = $files->{$files_keys[--$index]};
		if ($file->{'size'} == $file_size) {
			return $file->{'url_param'};
		}
	}

	return false;
}

/* date manipulation */

/*
The JS code from the offical iframe to set value for the URL parameter: https://winnipeg.ctvnews.ca/more/live-eye-iframe
  var minutes = 1000 * 600;
  var d = new Date();
  var t= d.getTime();
  var y = Math.round(t / minutes);

PHP verion and side-by-side comparison:
console.log('t ->', t);
console.log('p ->', <?php echo time(); ?>); //php
console.log('y ->', y);
console.log('p ->', <?php echo ceil(time()/600); ?>); //php
*/



/*
image url 2896390 with text 2025-01-25 13:36:19:
 - reverse calculted timestamp is 1737834000
 - reverse calculated date is 2025-01-25 19:40:00
 
 - calculated timestamp for 13:36:19 is 1737833875 - I will use it in $time_now variable
*/

/* tests */

/*

print ("Inputs:\r\n2896390 - url param\r\n2025-01-25 13:36:19 - date on the image\r\n");

print ("\r\nCalcualtions:\r\n");

print ("\r\nTest 1:\r\n");
$tmp1 = url_param_to_date('2896390')->getTimestamp();
print ($tmp1." - calculted timestamp from 2896390\r\n");
$tmp2 =  new DateTime("@".$tmp1);
print ($tmp2->format('Y-m-d H:i:s')." - calculated date from ".$tmp1."\r\n");
$tmp3 = date_to_url_param($tmp2);
print ($tmp3." - calculated url param for date above \r\n");

print ("\r\nTest 2:\r\n");
$tmp1 = 1737833875;
print ($tmp1." - timestamp for 13:36:19\r\n");
$tmp2 =  new DateTime("@".$tmp1);
print ($tmp2->format('Y-m-d H:i:s')." - calculated date from ".$tmp1."\r\n");
$tmp3 = date_to_url_param($tmp2);
print ($tmp3." - calculated url param for date above \r\n");

print ("\r\nTest 3:\r\n");
$tmp1 = 1737834000; 
print ($tmp1." - timestamp for 13:40:00\r\n");
$tmp2 = new DateTime("@".$tmp1);
print ($tmp2->format('Y-m-d H:i:s')." - calculated date from ".$tmp1."\r\n");
$tmp3 = date_to_url_param_alternative($tmp2);
print($tmp3." - calculated url param (alternative) for date above\r\n");

print ("\r\nTest 4:\r\n");
$tmp1 = 1737833875;
print ($tmp1." - timestamp for 13:36:19\r\n");
$tmp2 = new DateTime("@".$tmp1);
print ($tmp2->format('Y-m-d H:i:s')." - calculated date from ".$tmp1."\r\n");
$tmp3 = date_to_url_param_alternative($tmp2);
print($tmp3." - calculated url param (alternative) for date above\r\n");

print ("\r\nTest 5:\r\n");
//$tmp1 = 1737833875;
//print ($tmp1." - timestamp for 13:36:19\r\n");
//$tmp2 = new DateTime("@".$tmp1);
//print ($tmp2->format('Y-m-d H:i:s')." - calculated date from ".$tmp1."\r\n");
//$tmp3 = date_to_url_param_alternative($tmp2);
//print($tmp3." - calculated url param (alternative) for date above\r\n");
$tmp4 = url_param_to_date_alternative($tmp3);
print($tmp4->format('Y-m-d H:i:s')." - calculated date from ".$tmp3."\r\n");

print("\r\nFinal test:\r\n"); 
$tmp1 = url_param_to_date('2896390')->getTimestamp();
print ($tmp1." - calculted timestamp from 2896390\r\n");
$tmp2 = url_param_to_date('2896390');
print ($tmp2->format('Y-m-d H:i:s')." - calculated date from ".$tmp1."\r\n");
$tmp3 = date_to_url_param_alternative($tmp2);
print($tmp3." - calculated url param (alternative) for above\r\n");
$tmp4 = alternative_url_param_to_url_param($tmp3);
print($tmp4." - 'url param'->Date->'url param alternative'->'url param'\r\n"); //expected
*/
?>