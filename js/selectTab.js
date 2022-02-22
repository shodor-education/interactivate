---
---
function selectTab(sectionId) {
  var imagesDir = "{{ 'images/activity/' | relative_url }}";
  var sections = document.getElementsByClassName("tab-section");
  for (var i = 0; i < sections.length; i++) {
    var isSelected = (sections[i].id == sectionId);
    sections[i].style.display = isSelected ? "block" : "none";
    var tabName = sections[i].id.replace("-section", "");
    document.getElementById(tabName + "-img").src
      = imagesDir + tabName + (isSelected ? "-selected" : "" ) + ".gif";
  }
}