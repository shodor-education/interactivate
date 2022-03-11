<?php
header("Content-Type: text/plain");

include_once("helpers.php5");

$sdrDbConn = new mysqli($DB_SERVER, $SDR_DB_USER, $SDR_DB_PASS, $SDR_DB_NAME);
if ($sdrDbConn->connect_error) {
  die("Database connection failed: " . $sdrDbConn->connect_error);
}

$query = <<<END
select
  TSDObjective.`id` as id,
  trim(trailing from
    replace(
      replace(
        replace(
          replace(
            TSDObjective.`description`,
            "\"",
            "\\\\\""
          ),
          "\t",
          " "
        ),
        "\r\n",
        " "
      ),
      "  ",
      " "
    )
  ) as description,
  trim(trailing from TSDCategory.`name`) as categoryName,
  TSDGrade.`id` as gradeId
from TSDObjective
left join TSDCategory on TSDCategory.`id` = TSDObjective.`categoryId`
left join TSDGrade on TSDGrade.`id` = TSDCategory.`gradeId`
order by TSDObjective.`id`
END;

$objectives = $sdrDbConn->query($query);
while ($objective = $objectives->fetch_assoc()) {
  echo "FILENAME::$objective[id]\n---\n";

  // CATEGORY
  echo "category: \"$objective[categoryName]\"\n";

  // DESCRIPTION
  echo "description: \"$objective[description]\"\n";

  // GRADE BAND
  echo "grade-band: \"$objective[gradeId]\"\n";

  echo "---\n";
}
echo "\n";

?>

