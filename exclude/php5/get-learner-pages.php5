<?php
header("Content-Type: text/plain");

include_once("passwords.php5");

$DB_SERVER = "mysql-be-yes-I-really-mean-prod.shodor.org";
$SNAP2_DB_NAME = "db_snap";
$SNAP2_DB_USER = "db_snap_user";

$SDR_DB_NAME = "db_sdr";
$SDR_DB_USER = "search_sdr";

$snap2DbConn = new mysqli($DB_SERVER, $SNAP2_DB_USER, $SNAP2_DB_PASS, $SNAP2_DB_NAME);
if ($snap2DbConn->connect_error) {
  die("Database connection failed: " . $snap2DbConn->connect_error);
}
$sdrDbConn = new mysqli($DB_SERVER, $SDR_DB_USER, $SDR_DB_PASS, $SDR_DB_NAME);
if ($sdrDbConn->connect_error) {
  die("Database connection failed: " . $sdrDbConn->connect_error);
}

$query = <<<END
select Directory.`name`, Version.`content`
from Directory
left join Resource on Resource.`canonParentId` = Directory.`id`
left join Version on Version.`id` = Resource.`liveVersionId`
where Directory.`canonParentId` = 2202
and Resource.`name` = "learner"
order by Directory.`name`
END;

$activities = $snap2DbConn->query($query);

$images = array();
while ($activity = $activities->fetch_assoc()) {
  $query = <<<END
select replace(replace(UrlTV.`entry`, "http://www.shodor.org/interactivate/activities/", ""), "/", "") as shortname
from SDRProject
left join SDRProjectField on SDRProjectField.`projectId` = SDRProject.`id`
left join SDRField on SDRField.`id` = SDRProjectField.`fieldId`
left join SDRFieldValue on SDRFieldValue.`fieldId` = SDRField.`id`
left join SDRTextValue on SDRTextValue.`valueId` = SDRFieldValue.`valueId`
left join SDRVersionFieldValue on (SDRVersionFieldValue.`fieldId` = SDRField.`id` and SDRVersionFieldValue.`valueId` = SDRTextValue.`valueId`)
left join SDRVersion on SDRVersion.`id` = SDRVersionFieldValue.`versionId`
left join SDRVersionFieldValue as TitleVFV on TitleVFV.`versionId` = SDRVersion.`id`
left join SDRField as TitleF on TitleF.`id` = TitleVFV.`fieldId`
left join SDRTextValue as TitleTV on TitleTV.`valueId` = TitleVFV.`valueId`
left join SDRVersionFieldValue as UrlVFV on UrlVFV.`versionId` = SDRVersion.`id`
left join SDRField as UrlF on UrlF.`id` = UrlVFV.`fieldId`
left join SDRTextValue as UrlTV on UrlTV.`valueId` = UrlVFV.`valueId`
where SDRProject.`id` = 3
and SDRField.`name` = "Interactivate_Type"
and SDRTextValue.`entry` = "Activity"
and SDRVersion.`state` = "live"
and TitleF.`name` = "Title"
and UrlF.`name` = "Url"
and (TitleTV.`entry` = "$activity[name]" or UrlTV.`entry` = concat("http://www.shodor.org/interactivate/activities/$activity[name]/"))
END;
  $shortnameResults = $sdrDbConn->query($query);

  while ($shortnameResult = $shortnameResults->fetch_assoc()) {
    $shortname = $shortnameResult["shortname"];

    $html = $activity["content"];
    $html = preg_replace("#\s*<XMLResource>\s*\n#", "", $html);
    $html = preg_replace("#\s*<section mapping=\"content\">\s*\n#", "", $html);
    $html = preg_replace("#<link base=\"PATH:dictionary\" href=\"/(.)#", "<a href=\"{{ '/dictionary/$1' | relative_url }}", $html);
    $html = preg_replace("#<link href=\"http://www.shodor.org/interactivate/dictionary/(.)/#", "<a href=\"{{ '/dictionary/$1' | relative_url }}", $html);
    $html = preg_replace("#<link href=\"/interactivate/dictionary/(.)/?#", "<a href=\"{{ '/dictionary/$1' | relative_url }}", $html);
    $html = str_replace("<b>", "<strong>", $html);
    $html = str_replace("<i>", "<em>", $html);
    $html = str_replace("<u>", "<strong>", $html);
    $html = str_replace("</u>", "</u>", $html);
    $html = str_replace("</i>", "</em>", $html);
    $html = str_replace("</b>", "</strong>", $html);
    $html = str_replace("</link>", "</a>", $html);
    $html = preg_replace("#\s*</section>\s*\n#", "", $html);
    $html = preg_replace("#</XMLResource>#", "", $html);
    preg_match_all("<link metaid=\"(\d*)\">", $html, $linkMatches);
    foreach ($linkMatches[1] as $cserdId) {
      $query = <<<END
        select url
        from SDRResource
        where cserdid = $cserdId
END;
      $urlResults = $sdrDbConn->query($query);
      while ($urlResult = $urlResults->fetch_assoc()) {
        $url = str_replace("http://www.shodor.org/interactivate", "", $urlResult["url"]);
        $url = preg_replace("/\/$/", "", $url);
        $html = str_replace("<link metaid=\"$cserdId\">", "<a href=\"{{ '$url' | relative_url }}\">", $html);
        $html = str_replace("</link>", "</a>", $html);
      }
    }
    $nImages = 1;
    preg_match_all("<media .*snapid=\"(\d*)\">", $html, $mediaMatches);
    foreach ($mediaMatches[1] as $snapId) {
      $query = <<<END
        select content
        from Version
        where resourceId = $snapId
END;
      $mediaResults = $snap2DbConn->query($query);
      while ($mediaResult = $mediaResults->fetch_assoc()) {
        $json = json_decode($mediaResult["content"]);
        $path = $json->original->path;
        $extension = end(explode(".", $path));
        $imageName = $shortname . "-learner-" . ($nImages++) . "." . $extension;
        $images[$imageName] = "http://shodor.org/media/" . $path;
        $html = str_replace("<media snapid=\"$snapId\"", "<img src=\"{{ 'img/activities/learner/$imageName' | relative_url }}\"", $html);
        $html = str_replace("<media class=\"center\" snapid=\"$snapId\"", "<img class=\"center\" src=\"{{ 'img/activities/learner/$imageName' | relative_url }}\"", $html);
      }
    }
    echo "SHORTNAME::$shortname\n$html\n";
  }
}
foreach ($images as $name => $path) {
  echo "$name,$path\n";
}

?>

