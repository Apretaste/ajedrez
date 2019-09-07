function showToast(text) {
  M.toast({
    html: text
  });
}

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
          fe.css('background', 'green');

          fritz.start = null;
          fritz.end = null;

          if (fritz.step > solutionData.length) {
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