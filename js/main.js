---
---
onload = function () {
  if (document.getElementById("featured-img-link")) {
    const featuredActivities = [
      "JuliaSets"
    , "ShapeSorter"
    , "FunctionRevolution"
    , "Incline"
    , "PolarCoordinates"
    , "FireAssessment"
    , "ParametricGraphIt"
    , "SpreadofDisease"
    , "CrossSectionFlyer"
    , "AdvancedFire"
    , "3DTransmographer"
    , "VennDiagrams"
    ];
    const featuredActivity = featuredActivities[new Date().getMonth()];
    document.getElementById("featured-img-link").href = "activities/"
      + featuredActivity;
    document.getElementById("featured-img").src = "img/activities/featured/"
      + featuredActivity + ".jpg";
  }
}

function changeMouseoverImg(id) {
  var mouseoverImgs = document.getElementsByClassName("mouseover-img");
  for (var i = 0; i < mouseoverImgs.length; i++) {
    mouseoverImgs[i].style.display = "none";
  }
  document.getElementById("mouseover-" + id).style.display = "block";
}

function jumpTo(evt) {
  var option = evt.target.options[evt.target.selectedIndex];
  evt.target.selectedIndex = 0;
  window.location.href = option.dataset.catalog;
}

function selectCatalogCategory(categoryName) {
  var sections = document.getElementsByClassName("category-section");
  for (var i = 0; i < sections.length; i++) {
    sections[i].style.display
      = (categoryName == "all" || sections[i].id == "category-section-" + categoryName)
      ? "block"
      : "none";
  }
}

function selectLessonAlignment(label, evt) {
  var option = evt.target.options[evt.target.selectedIndex];
  var sections = document.getElementsByClassName(label + "-section");
  for (var i = 0; i < sections.length; i++) {
    var isSelected = (
      sections[i].id == label + "-section-" + option.dataset[label]
    );
    sections[i].style.display = isSelected ? "block" : "none";
  }
}

function selectTab(sectionId) {
  var imgDir = "{{ site.tab-img-dir | relative_url }}";
  var sections = document.getElementsByClassName("tab-section");
  for (var i = 0; i < sections.length; i++) {
    var isSelected = (sections[i].id == sectionId);
    sections[i].style.display = isSelected ? "block" : "none";
    var tabName = sections[i].id.replace("-section", "");
    document.getElementById(tabName + "-img").src
      = imgDir + "/" + tabName + (isSelected ? "-selected" : "" ) + ".gif";
  }
}
