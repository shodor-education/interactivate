<?php
header("Content-Type: text/plain");

include_once("helpers.php5");

$files = array();
$content = getVersionContent("guide", 2504);

$result = xmlToHtmlWithFiles(
  $content,
  "guide",
  $files
);
$content = $result[0];
$files = $result[1];
echo "$content\n";
foreach ($files as $name => $path) {
  echo "$name,$path\n";
}

?>

