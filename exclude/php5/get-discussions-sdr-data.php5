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
  echo "audiences:\n";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Interactivate_Audience"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  // DESCRIPTION
  echo "description: \"";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Description"
  );
  $result = $results->fetch_assoc();
  echo "$result[str]\"\n";

  // SUBJECTS
  echo "subjects:\n";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Primary_Subject"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  // TITLE
  echo "title: \"";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Title"
  );
  $result = $results->fetch_assoc();
  echo "$result[str]\"\n";

  // TOPICS
  echo "topics:\n";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Related_Subject"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  echo "---\n";

  $result = xmlToHtmlWithFiles(
    $content,
    $shortname,
    "discussions",
    "discussion",
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

