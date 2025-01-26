<?php

include 'helpers.php';

$dir    = 'saved/';
$files = get_files($dir);

$ignored = 0;
$downloaded_files = [];

$t = date_to_url_param(new DateTime("@".time()));

$tags = [];
foreach ($files as $file) {
	array_push($tags, $file->{'unified_number'});
};

for ($i = 145; $i >= 1; $i--) { // 144 images is theoretical max per 24 hours. The CTV website actully keeps only last ~1300 images
	$filename = $dir.$t.'.jpg';

	if (!in_array($t, $tags)) {
        $url = 'https://static.ctvnews.ca/cky/webcam/600_wpg_live_eye.jpg?ver=a'.$t;
    	file_put_contents($filename, fopen($url, 'r'));
		//$potential_duplicate = is_it_a_duplicate($files, filesize($filename));
    	    
    	$file_with_meta_data = (object)null;
    	$file_with_meta_data->{'unified_number'} = $t;
    	$file_with_meta_data->{'path'} = $filename;
    	$file_with_meta_data->{'size'} = filesize($filename);
    	$file_with_meta_data->{'last_modify'} = filemtime($filename);
    
    	/*if ($potential_duplicate) {
        	file_put_contents($filename, '');
    		$file_with_meta_data->{'size'} = 0;
        	print '<p>'.$filename.' is a duplicate of '.$potential_duplicate.'. Erasing.</p>';
        }*/
    	
        array_push($files, $file_with_meta_data);

    	array_push($downloaded_files, $filename);
		usleep(100);
	}
	$t--;
}

//TODO implement a better scan that starts from fetching the current defualt image and when makes real requests to CTV servers do not save a duplicate (let's save an empty file instead of the duplicate)
//TODO implement scaming of downloaded images and detect duplicates and remove them.

$count_per_day = 0;
$count_skipped = 0;
$size = 0;

foreach ($downloaded_files as $filename) {
	$filename = $filename; //Attention!
	$url_param = str_replace([$dir, '.jpg'], '', $filename);
	$date = url_param_to_date($url_param)->format('d-m-Y H:i');
	$file_size = filesize($filename);
	$size_readable = human_filesize($file_size);

	if ($file_size <> 0) {
		print '<a href="'.$filename.'"><img src="'.$filename.'" width="200" title="'.$date.'" data-file-size="'.$size_readable.'"></a>';
		$count_per_day++;
	} else {
    	$count_skipped++;
    }
}

print '<p>Added: '.$count_per_day.'</p>';
print '<p>Skipped: '.$count_skipped.'</p>';

$last_file = (object)null;
$last_file->{'unified_number'} = 0;

foreach ($files as $file) {
	if (intval($file->{'size'}) <> 0 && intval($file->{'unified_number'}) > intval($last_file->{'unified_number'})) {
    	$last_file = $file;
    }
}


print '<p>Last one: '.$last_file->{'path'}.'</p>';

if (filesize("latest.jpg") <> filesize($last_file->{'path'})) {
	copy($last_file->{'path'}, "latest.jpg");
	print 'updated last image';
}

print '<a href="'.$last_file->{'path'}.'"><img src="'.$last_file->{'path'}.'" width="400"></a>';
?>