<?php
header("Content-Type: text/plain");

$IMAGE_DIR="lessons";
$IMAGE_INFIX="lesson";
$INTERACTIVATE_TYPE="Lesson";

include_once("passwords.php5");
include_once("helpers.php5");

$lessons = $sdrDbConn->query($query);
$files = array();
while ($lesson = $lessons->fetch_assoc()) {
  $shortname = $lesson["url"];
  if (substr($shortname, -1) == "/") {
    $shortname = substr($shortname, 0, -1);
  }
  $shortname = substr($shortname, strrpos($shortname, "/") + 1);
  echo "FILENAME::$shortname\n---\n";

  $query = <<<END
select Version.`content`
from ResourceLink
left join Version on Version.`resourceId` = ResourceLink.`childId`
where ResourceLink.`parentId` = 2204
and Version.`status` = 3
and ResourceLink.`shortName` = "$shortname"
END;
  $versions = $snap2DbConn->query($query);
  $version = $versions->fetch_assoc();
  $json = json_decode($version["content"]);

  // ABSTRACT
  $abstract = xmlToHtmlWithFiles($json->abstract, $shortname, $files);
  $files = $abstract[1];
  $abstract[0] = str_replace("\"", "\\\"", $abstract[0]);
  echo "abstract: \"$abstract[0]\"\n";

  // ALIGNED STANDARDS OBJECTIVES
  echoAlignedStandardsObjectives($lesson["resourceId"]);

  // ALIGNED TEXTBOOK SECTIONS
  echoAlignedTextbookSections($lesson["resourceId"]);

  // ALTERNATE OUTLINE
  $alternate = xmlToHtmlWithFiles($json->alternate, $shortname, $files);
  $files = $alternate[1];
  $alternate[0] = str_replace("\"", "\\\"", $alternate[0]);
  echo "alternate-outline: \"$alternate[0]\"\n";

  // KEY TERMS
  if (isset($json->keyTerms)) {
    echo "key-terms:\n";
    foreach ($json->keyTerms as $term) {
      $term = str_replace("\"", "\\\"", $term);
      echo "  - \"$term\"\n";
    }
  }

  // LESSON OUTLINE
  if (isset($json->sections)) {
    echo "lesson-outline:\n";
    foreach ($json->sections as $heading => $content) {
      echo "  - heading: \"$heading\"\n";
      $content = xmlToHtmlWithFiles($content, $shortname, $files);
      $files = $content[1];
      $content[0] = str_replace("\"", "\\\"", $content[0]);
      echo "    content: \"$content[0]\"\n";
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
    $followUpIntro = xmlToHtmlWithFiles($json->followUpIntro, $shortname, $files);
    $files = $followUpIntro[1];
    $followUpIntro[0] = str_replace("\"", "\\\"", $followUpIntro[0]);
    echo "suggested-follow-up: \"$followUpIntro[0]\"\n";
  }

  // TEACHER PREPARATION
  $preps = xmlToHtmlWithFiles($json->preps, $shortname, $files);
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

