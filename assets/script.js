var projectTemplate = $("#project-template");
var target          = $("projects");

$.each(projects, function (index, value) {
  console.log(value);
  var project = projectTemplate;
  project.find("a").attr("href", "http://" + value + ".test/");
  project.find("a strong").html(value);

  $("#projects").append(project.html());
});

$("#projects .project").each(function (index) {
  var target = $(this);
  var link   = $(this).find("a").attr("href");

  link = link.replace(/\.[a-z]+\/$/, "");
  link = link.replace(/^http(s)?:\/\//, "");

  $.each(vhosts, function (ignoreIndex, domain) {
    var regex = link + "\.[a-z]+$";
    regex     = new RegExp(regex);

    if ((regex.exec(domain)) !== null) {
      console.log(domain + 'found in', regex);
      target.addClass("active");
    }
  });
});