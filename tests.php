<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include 'helpers.php';
/* date manipulation */

/*
The JS code from the offical iframe to set value for the URL parameter: https://winnipeg.ctvnews.ca/more/live-eye-iframe
  var minutes = 1000 * 600;
  var d = new Date();
  var t= d.getTime();
  var y = Math.round(t / minutes);
  consloe.log(y);

PHP verion and side-by-side comparison:
console.log('t ->', t);
console.log('p ->', <?php echo time(); ?>); //php
console.log('y ->', y);
console.log('p ->', <?php echo ceil(time()/600); ?>); //php
*/



/*
image url 2896390 with text 2025-01-25 13:36:19:
 - reverse calculted timestamp is 1737834000
 - reverse calculated date is 2025-01-25 19:40:00
 
 - calculated timestamp for 13:36:19 is 1737833875 - I will use it in $time_now variable
*/

// TODO improve generator to follow this test data
// - 1737938707301 - 2896565
// - 1737938607301 - 2896564

print ("Inputs:\r\n2896390 - url param\r\n2025-01-25 13:36:19 - date on the image\r\n");

print ("\r\nCalcualtions:\r\n");

print ("\r\nTest 1:\r\n");
$tmp1 = url_param_to_date('2896390')->getTimestamp();
print ($tmp1 . " - calculted timestamp from 2896390\r\n");
$tmp2 = new DateTime("@" . $tmp1);
print ($tmp2->format('Y-m-d H:i:s') . " - calculated date from " . $tmp1 . "\r\n");
$tmp3 = date_to_url_param($tmp2);
print ($tmp3 . " - calculated url param for date above \r\n");

print ("\r\nTest 2:\r\n");
$tmp1 = time();
print ($tmp1 . " - timestamp for 13:36:19\r\n");
$tmp2 = new DateTime("@" . $tmp1);
print ($tmp2->format('Y-m-d H:i:s') . " - calculated date from " . $tmp1 . "\r\n");
$tmp3 = date_to_url_param($tmp2);
print ($tmp3 . " - calculated url param for date above \r\n");

print ("\r\nTest 3:\r\n");
$tmp1 = time();
print ($tmp1 . " - timestamp for 13:40:00\r\n");
$tmp2 = new DateTime("@" . $tmp1);
print ($tmp2->format('Y-m-d H:i:s') . " - calculated date from " . $tmp1 . "\r\n");
$tmp3 = date_to_url_param_alternative($tmp2);
print ($tmp3 . " - calculated url param (alternative) for date above\r\n");

print ("\r\nTest 3.1:\r\n");
$tmp1 = '1737938707'; //JS timestamp 1737938707301
$tmp2 = new DateTime("@" . $tmp1);
print ($tmp2->format('Y-m-d H:i:s') . " - calculated date from " . $tmp1 . "\r\n");
$tmp3 = date_to_url_param($tmp2);
print ($tmp3 . " - calculated url param for date above - expected 2896565\r\n");

print ("\r\nTest 3.2:\r\n");
$tmp1 = '1737938607'; //JS timestamp 1737938607301
$tmp2 = new DateTime("@" . $tmp1);
print ($tmp2->format('Y-m-d H:i:s') . " - calculated date from " . $tmp1 . "\r\n");
$tmp3 = date_to_url_param($tmp2);
print ($tmp3 . " - calculated url param for date above - expected 2896564\r\n");

print ("\r\nTest 4:\r\n");
$tmp1 = 1737833875;
print ($tmp1 . " - timestamp for 13:36:19\r\n");
$tmp2 = new DateTime("@" . $tmp1);
print ($tmp2->format('Y-m-d H:i:s') . " - calculated date from " . $tmp1 . "\r\n");
$tmp3 = date_to_url_param_alternative($tmp2);
print ($tmp3 . " - calculated url param (alternative) for date above\r\n");

print ("\r\nTest 5:\r\n");
//$tmp1 = 1737833875;
//print ($tmp1." - timestamp for 13:36:19\r\n");
//$tmp2 = new DateTime("@".$tmp1);
//print ($tmp2->format('Y-m-d H:i:s')." - calculated date from ".$tmp1."\r\n");
//$tmp3 = date_to_url_param_alternative($tmp2);
//print($tmp3." - calculated url param (alternative) for date above\r\n");
$tmp4 = url_param_to_date_alternative($tmp3);
print ($tmp4->format('Y-m-d H:i:s') . " - calculated date from " . $tmp3 . "\r\n");

print ("\r\nFinal test:\r\n");
$tmp1 = url_param_to_date('2896390')->getTimestamp();
print ($tmp1 . " - calculted timestamp from 2896390\r\n");
$tmp2 = url_param_to_date('2896390');
print ($tmp2->format('Y-m-d H:i:s') . " - calculated date from " . $tmp1 . "\r\n");
$tmp3 = date_to_url_param_alternative($tmp2);
print ($tmp3 . " - calculated url param (alternative) for above\r\n");
$tmp4 = alternative_url_param_to_url_param($tmp3);
print ($tmp4 . " - 'url param'->Date->'url param alternative'->'url param'\r\n"); //expected

print ("\r\nTest 7:\r\n");
$files = [];
$a = (object) null;
$a->{'unified_number'} = '1';
array_push($files, $a);
$b = (object) null;
$b->{'unified_number'} = '2';
array_push($files, $b);

print (is_it_a_duplicate_un($files, '1') ? 'Pass' : 'Fail');
print ("\r\n");
print (is_it_a_duplicate_un($files, '3') ? 'Fail' : 'Pass');
print ("\r\n");

print ("\r\nTest 8:\r\n");
$files = [];
$a = (object) null;
$a->{'size'} = '1';
$a->{'hash'} = '123';
array_push($files, $a);
$b = (object) null;
$b->{'size'} = '1';
$b->{'hash'} = '345';
$c = (object) null;
$c->{'size'} = '2';
$c->{'hash'} = '345';

print (is_it_a_duplicate_file($files, $a) ? 'Pass' : 'Fail'); //same filesize, same hash
print ("\r\n");
print (is_it_a_duplicate_file($files, $b) ? 'Fail' : 'Pass'); //same filesize, diffeent hash
print ("\r\n");
print (is_it_a_duplicate_file($files, $c) ? 'Fail' : 'Pass'); //diffeent filesize
print ("\r\n");


print ("\r\nTest 9:\r\n");
$files = [];
$a = (object) null;
$a->{'size'} = '1';
$a->{'hash'} = '123';
array_push($files, $a);
$b = (object) null;
$b->{'size'} = '1';
$b->{'hash'} = '345';
array_push($files, $b);
$c = (object) null;
$c->{'size'} = '2';
$c->{'hash'} = '345';
array_push($files, $c);

print (count(group_by_key($files, 'size')->{'1'}) == 2 ? 'Pass' : 'Fail');
print ("\r\n");
print (count(group_by_key($files, 'size')->{'2'}) == 1 ? 'Pass' : 'Fail');
print ("\r\n");

print (count(group_by_key($files, 'hash')->{'123'}) == 1 ? 'Pass' : 'Fail');
print ("\r\n");
print (count(group_by_key($files, 'hash')->{'345'}) == 2 ? 'Pass' : 'Fail');
print ("\r\n");
?>