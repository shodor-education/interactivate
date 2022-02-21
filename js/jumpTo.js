function jumpTo(evt) {
  var option = evt.target.options[evt.target.selectedIndex];
  evt.target.selectedIndex = 0;
  window.location.href = option.dataset.subdirectory;
}
