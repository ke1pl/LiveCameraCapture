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

	$files1 = get_files('saved/', true);
	$files2 = get_files('saved2/', true);
	$files3 = get_files('saved3/', true);
	$files4 = get_files('saved4/', true);

	$files = array_merge($files1, $files2, $files3, $files4);

	$files_sorted = $files;

	function cmp($a, $b)
	{
		return $a->{'timestamp'} > $b->{'timestamp'};
	}

	rsort($files_sorted);

	export_db_to_file($files_sorted);

	print ('<p>Total files: ' . count($files) . '</p>');
	/*
		   // Stage 1
		   $zeros = [];
		   $not_zeros = [];

		   foreach ($files as $file) {
			   if ($file->{'size'} == 0) {
				   array_push($zeros, $file);
			   } else {
				   array_push($not_zeros, $file);
			   }
		   }

		   //print ('<p>Zeros: ' . count($zeros) . '</p>');
		   print ('<p>Not zeros: ' . count($not_zeros) . '</p>');*/

	// Stage 2
	/*$files_grouped_by_hash = group_by_key($files, 'hash');

	   $files_without_duplicates = [];
	   foreach ($files_grouped_by_hash as $group) {
		   if (count($group) == 1) {
			   array_push($files_without_duplicates, $group[0]);
		   }

		   if (count($group) > 1) {
			   //var_dump($group);
			   $oldest_file = $group[0];
			   foreach ($group as $file) {
				   if ($file->{'unified_number'} < $oldest_file->{'unified_number'}) {
					   $oldest_file = $file;
				   }
			   }
			   array_push($files_without_duplicates, $oldest_file);
		   }
	   }
	   print ('<p>Files without duplicates: ' . count($files_without_duplicates) . '</p>');*/
	/*
		   // Stage 3
		   $files_grouped_by_un = group_by_key($files_without_duplicates, 'unified_number');

		   $number_of_affected_un = 0;
		   $number_of_duplicates = 0;
		   foreach ($files_grouped_by_un as $file) {
			   if (count($file) > 1) {
				   $number_of_duplicates += count($file);
				   $number_of_affected_un++;
			   }
		   }
		   print ('<p>UN collisions: ' . $number_of_affected_un . '(' . $number_of_duplicates . ' files)</p>');*/

	//export_db_to_file($files_without_duplicates);
	?>
</body>

</html>