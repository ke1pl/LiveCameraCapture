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

$files1 = get_files_with_meta_data('saved/');
$files2 = get_files_with_meta_data('saved2/');
$files3 = get_files_with_meta_data('saved3/');
$files4 = get_files_with_meta_data('saved4/');

$files = (object) array_merge((array) $files1, (array) $files2, (array) $files3, (array) $files4);

/*
asusmed timestamp to date()
*/


/*
date to asusmed timestamp ()
*/

/*
- unify:
Get one array
Calulate assumed timestamp for images that do not comply the format
Get file path
Get file size
*/

/*
- clean up:
iterate over every file in the array:
- same assumed timestamp but in different folders
- same filesize but differnt assumed timestamp
*/



function find_by_filesize($files, $file_to_find) {
	$file_to_find_size = $file_to_find->{'size'};
	
	if ($file_to_find_size == 0) {
		return false;
	};

	foreach ($files as $file) {
		$file_in_the_folder_size = $file->{'size'};
		
		if ($file_in_the_folder_size == $file_to_find_size && $file_to_find->{'url_param'} <> $file_to_find->{'url_param'}) {
			return true;
		}
	}

	return false;
};

?>
</body>
</html>