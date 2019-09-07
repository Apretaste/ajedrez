
function showToast(text) {
  M.toast({
    html: text
  });
}

var fritz = {
  step: 0,
  start: null,
  end: null,
  checkStep: function(){
    return solutionData[this.step].start === this.start
        && solutionData[this.step].end === this.end;
  }
};

$(function(){
  $(".fritz-cell").click(function(){
    var id = $(this).attr('id');
    if (fritz.start === null){
      fritz.start = id;
      $(this).addClass('blink_me');
    } else {
      fritz.end = id;
      if (fritz.checkStep())
      {
        showToast('Bien!!!');

        var fs = $("#" +  fritz.start);
        var fe = $("#" +  fritz.end);

        fritz.step++;

        fs.removeClass('blink_me');
        fe.html(fs.html());
        fe.css('background', 'green');

        if (fritz.step > solutionData.length) {
          showToast('Felicitaciones!')
        }
      } else {
        $("#" +  fritz.start).removeClass('blink_me');
        fritz.start = null;
      }
    }
  });
});