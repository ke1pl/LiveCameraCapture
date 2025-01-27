<?php

include 'helpers.php';

$files = load_db_form_file();

$files_sorted = $files;

function cmp($a, $b)
{
	return $a->{'timestamp'} > $b->{'timestamp'};
}

rsort($files_sorted);
print ('<p>count($files_sorted) is ' . count($files_sorted) . '</p>');

$downloaded_files = [];
$dir = 'saved/';

for ($i = 0; $i < count($files_sorted); $i++) {
	$file = $files_sorted[$i];

	if ($file->{'size'} == 0) {
		$filename = $file->{'path'};
		$t = $file->{'unified_number'};

		$url = 'https://static.ctvnews.ca/cky/webcam/600_wpg_live_eye.jpg?ver=a' . $t;
		file_put_contents($filename, fopen($url, 'r'));

		$file_with_meta_data = get_file_meta_data($dir, $t . '.jpg', true);

		print '<p>' . $filename . ' saved.</p>';
		// var_dump($file);
		// var_dump($files_sorted[$i]);
		// var_dump($file_with_meta_data);

		$files_sorted[$i] = $file_with_meta_data;

		array_push($downloaded_files, $file_with_meta_data);
		usleep(200);
	}

	if (count($downloaded_files) == 1000) {
		break;
	}
}

export_db_to_file($files_sorted);
print ('<p>count($files_sorted) is ' . count($files_sorted) . '</p>');

print ('<p>count($downloaded_files) is ' . count($downloaded_files) . '</p>');

print ('<br />');
/*foreach ($downloaded_files as $file) {
	print (render_file($file, false));
}*/

$files_grouped_by_hash = group_by_key($downloaded_files, 'hash');

foreach ($files_grouped_by_hash as $group) {
	print ('<p>'.$group[0]->{'hash'}.' - '.count($group).'</p>');
}

?>