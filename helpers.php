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

	return round($timestamp / 600);
}

function date_to_url_param_alternative($date)
{
	$format = 'YmdHis'; //99% correct, check again
	$timestamp = round($date->getTimestamp() / 600) * 600; //analizr exisitng tags - rounding might be incorrect
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
		$full_path_to_the_file = $dir . $filename;

		$filename_type = 0;
		if (strlen($filename) == 11) {// 2895929.jpg - 11
			$filename_type = 1;
		}
		if (strlen($filename) == 18) {// 20190502234634.jpg - 18
			$filename_type = 2;
		}

		$number = str_replace(['.jpg'], '', $filename);
		$unified_number = $number;
		if ($filename_type == 2) {
			$unified_number = alternative_url_param_to_url_param($number);
		}

		$file_with_meta_data = (object) null;
		$file_with_meta_data->{'unified_number'} = (int) $unified_number;
		//$file_with_meta_data->{'filename'} = $filename;
		//$file_with_meta_data->{'filename_type'} = $filename_type;
		//$file_with_meta_data->{'dir'} = $dir;
		$file_with_meta_data->{'path'} = $full_path_to_the_file;
		$file_with_meta_data->{'size'} = filesize($full_path_to_the_file);
		$file_with_meta_data->{'hash'} = md5(file_get_contents($full_path_to_the_file));
		//$file_with_meta_data->{'last_modify'} = filemtime($full_path_to_the_file);

		if ($filename_type == 0) {
			print ('<p> Exception found: ' . $dir . $filename . ' - not including to the list</p>');
		} else {
			array_push($output, $file_with_meta_data);
		}
	}

	return $output;
}

function is_it_a_duplicate_un($files, $un)
{
	foreach ($files as $file) {
		if ($file->{'unified_number'} == $un) {
			return true;
		}
	}

	return false;
}

function is_it_a_duplicate_file($files, $file_to_find)
{
	foreach ($files as $file) {
		if ($file->{'size'} == $file_to_find->{'size'} && $file->{'hash'} == $file_to_find->{'hash'}) {
			return true;
		}
	}

	return false;
}

function group_by_key($files, $key)
{
	$files_grouped_by_key = (object) null;

	foreach ($files as $file) {
		$value_for_grouping = $file->{$key};

		if (property_exists($files_grouped_by_key, $value_for_grouping)) {
			array_push($files_grouped_by_key->{$value_for_grouping}, $file);
		} else {
			$files_grouped_by_key->{$value_for_grouping} = [$file];
		}
	}

	return $files_grouped_by_key;
}

function export_db_to_file($files)
{
	$content = json_encode($files);
	file_put_contents('data/files.json', $content);
}

function load_db_form_file()
{
	$content = file_get_contents('data/files.json');

	return json_decode($content);
}

?>