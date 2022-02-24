function selectCatalogCategory(categoryName) {
  var sections = document.getElementsByClassName("category-section");
  for (var i = 0; i < sections.length; i++) {
    sections[i].style.display
      = (item == "all" || sections[i].id == "category-section-" + categoryName)
      ? "block"
      : "none";
  }
}
