function showToast(text) {
  M.toast({
    html: text
  });
}

var colors = ['#f1f8e9', '#dcedc8', '#c5e1a5', '#aed581', '#9ccc65', '#8bc34a'];
var scolors = ['light-green lighten-5', 'light-green lighten-4', 'light-green lighten-3',
              'light-green lighten-2', 'light-green lighten-1', 'light-green'];

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
        if ( $(this).html().replace('&nbsp;','') !== '') {
          fritz.start = id;
          //$(this).addClass('blink_me');

          $(this).addClass('btn-floating');
          $(this).addClass('pulse');
        }
      }
      else {
        fritz.end = id;
        var fs = $("#" + fritz.start);
        if (fritz.checkStep()) {
          var fe = $("#" + fritz.end);

          fritz.step++;

          //fs.removeClass('blink_me');
          fs.removeClass('btn-floating');
          fs.removeClass('pulse');

          fe.html(fs.html());
          fs.html('');
          fs.css('background', colors[fritz.step]);
          fe.css('background', colors[fritz.step]);

          var sol = $("#solution");

          if (fritz.step === 1) {
            sol.html("");
          }

          sol.html(sol.html() + " <span class=\"left badge " + scolors[fritz.step] + "\">" + fritz.start + "-" + fritz.end + "</span>");

          fritz.start = null;
          fritz.end = null;

          if (fritz.step >= solutionData.length) {
            $('#modal1').modal('open');
            if (levelNumber < 3) {
              $('#btnLevelUp').click(function(){
                 //if (levelNumber < 3) {
                   var levels = ['','FACIL','MEDIO','DIFICIL'];
                   apretaste.send({command: 'AJEDREZ', data: {query: levels[levelNumber+1]}});
                 //}
              });
            } else {
              $('#btnLevelUp').hide();
            }

						apretaste.send({
							command: "AJEDREZ SOLVE",
							data: {
							},
							redirect: false
						});
          }
          else {
            showToast('Bien!!!');

          }
        }
        else {
          //$("#" + fritz.start).removeClass('blink_me');
          fs.removeClass('btn-floating');
          fs.removeClass('pulse');
          showToast('Jugada incorrecta.');
          fritz.start = null;
        }
      }
    }
  });

  $('.fixed-action-btn').floatingActionButton({
    direction: 'top',
    hoverEnabled: false
  });

  $('.modal').modal();
});

function showNextStep() {
  if (fritz.step < solutionData.length) {
    showToast('Siguiente paso: ' + solutionData[fritz.step].start + '-' + solutionData[fritz.step].end);
  }
}
