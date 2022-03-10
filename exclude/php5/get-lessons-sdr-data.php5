<?php
header("Content-Type: text/plain");

include_once("helpers.php5");

$lessons = getSdrResources("Lesson");
$files = array();
while ($lesson = $lessons->fetch_assoc()) {
  $shortname = getShortname($lesson);
  echo "FILENAME::$shortname\n---\n";
  $json = getVersionContentJson($shortname, 2204);

  // ALIGNED STANDARDS OBJECTIVES
  echoAlignedStandardsObjectives($lesson["resourceId"]);

  // ALIGNED TEXTBOOK SECTIONS
  echoAlignedTextbookSections($lesson["resourceId"]);

  // KEY TERMS
  if (isset($json->keyTerms)) {
    echo "key-terms:\n";
    foreach ($json->keyTerms as $term) {
      $term = str_replace("\"", "\\\"", $term);
      echo "  - \"$term\"\n";
    }
  }

  // OBJECTIVES
  echo "objectives:\n";
  foreach ($json->objectives as $objective) {
    $objective = str_replace("\"", "\\\"", $objective);
    echo "  - \"$objective\"\n";
  }

  // STUDENT PREREQUISITES
  if (isset($json->prereqs)) {
    echo "student-prerequisites:\n";
    foreach ($json->prereqs as $fullCategory => $prereqs) {
      preg_match("#<i>(.*)</i>#", $fullCategory, $category);
      $category = (count($category) > 1) ? $category[1]: $fullCategory;
      $category = str_replace(":", "", $category);
      echo "  - category: \"$category\"\n";
      echo "    prereqs:\n";
      foreach ($prereqs as $prereq) {
        $prereq = str_replace("\"", "\\\"", $prereq);
        echo "      - \"$prereq\"\n";
      }
    }
  }

  // SUBJECTS
  echo "subjects:\n";
  $results = getTextValues(
    $sdrDbConn,
    $lesson["versionId"],
    "Primary_Subject"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  // SUGGESTED FOLLOW-UP
  if (isset($json->followUpIntro)) {
    $followUpIntro = xmlToHtmlWithFiles(
      $json->followUpIntro,
      $shortname,
      "lessons",
      "lesson",
      $files
    );
    $files = $followUpIntro[1];
    $followUpIntro[0] = str_replace("\"", "\\\"", $followUpIntro[0]);
    echo "suggested-follow-up: \"$followUpIntro[0]\"\n";
  }

  // TEACHER PREPARATION
  $preps = xmlToHtmlWithFiles(
    $json->preps,
    $shortname,
    "lessons",
    "lesson",
    $files);
  $files = $preps[1];
  $preps[0] = str_replace("\"", "\\\"", $preps[0]);
  echo "teacher-preparation: \"$preps[0]\"\n";

  // TITLE
  echo "title: \"";
  $results = getTextValues(
    $sdrDbConn,
    $lesson["versionId"],
    "Title"
  );
  $result = $results->fetch_assoc();
  echo "$result[str]\"\n";

  // TOPICS
  echo "topics:\n";
  $results = getTextValues(
    $sdrDbConn,
    $lesson["versionId"],
    "Related_Subject"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  echo "---\n";
}
foreach ($files as $name => $url) {
  echo "$name,$url\n";
}

?>

