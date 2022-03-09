function copytext(ayat,terjemah,aya_number) {
    const el = document.createElement('textarea');
    el.value = 'Q.S '+sura_id+':'+aya_number;
    el.value += '\n'+ayat;
    el.value += '\n\nTerjemah :\n'+terjemah;
    // el.setAttribute('readonly', '');
    el.style.position = 'absolute';
    el.style.left = '-9999px';
    document.body.appendChild(el);
    el.select();
    if (document.execCommand('copy')) {
      $('#alert-'+aya_number).html('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Has been copied!</strong></div>');
    }
    document.body.removeChild(el);
}

function playsound(aya) {
  var playbutton = document.getElementsByClassName('play-button');

  var sounds = document.getElementsByTagName('audio');


  var ayamp3 = aya;
  if (parseInt(ayamp3) < 10) {
    ayamp3 = '00'+ayamp3;
  }else if (parseInt(ayamp3) >= 10 && parseInt(ayamp3) < 100) {
    ayamp3 = '0'+ayamp3;
  }

  var suramp3 = sura_id;
  if (parseInt(suramp3) < 10) {
    suramp3 = '00'+suramp3;
  }else if (parseInt(suramp3) >= 10 && parseInt(suramp3) < 100) {
    suramp3 = '0'+suramp3;
  }

  var mp3 = mp3_url+suramp3+ayamp3+'.mp3';
  // var mp3 ='http://www.everyayah.com/data/Abdul_Basit_Mujawwad_128kbps/'+suramp3+ayamp3+'.mp3';
  //console.log(aya);
  if (idplay == aya && playstat == 1) {
    for(i=0; i<sounds.length; i++) {
      playbutton[i].innerHTML = '<i class="fa fa-play"> Play</i>';
      sounds[i].pause();
    }
    playstat = 0;
  }else {

    idplay = aya;
    playstat = 1;
    for(i=0; i<sounds.length; i++) {
      playbutton[i].innerHTML = '<i class="fa fa-play"> Play</i>';
      sounds[i].pause();
      if (aya_start < 2 && i == (aya-1)) {
        sounds[i].src= mp3;

        sounds[i].play();
        playbutton[i].innerHTML = '<i class="fa fa-pause"> Pause</i>';
        audioElement = sounds[i];
        $('html, body').animate({
            scrollTop: $("#section-"+aya).offset().top
        }, 1000);
      }else if (aya_start > 1 && i == (aya-aya_start)) {
        sounds[i].src= mp3;
        sounds[i].play();
        playbutton[i].innerHTML = '<i class="fa fa-pause"> Pause</i>';
        audioElement = sounds[i];
        $('html, body').animate({
            scrollTop: $("#section-"+aya).offset().top
        }, 1000);
      }

    }
  }

  audioElement.addEventListener('ended', function() {
      $('#play-id-'+aya).html('<i class="fa fa-play"> Play</i>');
        if (aya < count_aya) {

          playsound(aya+1);
        }
    }, false);

}