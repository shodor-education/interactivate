<?php
header("Content-Type: text/plain");
include_once("helpers.php5");
$files = array();
$activities = getSdrResources("Activity");
while ($activity = $activities->fetch_assoc()) {
  $shortname = getShortname($activity);

  $query = <<<END
select Version.`content`
from Directory
left join DirectoryLink on DirectoryLink.`childId` = Directory.`id`
left join Resource on Resource.`canonParentId` = Directory.`id`
left join ResourceLink on ResourceLink.`childId` = Resource.`id`
left join Version on Version.`id` = Resource.`liveVersionId`
where Directory.`canonParentId` = 2202
and ResourceLink.`shortname` = "help"
and DirectoryLink.`shortName` = "$shortname"
END;
  $versions = $snap2DbConn->query($query);
  $version = $versions->fetch_assoc();
  $content = $version["content"];
  $content = str_replace("&ndash;", "&#8211;", $content);
  $content = str_replace("&ne;", "&#8800;", $content);
  $xml = simplexml_load_string($content);
  $description = $xml->xpath("//section[@mapping='description']");
  if ($description) {
    $description = $description[0]->asXML();
    $result = xmlToHtmlWithFiles(
      $description,
      $shortname,
      "activities/help",
      "help",
      $files
    );
    $description = $result[0];
    $files = $result[1];
    if ($description) {
      echo "FILENAME::$shortname\n";
      echo "$description\n";
    }
  }
}
foreach ($files as $name => $path) {
  echo "$name,$path\n";
}
?>

