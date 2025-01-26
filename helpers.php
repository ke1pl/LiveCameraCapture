<?php

function url_param_to_date($url_param) {
	$date = (int)$url_param;
	$timestamp = $date*600;
	$date = new DateTime("@".$timestamp);

	return $date;
};

function date_to_url_param($date) {
	$timestamp = $date->getTimestamp();
	return ceil($timestamp/600);
};

function date_to_url_param_alternative($date) {
	$format = 'YmdHis'; //99% correct, check again
	
	$timestamp = ceil($date->getTimestamp()/600)*600; //analizr exisitng tags - rounding might be incorrect
	$date = new DateTime("@".$timestamp);
	
	$date->setTimezone(new DateTimeZone('America/Winnipeg'));
	
	return $date->format($format);
};

function url_param_to_date_alternative($dateString) {
	$format = 'YmdHis'; //99% correct, check again

	$date = DateTime::createFromFormat($format, $dateString, new DateTimeZone('America/Winnipeg'));

	$date->setTimezone(new DateTimeZone('UTC'));

	return $date;
};

function alternative_url_param_to_url_param($dateString) {
	$date = url_param_to_date_alternative($dateString);
	
	return date_to_url_param($date);
};

function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);

  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function get_files($dir) {
	$weeds = array('.', '..');
	$output = [];

	$files = array_diff(scandir($dir, SCANDIR_SORT_DESCENDING), $weeds);

	foreach ($files as $filename) {
    	$file_with_meta_data = (object)null;
    	$file_with_meta_data->{'filename'} = $filename;
    	$file_with_meta_data->{'dir'} = $dir;

		$filename_type = 0;
		if (strlen($filename) == 11) {
			$filename_type = 1;
		};
		if (strlen($filename) == 18) {
			$filename_type = 2;
		};
		// 2895929.jpg - 11
		// 20190502234634.jpg - 18
    	$file_with_meta_data->{'filename_type'} = $filename_type;

		$number = str_replace(['.jpg'], '', $filename);

		$unified_number = $number;
		if ($filename_type == 2) {
			$unified_number = alternative_url_param_to_url_param($number);
		};
    	$file_with_meta_data->{'unified_number'} = $unified_number;

    	$full_path_to_the_file = $dir.$filename;
    	$file_with_meta_data->{'path'} = $full_path_to_the_file;
    	$file_with_meta_data->{'size'} = filesize($full_path_to_the_file);
    	$file_with_meta_data->{'last_modify'} = filemtime($full_path_to_the_file);

		if ($filename_type == 0) {
			print ('<p> Exception found: '.$dir.$filename.' - not including to the list</p>');
		} else {
	        array_push($output, $file_with_meta_data);
		};    	
	}
	
	return $output;
}

function is_it_a_duplicate($files, $file_size) {
	$files_keys = array_keys((array)$files);

	$index = count($files_keys);

	while($index) {
    	$file = $files->{$files_keys[--$index]};
    	if ($file->{'size'} == $file_size) {
    		return $file->{'url_param'};
        }
	}
	
	return false;
}

/**
 * Returns the size of a file without downloading it, or -1 if the file
 * size could not be determined.
 *
 * @param $url - The location of the remote file to download. Cannot
 * be null or empty.
 *
 * @return The size of the file referenced by $url, or -1 if the size
 * could not be determined.
 */
function curl_get_file_size( $url ) {
  // Assume failure.
  $result = -1;

  $curl = curl_init( $url );

  // Issue a HEAD request and follow any redirects.
  curl_setopt( $curl, CURLOPT_NOBODY, true );
  curl_setopt( $curl, CURLOPT_HEADER, true );
  curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' );

  $data = curl_exec( $curl );
  curl_close( $curl );

  if( $data ) {
    $content_length = "unknown";
    $status = "unknown";

    if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
      $status = (int)$matches[1];
    }

    if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
      $content_length = (int)$matches[1];
    }

    // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
    if( $status == 200 || ($status > 300 && $status <= 308) ) {
      $result = $content_length;
    }
  
  	var_dump($data);
  }

  return $result;
}

?>