var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function () {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;

    if (panel.classList === "panel-active") {
      panel.classList.remove("panel-active");
    } else {
      panel.classList.add("panel-active");
    }
  });
}
