---
---
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
