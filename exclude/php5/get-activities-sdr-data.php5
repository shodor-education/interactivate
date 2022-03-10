<?php
header("Content-Type: text/plain");

include_once("helpers.php5");

$GWT_DIRS = array(
  "3DTransmographer" => "_3dtransmographer",
  "ABetterFire" => "abetterfire",
  "AdjustableSpinner" => "adjustablespinner",
  "AdvancedFire" => "advancedfire",
  "AdvancedMontyHall" => "advancedmontyhall",
  "AlgebraFour" => "fractionfour",
  "AlgebraQuiz" => "fractionfour",
  "Angles" => "angles",
  "AnotherHilbertCurve" => "anotherhilbertcurve",
  "AreaExplorer" => "areaexplorer",
  "ArithmeticFour" => "fractionfour",
  "ArithmeticQuiz" => "fractionfour",
  "BarGraph" => "bargraph",
  "BarGraphSorter" => "bargraphsorter",
  "BasicSpinner" => "adjustablespinner",
  "BoundFractionFinder" => "fractionpointer",
  "BoundFractionPointer" => "fractionpointer",
  "BoxPlot" => "boxplot",
  "Buffon" => "buffon",
  "CaesarCipher" => "caesarcipher",
  "CaesarCipherThree" => "caesarcipherthree",
  "CaesarCipherTwo" => "caesarciphertwo",
  "CantorComb" => "cantorcomb",
  "Chairs" => "chairs",
  "CircleGraph" => "circlegraph",
  "ClockArithmetic" => "clockarithmetic",
  "ClockWise" => "clockwise",
  "Coin" => "coin",
  "ColoringMultiples" => "coloringmultiples",
  "ColoringRemainder" => "coloringremainder",
  "ComparisonEstimator" => "comparisonestimator",
  "ConicFlyer" => "conicflyer",
  "Converter" => "converter",
  "CrazyChoicesGame" => "crazychoicesgame",
  "CrossSectionFlyer" => "crosssectionflyer",
  "DataFlyer" => "dataflyer",
  "Derivate" => "derivate",
  "DiceTable" => "dicetable",
  "DirectableFire" => "directablefire",
  "ElapsedTime" => "elapsedtime",
  "ElapsedTimeTwo" => "elapsedtimetwo",
  "EquationSolver" => "equationsolver",
  "EquivFractionFinder" => "equivfractionpointer",
  "EquivFractionPointer" => "equivfractionpointer",
  "Estimator" => "estimator",
  "EstimatorFour" => "fractionfour",
  "EstimatorQuiz" => "fractionfour",
  "ExpProbability" => "expprobability",
  "Factorize" => "factorize",
  "FactorizeTwo" => "factorizetwo",
  "Fire" => "fire",
  "FireAssessment" => "fireassessment",
  "FittedNormalDistr" => "fittednormaldistr",
  "FlakeMaker" => "flakemaker",
  "FloorTiles" => "floortiles",
  "FractalDimensions" => "fractaldimensions",
  "FractionFinder" => "fractionpointer",
  "FractionFour" => "fractionfour",
  "FractionPointer" => "fractionpointer",
  "FractionQuiz" => "fractionfour",
  "FractionSorter" => "fractionsorter",
  "FracturedPictures" => "fracturedpictures",
  "FunctionFlyer" => "dataflyer",
  "FunctionMachine" => "functionmachine",
  "FunctionRevolution" => "functionrevolution",
  "GeneralCoordinates" => "generalcoordinates",
  "GeneralizedMontyHall" => "generalizedmontyhall",
  "Graphit" => "graphit",
  "GraphSketcher" => "graphit",
  "HilbertCurve" => "hilbertcurve",
  "Histogram" => "histogram",
  "ImageTool" => "imagetool",
  "Incline" => "incline",
  "inequality" => "graphit",
  "Integrate" => "integrate",
  "JuliaSets" => "juliasets",
  "KochSnowflake" => "kochsnowflake",
  "Life" => "life",
  "LifeLite" => "lifelite",
  "LinearFunctMachine" => "linearfunctmachine",
  "Marbles" => "marbles",
  "MazeGame" => "simplemazegame",
  "Measures" => "measures",
  "Mixtures" => "mixtures",
  "MoreOrLessEstimator" => "moreorlessestimator",
  "MultiBarGraph" => "multibargraph",
  "MultiFunctionDataFly" => "multifunctiondatafly",
  "MultipleLinearRegression" => "multiplelinearregression",
  "NormalDistribution" => "normaldistribution",
  "NumberBaseClocks" => "numberbaseclocks",
  "NumberCruncher" => "numbercruncher",
  "OperationsQuiz" => "fractionfour",
  "OrderedSimplePlot" => "graphit",
  "OrderOfOperationsFou" => "fractionfour",
  "OverlappingGaussians" => "overlappinggaussians",
  "ParametricGraphIt" => "parametricgraphit",
  "PatternGenerator" => "patterngenerator",
  "PerimeterExplorer" => "areaexplorer",
  "PieChart" => "piechart",
  "PlopIt" => "plopit",
  "PolarCoordinates" => "polarcoordinates",
  "PositiveLinearFunct" => "linearfunctmachine",
  "PossibleOrNot" => "possibleornot",
  "PythagoreanExplorer" => "pythagoreanexplorer",
  "RabbitsAndWolves" => "rabbitsandwolves",
  "RacingGameWithOneDie" => "racinggamewithonedie",
  "RacingGameWithTwoDie" => "racinggamewithtwodie",
  "Recursion" => "recursion",
  "Regression" => "regression",
  "ScatterPlot" => "graphit",
  "Sequencer" => "sequencer",
  "ShapeBuilder" => "shapebuilder",
  "ShapeExplorer" => "areaexplorer",
  "ShapeSorter" => "shapesorter",
  "SierpinskiCarpet" => "sierpinskicarpet",
  "SierpinskiTriangle" => "sierpinskitriangle",
  "SimpleCoordinates" => "generalcoordinates",
  "SimpleMazeGame" => "simplemazegame",
  "SimpleMontyHall" => "simplemontyhall",
  "SimplePlot" => "graphit",
  "SingleFractionFinder" => "equivfractionpointer",
  "SingleFractionPoint" => "equivfractionpointer",
  "SkewDistribution" => "skewdistribution",
  "SlopeSlider" => "dataflyer",
  "SpreadofDisease" => "spreadofdisease",
  "SquaringTheTriangle" => "squaringthetriangle",
  "StemAndLeafPlotter" => "stemandleafplotter",
  "Stopwatch" => "stopwatch",
  "SurfaceAreaAndVolume" => "surfaceareaandvolume",
  "Tessellate" => "tessellate",
  "TheChaosGame" => "thechaosgame",
  "TheMandelbrotSet" => "themandelbrotset",
  "Tortoise" => "tortoise",
  "Transmographer" => "transmographer",
  "TransmographerTwo" => "transmographertwo",
  "TriangleExplorer" => "triangleexplorer",
  "TripleVennDiagram" => "triplevenndiagram",
  "TwoColors" => "twocolors",
  "TwoVariableFunction" => "twovariablefunction",
  "VennDiagrams" => "venndiagrams",
  "VerticalLineTest" => "verticallinetest",
  "WholeNumberCruncher" => "numbercruncher"
);

function getRelatedResources($dbConn, $propertyName, $resourceId, $type, $shortnameFilter) {
  $query = <<<END
select $shortnameFilter as str
from TSDRelation
left join SDRVersion on SDRVersion.`cserdId` = if(
  TSDRelation.`sourceId` = $resourceId,
  TSDRelation.`destId`,
  TSDRelation.`sourceId`
)
left join SDRVersionFieldValue on SDRVersionFieldValue.`versionId` = SDRVersion.`id`
left join SDRField on SDRField.`id` = SDRVersionFieldValue.`fieldId`
left join SDRTextValue on SDRTextValue.`valueId` = SDRVersionFieldValue.`valueId`
left join SDRVersionFieldValue as UrlVFV on UrlVFV.`versionId` = SDRVersion.`id`
left join SDRField as UrlF on UrlF.`id` = UrlVFV.`fieldId`
left join SDRTextValue as UrlTV on UrlTV.`valueId` = UrlVFV.`valueId`
left join SDRVersionFieldValue as TitleVFV on TitleVFV.`versionId` = SDRVersion.`id`
left join SDRField as TitleF on TitleF.`id` = TitleVFV.`fieldId`
left join SDRTextValue as TitleTV on TitleTV.`valueId` = TitleVFV.`valueId`
where (TSDRelation.`sourceId` = $resourceId or TSDRelation.`destId` = $resourceId)
and TSDRelation.`version` = "LIVE"
and SDRVersion.`state` = "live"
and SDRField.`name` = "Interactivate_Type"
and SDRTextValue.`entry` = "$type"
and UrlF.`name` = "Url"
and TitleF.`name` = "Title"
order by TitleTV.`entry`
END;
  $results = $dbConn->query($query);
  if ($results->num_rows > 0) {
    echo "related-$propertyName:\n";
    while ($result = $results->fetch_assoc()) {
      echo "  - \"$result[str]\"\n";
    }
  }
}

$activities = getSdrResources("Activity");
while ($activity = $activities->fetch_assoc()) {
  $shortname = getShortname($activity);
  echo "FILENAME::$shortname\n---\n";

  // ALIGNED STANDARDS OBJECTIVES
  echoAlignedStandardsObjectives($activity["resourceId"]);

  // ALIGNED TEXTBOOK SECTIONS
  echoAlignedTextbookSections($activity["resourceId"]);

  // AUDIENCES
  echo "audiences:\n";
  $results = getTextValues(
    $sdrDbConn,
    $activity["versionId"],
    "Interactivate_Audience"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  // DESCRIPTION
  echo "description: \"";
  $results = getTextValues(
    $sdrDbConn,
    $activity["versionId"],
    "Description"
  );
  $result = $results->fetch_assoc();
  echo "$result[str]\"\n";

  // GWT DIR
  echo "gwt-dir: \"" . $GWT_DIRS[$shortname] . "\"\n";

  // RELATED ACTIVITIES
  $normalShortnameFilter = "substring_index(substring_index(UrlTV.`entry`, '/', -2), '/', 1)";
  getRelatedResources(
    $sdrDbConn,
    "activities",
    $activity["resourceId"],
    "Activity",
    $normalShortnameFilter
  );

  // RELATED DISCUSSIONS
  getRelatedResources(
    $sdrDbConn,
    "discussions",
    $activity["resourceId"],
    "Discussion",
    $normalShortnameFilter
  );

  // RELATED LESSONS
  getRelatedResources(
    $sdrDbConn,
    "lessons",
    $activity["resourceId"],
    "Lesson",
    $normalShortnameFilter
  );

  // RELATED WORKSHEETS
  getRelatedResources(
    $sdrDbConn,
    "worksheets",
    $activity["resourceId"],
    "Worksheet",
    "substring_index(UrlTV.`entry`, '/', -1)"
  );

  // SUBJECTS
  echo "subjects:\n";
  $results = getTextValues(
    $sdrDbConn,
    $activity["versionId"],
    "Primary_Subject"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  // TITLE
  echo "title: \"";
  $results = getTextValues(
    $sdrDbConn,
    $activity["versionId"],
    "Title"
  );
  $result = $results->fetch_assoc();
  echo "$result[str]\"\n";

  // TOPICS
  echo "topics:\n";
  $results = getTextValues(
    $sdrDbConn,
    $activity["versionId"],
    "Related_Subject"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  // TYPE
  $results = getTextValues(
    $sdrDbConn,
    $activity["versionId"],
    "ActivityType"
  );
  if ($results->num_rows > 0) {
    echo "type: \"";
    $result = $results->fetch_assoc();
    echo strtolower($result["str"]) . "\"\n";
  }

  echo "---\n";
}
echo "\n";

?>

