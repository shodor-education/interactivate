<?php
header("Content-Type: text/plain");

include_once("helpers.php5");

$worksheets = getSdrResources("Worksheet");
while ($worksheet = $worksheets->fetch_assoc()) {
  $shortname = getShortname($worksheet);

  $results = getTextValues(
    $sdrDbConn,
    $worksheet["versionId"],
    "Title"
  );
  $result = $results->fetch_assoc();
  $title = $result["str"];

  $search  = array(" ", "(", ")", "'", "\"", ",", "!", "?", ":");
  $replace = array("_", "_", "_", "_", "_" , "_", "_", "_", "_");
  $snap2ResourceName = str_replace($search, $replace, $title);
  $snap2ResourceName = str_replace("\\", "", $snap2ResourceName);
  $snap2ResourceName = substr($snap2ResourceName, 0, 64);
  $query = <<<END
select Version.`content`
from ResourceLink
left join Version on Version.`resourceId` = ResourceLink.`childId`
where ResourceLink.`parentId` = 2330
and Version.`status` = 3
and ResourceLink.`shortName` = "$snap2ResourceName"
END;
  $versions = $snap2DbConn->query($query);
  if (mysqli_num_rows($versions) == 0) {
    $query = str_replace($snap2ResourceName, $shortname, $query);
    $versions = $snap2DbConn->query($query);
    if (mysqli_num_rows($versions) == 0) {
      continue;
    }
  }
  $version = $versions->fetch_assoc();
  $json = json_decode($version["content"]);
  $path = str_replace("\\", "", $json->doc->path);

  $urls[$shortname] = "http://shodor.org/media/$path";

  echo "FILENAME::$shortname\n---\n";

  // ALIGNED STANDARDS OBJECTIVES
  echoAlignedStandardsObjectives($worksheet["resourceId"]);

  // ALIGNED TEXTBOOK SECTIONS
  echoAlignedTextbookSections($worksheet["resourceId"]);

  // AUDIENCES
  echoAudiences($worksheet);

  // DESCRIPTION
  echoDescription($worksheet);

  // SUBJECTS
  echoSubjects($worksheet);

  // TITLE
  echo "title: \"$title\"\n";

  // TOPICS
  echoTopics($worksheet);

  echo "---\n";
}
foreach ($urls as $name => $url) {
  echo "$name,$url\n";
}

?>

