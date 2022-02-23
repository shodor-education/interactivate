function selectCategoryItem(categoryLabel, item) {
  var sections = document.getElementsByClassName(categoryLabel + "-section");
  for (var i = 0; i < sections.length; i++) {
    sections[i].style.display
      = (item == "all" || sections[i].id == categoryLabel + "-section-" + item)
      ? "block"
      : "none";
  }
}
