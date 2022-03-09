<?php
header("Content-Type: text/plain");

include_once("passwords.php5");
include_once("connect-to-databases.php5");

function getPath($snapId) {
  global
    $snap2DbConn;
  $query = <<<END
select parentId, shortName
from DirectoryLink
where childId = $snapId
END;
  $results = $snap2DbConn->query($query);
  while ($result = $results->fetch_assoc()) {
    if ($result["parentId"] != 1) {
      return getPath($result["parentId"]) . "/" . $result["shortName"];
    }
    else {
      return $result["shortName"];
    }
  }
}

function echoContentAndGetImages($versions, $shortname, $images) {
  global
      $IMAGE_DIR
    , $IMAGE_INFIX
    , $sdrDbConn
    , $snap2DbConn
  ;
  while ($version = $versions->fetch_assoc()) {
    $html = $version["content"];
    $html = preg_replace("#\s+\n#", "\n", $html);
    $html = preg_replace("#\s*<XMLResource>\s*\n#", "", $html);
    $html = preg_replace("#\s*<section mapping=\"content\">\s*\n#", "", $html);
    $html = preg_replace("#<link base=\"PATH:dictionary\" href=\"/(.)#", "<a href=\"{{ '/dictionary/$1' | relative_url }}", $html);
    $html = preg_replace("#<link href=\"http://www.shodor.org/interactivate/dictionary/(.)/#", "<a href=\"{{ '/dictionary/$1' | relative_url }}", $html);
    $html = preg_replace("#<link href=\"/interactivate/dictionary/(.)/?#", "<a href=\"{{ '/dictionary/$1' | relative_url }}", $html);
    $html = str_replace("<b>", "<strong>", $html);
    $html = str_replace("<i>", "<em>", $html);
    $html = str_replace("<u>", "<strong>", $html);
    $html = str_replace("class=\"center\"", "class=\"h-center\"", $html);
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
select Version.`content`, Resource.`contentType`
from Version
left join Resource on Resource.`id` = Version.`resourceId`
where Version.`resourceId` = $snapId
and Version.`status` = 3
END;
      $mediaResults = $snap2DbConn->query($query);
      while ($mediaResult = $mediaResults->fetch_assoc()) {
        if ($mediaResult["contentType"] == 2) {
          $json = json_decode($mediaResult["content"]);
          $path = $json->original->path;
          $extension = end(explode(".", $path));
          $imageName = "$shortname-$IMAGE_INFIX-" . ($nImages++) . "." . $extension;
          $images[$imageName] = "http://shodor.org/media/" . $path;

          $html = str_replace(
            "<media snapid=\"$snapId\"",
            "<img src=\"{{ 'img/$IMAGE_DIR/$imageName' | relative_url }}\"",
            $html
          );
          $html = str_replace(
            "<media class=\"h-center\" snapid=\"$snapId\"",
            "<img class=\"h-center\" src=\"{{ 'img/$IMAGE_DIR/$imageName' | relative_url }}\"",
            $html
          );
        }
        else if ($mediaResult["contentType"] == 9) {

          $query = <<<END
select ResourceLink.`parentId`
from Resource
left join ResourceLink on ResourceLink.`childId` = Resource.`id`
where Resource.`id` = $snapId
END;
          $json = json_decode($mediaResult["content"]);
          $filename = $json->file->name;
          $results = $snap2DbConn->query($query);
          while ($result = $results->fetch_assoc()) {
            $path = getPath($result["parentId"]) . "/" . $filename;
            $html = str_replace(
              "<media snapid=\"$snapId\" />",
              "<a href=\"http://shodor.org/media/content/$path\">Download File</a>",
              $html
            );
          }
        }
      }
    }
    echo "$html\n";
  }
  return $images;
}

?>

