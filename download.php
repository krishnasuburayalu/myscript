<?php

//  php download.php  -f songs1.txt -d /home/user/Music/



//get HTML file name
$options = getopt("f:d:");

if(!isset($options) || empty($options['f']) || empty($options['d']) ){
  plog( "No HTML / Directory found");
  return false;
}

$file_path = $options['f'];
$dir_path = $options['d'];

//check file is avaliable
if(!file_exists($file_path)){
  plog("Sorce HTML was not found");
  return false;
}

//check dir avaliable
if(!file_exists($dir_path)){
  plog("Destination Directory was not found");
  return false;
}

//load HTML
$doc = new DOMDocument();
$doc->loadHTML(file_get_contents($file_path));
$xpath = new DOMXPath($doc);

// We starts from the root element
$query = '//html/body/ul/li/a';
$entries = $xpath->query($query);
$total = $entries->length;

$counter = 1;
foreach ($entries as $entry) {
  $url = $entry->getAttribute('href');
  $link_name = str_replace(array(" ","/", ",", "-","–","TamilWire.com","TamilWire.Com", "TamilTunes.com"),"", $entry->nodeValue);
  $file_path = $dir_path . $link_name;
  plog("[" .$counter . " of " . $total . "]  :: " .  $link_name );
  get_file($url, $file_path);
  $counter++;
}

function get_file($url = NULL, $file_path = NULL){
  if($url == NULL || $file_path == NULL){
    return false;
  }
  set_time_limit(0);
  //This is the file where we save the    information
  $fp = fopen ($file_path, 'w+');
  //  $fp = fopen (dirname(__FILE__) . '/localfile.tmp', 'w+');
  //Here is the file we are downloading, replace spaces with %20
  $ch = curl_init(str_replace(" ","%20",$url));
  curl_setopt($ch, CURLOPT_TIMEOUT, 50);
  // write curl response to file
  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  // get curl response
  curl_exec($ch);
  curl_close($ch);
  fclose($fp);
}

function plog($message = NULL, $type = 'd'){
    echo $message . "\n";
}


?>
