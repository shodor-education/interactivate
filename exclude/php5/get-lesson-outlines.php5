<?php
header("Content-Type: text/plain");
include_once("helpers.php5");
function echoSections($sections, $shortname, $files) {
  foreach ($sections as $heading => $content) {
    $result = xmlToHtmlWithFiles(
      $content,
      $shortname,
      $files
    );
    $content = $result[0];
    $files = $result[1];
    echo "<li>\n<h3>$heading</h3>\n$content\n</li>\n";
  }
  return $files;
}
echoJsonHtml("Lesson", 2204, "sections", "echoSections");
?>

