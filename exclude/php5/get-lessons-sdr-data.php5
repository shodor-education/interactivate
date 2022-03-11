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

  // DESCRIPTION
  echoDescription($lesson);

  // KEY TERMS
  echoJsonArray(
    $json,
    "keyTerms",
    "key-terms"
  );

  // OBJECTIVES
  echoJsonArray(
    $json,
    "objectives",
    "objectives"
  );

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
  echoSubjects($lesson);

  // TITLE
  echoTitle($lesson);

  // TOPICS
  echoTopics($lesson);

  echo "---\n";
}
foreach ($files as $name => $url) {
  echo "$name,$url\n";
}

?>

