<?php

// *** requires PHP5 ***

// search for mp3 and flv files
$filter1 = ".mp3";
$filter2 = ".flv";

// url to be added to the path *** NO TRAILING SLASH ***
$url = 'http://www.yoursite.com/phpbbroot/mplayer';

// path to the directory you want to scan
$directory = './media';

$it = new RecursiveDirectoryIterator("$directory");

foreach(new RecursiveIteratorIterator($it) as $file)
{
  if(!((strpos(strtolower($file), $filter1)) === false) || (!((strpos(strtolower($file), $filter2)) === false)))
  {
    $items[] = preg_replace(array("#\\\#", "#\./#"), array("/", "/"), $file);
  }
}

arsort($items);

header("content-type:text/xml;charset=utf-8");

print <<<END
<?xml version='1.0' encoding='UTF-8'?>
<playlist version='1' xmlns='http://xspf.org/ns/0/'>
  <title>bb3 Media Player</title>
  <info>http://www.yoursite.com</info>
  <trackList>

END;

foreach($items as $item)
{
  $title_array = explode('/', $item);
  $title = substr(end($title_array), 0, (strlen(end($title_array)) - 4));
  print <<<END
    <track>
      <title>$title</title>
      <location>$url$item</location>
    </track>

END;
}

print <<<END
  </trackList>
</playlist>

END;

?>