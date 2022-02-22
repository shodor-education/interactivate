function selectSubject(name) {
  var sections = document.getElementsByClassName("subject-section");
  for (var i = 0; i < sections.length; i++) {
    sections[i].style.display
      = (name == "all" || sections[i].id == "subject-section-" + name)
      ? "block"
      : "none";
  }
}
