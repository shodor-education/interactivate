<?php
header("Content-Type: text/plain");

include_once("helpers.php5");

$files = array();
$discussions = getSdrResources("Discussion");
while ($discussion = $discussions->fetch_assoc()) {
  $shortname = getShortname($discussion);
  echo "FILENAME::$shortname\n---\n";
  $content = getVersionContent($shortname, 2203);

  // ALIGNED STANDARDS OBJECTIVES
  echoAlignedStandardsObjectives($discussion["resourceId"]);

  // ALIGNED TEXTBOOK SECTIONS
  echoAlignedTextbookSections($discussion["resourceId"]);

  // AUDIENCES
  echoAudiences($discussion);

  // DESCRIPTION
  echoDescription($discussion);

  // SUBJECTS
  echoSubjects($discussion);

  // TITLE
  echoTitle($discussion);

  // TOPICS
  echoTopics($discussion);

  echo "---\n";

  $result = xmlToHtmlWithFiles(
    $content,
    $shortname,
    $files
  );
  $content = $result[0];
  $files = $result[1];
  echo "$content\n";
}
foreach ($files as $name => $path) {
  echo "$name,$path\n";
}

?>

