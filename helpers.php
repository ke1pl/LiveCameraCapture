<?php


function url_param_to_date($url_param, $format) {
	$date = (int)$url_param;
	$timestamp = $date*600;
	$date = new DateTime("@".$timestamp);  // will snap to UTC because of the "@timezone" syntax
	//echo $date->format('Y-m-d H:i:sP') . "<br>";  // UTC time
	$date->setTimezone(new DateTimeZone('America/Winnipeg'));

	return $date->format($format);  // Local time
}

function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);

  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function get_files_with_meta_data () {
	$dir    = 'saved/';
	$weeds = array('.', '..');
	$output = (object)null;

	$files = array_diff(scandir($dir, SCANDIR_SORT_DESCENDING), $weeds);

	foreach ($files as $filename) {
    	$url_param = str_replace(['.jpg'], '', $filename);
    	$filename = $dir.$filename;
    
    	$file_with_meta_data = (object)null;
    	$file_with_meta_data->{'url_param'} = $url_param;
    	$file_with_meta_data->{'path'} = $filename;
    	$file_with_meta_data->{'size'} = filesize($filename);
    	$file_with_meta_data->{'last_modify'} = filemtime($filename);
    	
        $output->{$url_param} = $file_with_meta_data;
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