<?php

include 'helpers.php';

$dir = 'saved/';
$files = load_db_form_file();

print ('Right after load ->' . count($files));
$downloaded_files = [];
$t = date_to_url_param(new DateTime("@" . time()));

for ($i = 145; $i >= 1; $i--) { // 144 images is theoretical max per 24 hours. The CTV website actully keeps only last ~1300 images
	$filename = $dir . $t . '.jpg';

	if (!is_it_a_duplicate_un($files, $t)) {
		$url = 'https://static.ctvnews.ca/cky/webcam/600_wpg_live_eye.jpg?ver=a' . $t;
		file_put_contents($filename, fopen($url, 'r'));

		$file_with_meta_data = get_file_meta_data($dir, $t . '.jpg', true);

		$duplicate = is_it_a_duplicate_file($files, $file_with_meta_data);

		if ($duplicate == false) {
			print '<p>' . $filename . ' saved.</p>';
		} else {
			//var_dump($duplicate);
			file_put_contents($filename, '');
			$file_with_meta_data->{'size'} = 0;
			print '<p>' . $filename . ' is a duplicate of ' . $duplicate->{'path'} . '. Erased to save space.</p>';

		}
		print ('Before update ->' . count($files));
		array_push($files, $file_with_meta_data);
		print ('After update ->' . count($files));

		array_push($downloaded_files, $file_with_meta_data);
		usleep(100);
	}
	$t--;
}

if (count($downloaded_files) > 0) {
	print ('<p>count($downloaded_files) is ' . count($downloaded_files) . ' therefore update DB</p>');

	print ('Right Before export ->' . count($files));

	$files_sorted = $files;

	function cmp($a, $b)
	{
		return $a->{'timestamp'} > $b->{'timestamp'};
	}

	rsort($files_sorted);

	export_db_to_file($files_sorted);
}

//TODO implement a better scan that starts from fetching the current defualt image and when makes real requests to CTV servers do not save a duplicate (let's save an empty file instead of the duplicate)

$count_per_day = 0;
$count_skipped = 0;
$size = 0;

print ('<br/>');

foreach ($downloaded_files as $file) {
	if ($file->{'size'} <> 0) {
		print (render_file($file, false));
		$count_per_day++;
	} else {
		$count_skipped++;
	}
}

print '<p>Added: ' . $count_per_day . '</p>';
print '<p>Skipped: ' . $count_skipped . '</p>';

$last_file = $files[0];

foreach ($files as $file) {
	if ($file->{'size'} <> 0 && $file->{'timestamp'} > $last_file->{'timestamp'}) {
		$last_file = $file;
	}
}

print '<p>The latest one: ' . $last_file->{'path'};

if (filesize("latest.jpg") <> filesize($last_file->{'path'})) {
	copy($last_file->{'path'}, "latest.jpg");
	print ' (updated last image)';
} else {
	print ' (kept the same)';
}

print '</p>';

print (render_file($last_file, false));
?>