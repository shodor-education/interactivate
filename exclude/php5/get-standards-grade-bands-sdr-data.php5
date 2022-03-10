<?php
header("Content-Type: text/plain");

include_once("helpers.php5");

$query = <<<END
select
  TSDGrade.`id` as grade_id,
  TSDOrganization.`name` as org_name,
  TSDGrade.`name` as grade_name
from TSDGrade
left join TSDOrganization on TSDOrganization.`id` = TSDGrade.`organizationId`
order by TSDGrade.`id`
END;

$standards_grade_bands = $sdrDbConn->query($query);
while ($standards_grade_band = $standards_grade_bands->fetch_assoc()) {
  echo "FILENAME::$standards_grade_band[grade_id]\n---\n";

  // STANDARDS CATEGORIES
  echo "standards-categories:\n";
  $query = <<<END
select trim(trailing from name) as name
from TSDCategory
where TSDCategory.`gradeId` = $standards_grade_band[grade_id]
order by name
END;
  $categories = $sdrDbConn->query($query);
  while ($category = $categories->fetch_assoc()) {
    echo "  - \"$category[name]\"\n";
  }

  // TITLE
  echo "title: \"$standards_grade_band[org_name]: $standards_grade_band[grade_name]\"\n";

  echo "---\n";
}
echo "\n";

?>

