function showToast(text) {
  M.toast({
    html: text
  });
}

var colors = ['#e0f2f1', '#b2dfdb', '#80cbc4', '#4db6ac', '#26a69a', '#009688'];
var scolors = ['teal lighten-5', 'teal lighten-4', 'teal lighten-3', 'teal lighten-2', 'teal lighten-1', 'teal'];

var fritz = {
  step: 0,
  start: null,
  end: null,
  checkStep: function () {
    return solutionData[this.step].start === this.start
        && solutionData[this.step].end === this.end;
  }
};

$(function () {
  $(".fritz-cell").click(function () {
    if (fritz.step < solutionData.length) {
      var id = $(this).attr('id');
      if (fritz.start === null) {
        fritz.start = id;
        $(this).addClass('blink_me');
      }
      else {
        fritz.end = id;
        if (fritz.checkStep()) {


          var fs = $("#" + fritz.start);
          var fe = $("#" + fritz.end);

          fritz.step++;

          fs.removeClass('blink_me');
          fe.html(fs.html());
          fs.html('');
          fe.css('background', colors[fritz.step]);

          var sol = $("#solution");

          if (fritz.step === 1) {
            sol.html("");
          }

          sol.html(sol.html() + " <span class=\"badge " + scolors[fritz.step] + "\">" + fritz.start + "-" + fritz.end + "</span>");

          fritz.start = null;
          fritz.end = null;

          if (fritz.step >= solutionData.length) {
            showToast('Fin de la partida. Felicitaciones!');
          }
          else {
            showToast('Bien!!!');
          }
        }
        else {
          $("#" + fritz.start).removeClass('blink_me');
          showToast('Mal !!');
          fritz.start = null;
        }
      }
    }
  });
});