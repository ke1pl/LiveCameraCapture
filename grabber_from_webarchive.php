<?php

ini_set("allow_url_fopen", 1);

include 'helpers.php';

$dir    = 'saved4/';

$files = get_files_with_meta_data('saved4/');

/*$ignored = 0;
$downloaded_files = [];
$ids = ["2402403","2479767","2481920","2488566","2489998","2491562","2492483","2493379","2494085","2495196","2499155","2499770"];

foreach ($ids as $id) {
	if (!in_array($id.'.jpg', array_keys((array)$files))) {
    	$url = 'https://web.archive.org/web/20240712022545if_/http://static.ctvnews.ca/cky/webcam/600_wpg_live_eye.jpg?ver='.$id;
    	$filename = $dir.$id.'.jpg';
    	file_put_contents($filename, fopen($url, 'r'));
    	array_push($downloaded_files, $filename);
    	usleep(1000);
    } else {
    	print ("$id was skipped</br>");
    }
    	//$filename = $dir.$id.'.jpg';
    	//array_push($downloaded_files, $filename);
}*/

$ignored = 0;
$downloaded_files = [];
$urls_to_download = [];

$url = 'https://static.ctvnews.ca/cky/webcam/600_wpg_live_eye.jpg';
$year = 2024;
	$data_url = "https://web.archive.org/__wb/calendarcaptures/2?url=".urlencode($url)."&date=".$year;
	$json = file_get_contents($data_url);
	$obj = json_decode($json);
	var_dump($obj->items);
	foreach ($obj->items as $item) {
    	$date_str = (string)$item[0];
    	$date_prefix = (strlen($date_str) == 9) ? "0" : "";
    	$date_full = $year.$date_prefix.$date_str;
    	$url_full = "https://web.archive.org/web/".$date_full."if_/".$url;
    
    	print $url_full."<br/>/n";
    
    	if (!in_array($date_full.'.jpg', array_keys((array)$files))) {
        	$filename = $dir.$date_full.'.jpg';
        	file_put_contents($filename, fopen($url_full, 'r'));
        	array_push($downloaded_files, $filename);
        	usleep(500);
    	} else {
        	print ("$date_full was skipped</br>");
    	}
    }

$ignored = 0;
$urls = [];
/*foreach ($urls as $url) {
	if (!in_array($id.'.jpg', array_keys((array)$files))) {
    	$url = 'https://web.archive.org/web/20240712022545if_/http://static.ctvnews.ca/cky/webcam/600_wpg_live_eye.jpg?ver='.$id;
    	$filename = $dir.$id.'.jpg';
    	file_put_contents($filename, fopen($url, 'r'));
    	array_push($downloaded_files, $filename);
    	usleep(1000);
    } else {
    	print ("$id was skipped</br>");
    }
    	//$filename = $dir.$id.'.jpg';
    	//array_push($downloaded_files, $filename);
}*/

foreach ($downloaded_files as $filename) {
	$filename = $filename; //Attention!
	$url_param = str_replace([$dir, '.jpg'], '', $filename);
	$date = url_param_to_date($url_param, 'd-m-Y H:i');
	$file_size = filesize($filename);
	$size_readable = human_filesize($file_size);

		print '<a href="'.$filename.'"><img src="'.$filename.'" width="200" title="'.$date.'" data-file-size="'.$size_readable.'"></a>';
}

?>