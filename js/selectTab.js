---
---
function selectTab(sectionId) {
  var imgDir = "{{ 'img/activity/' | relative_url }}";
  var sections = document.getElementsByClassName("tab-section");
  for (var i = 0; i < sections.length; i++) {
    var isSelected = (sections[i].id == sectionId);
    sections[i].style.display = isSelected ? "block" : "none";
    var tabName = sections[i].id.replace("-section", "");
    document.getElementById(tabName + "-img").src
      = imgDir + tabName + (isSelected ? "-selected" : "" ) + ".gif";
  }
}