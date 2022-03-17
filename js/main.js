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
  const mouseoverImgs = document.getElementsByClassName("mouseover-img");
  for (let i = 0; i < mouseoverImgs.length; i++) {
    mouseoverImgs[i].style.display = "none";
  }
  document.getElementById("mouseover-" + id).style.display = "block";
}

function jumpTo(evt) {
  const option = evt.target.options[evt.target.selectedIndex];
  evt.target.selectedIndex = 0;
  window.location.href = option.dataset.catalog;
}

function selectBrowseOption() {
  const fields = [
    "subject"
  , "topic"
  , "audience"
  , "type"
  ];
  const noResults = document.getElementById("no-results");
  noResults.style.display = "none";
  const selectedResources = Array.prototype.slice.call(
    document.getElementsByClassName("resource-section")
  );
  for (let i = 0; i < selectedResources.length; i++) {
    selectedResources[i].style.display = "block";
  }
  for (let i = 0; i < fields.length; i++) {
    const field = fields[i];
    const select = document.getElementById(field + "-select");
    for (let j = 0; j < selectedResources.length; j++) {
      const resource = selectedResources[j];
      const fieldValues = resource.dataset[field].split(",");
      let hide = false;
      for (let k = 0; k < select.selectedOptions.length; k++) {
        const selectedOption = select.selectedOptions[k].value;
        if (!fieldValues.includes(selectedOption)) {
          hide = true;
          break;
        }
      }
      if (hide) {
        resource.style.display = "none";
        selectedResources.splice(j, 1);
        j--;
      }
    }
  }
  if (selectedResources.length == 0) {
    noResults.style.display = "block";
  }
}

function selectCatalogCategory(categoryName) {
  const sections = document.getElementsByClassName("category-section");
  for (let i = 0; i < sections.length; i++) {
    sections[i].style.display
      = (categoryName == "all" || sections[i].id == "category-section-" + categoryName)
      ? "block"
      : "none";
  }
}

function selectLessonAlignment(label, evt) {
  const option = evt.target.options[evt.target.selectedIndex];
  const sections = document.getElementsByClassName(label + "-section");
  for (let i = 0; i < sections.length; i++) {
    const isSelected = (
      sections[i].id == label + "-section-" + option.dataset[label]
    );
    sections[i].style.display = isSelected ? "block" : "none";
  }
}

function selectTab(sectionId) {
  const imgDir = "{{ site.tab-img-dir | relative_url }}";
  const sections = document.getElementsByClassName("tab-section");
  for (let i = 0; i < sections.length; i++) {
    const isSelected = (sections[i].id == sectionId);
    sections[i].style.display = isSelected ? "block" : "none";
    const tabName = sections[i].id.replace("-section", "");
    document.getElementById(tabName + "-img").src
      = imgDir + "/" + tabName + (isSelected ? "-selected" : "" ) + ".gif";
  }
}
