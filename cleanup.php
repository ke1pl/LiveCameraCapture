<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>
<!DOCTYPE html>
<html>
<body>

<h1>Happy? Super Happy!</h1>

<?php
include 'helpers.php';

$files1 = get_files('saved/');
$files2 = get_files('saved2/');
$files3 = get_files('saved3/');
$files4 = get_files('saved4/');

$files = array_merge($files1, $files2, $files3, $files4);
//$files = $files4;

/*
- clean up:
iterate over every file in the array:
- same assumed timestamp but in different folders
- same filesize but differnt assumed timestamp
*/

$zeros = [];
$not_zeros = [];

foreach ($files as $file) {
	if ($file->{'size'} == 0) {
		array_push($zeros, $file);
	} else {
		array_push($not_zeros, $file);
	};
}

print ('<p>Processed url_params: '.count($files).'</p>');
print ('<p>Zeros: '.count($zeros).'</p>');
print ('<p>Not zeros: '.count($not_zeros).'</p>');

function find_by_filesize($files, $file_to_find) {
	foreach ($files as $file) {
		if ($file->{'size'} == $file_to_find->{'size'}) {
			return true;
		}
	}

	return false;
};

$clean = [];
$matchigng_by_the_file_size = [];

foreach ($not_zeros as $file) {
	if (!find_by_filesize($clean, $file)) {
		array_push($clean, $file);
	} else {
		array_push($matchigng_by_the_file_size, $file);	
	};
};
print ('<p>without filesize collisions: '.count($clean).'</p>');


$superclean = [];
$matchigng_by_un = [];
function find_by_un($files, $file_to_find) {
	foreach ($files as $file) {
		if ($file->{'unified_number'} == $file_to_find->{'unified_number'}) {
			return true;
		};
	}

	return false;
};


foreach ($clean as $file) {
	if (!find_by_un($superclean, $file)) {
		array_push($superclean, $file);
	} else {
		array_push($matchigng_by_un, $file);	
	};
};

print ('<p>without filename collisions: '.count($superclean).'</p>');

$files_grouped_by_size = (object)null;

foreach ($matchigng_by_the_file_size as $file) {
	$separator = $file->{'size'};

	if (property_exists($files_grouped_by_size, $separator)) {
    	array_push($files_grouped_by_size->{$separator}, $file);
    } else {
    	$files_grouped_by_size->{$separator} = [$file];
    };
}

foreach($files_grouped_by_size as $key => $value) {
	if (count($value)>0) { //TODO remove?
    	$output = [];
    	foreach ($value as $file) {
			$filename = $file->{'path'};
			$file_size = $file->{'size'};
				$date = url_param_to_date($file->{'unified_number'})->format('Y-m-d H:i');
				$size_readable = human_filesize($file_size);
			
				print('<a href="'.$filename.'"><img loading="lazy" src="'.$filename.'" width="150" height="100" title="'.$date.' '.$size_readable.'"></a>');
    	}    	
    	print '<br/>';
    }
}
/*
foreach ($matchigng_by_the_file_size as $file) {
	$filename = $file->{'path'};
	$file_size = $file->{'size'};
		$date = url_param_to_date($file->{'unified_number'})->format('Y-m-d H:i');
		$size_readable = human_filesize($file_size);
	
		print('<a href="'.$filename.'"><img loading="lazy" src="'.$filename.'" width="150" height="100" title="'.$date.' '.$size_readable.'"></a>');
}*/

?>
</body>
</html>