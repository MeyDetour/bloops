import { Controller } from '@hotwired/stimulus';
import axios from 'axios';
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static targets = ['audio','pause','progress','progressAudio','icSound']
connect() {

    this.progressAudioTarget.style.display = 'none'
}

    playPauseBtn (){
            if (this.audioTarget.paused) {
                this.audioTarget.play();

            } else {
                this.audioTarget.pause();
            }
        };

        audio () {
            const progress = (   this.audioTarget.currentTime /    this.audioTarget.duration) * 100;
            this.progressTarget.value = progress;
        }

        progressBar () {
            const time = (  this.progressTarget.value *    this.audioTarget.duration) / 100;
            this.audioTarget.currentTime = time;
        }
    progressAudio() {
        const volume = this.progressAudioTarget.value;

        this.audioTarget.volume = volume;
    }
clickIcAudio(){
            if(   this.progressAudioTarget.style.display === 'flex'){
                this.progressAudioTarget.style.display = 'none'
            }else{

                this.progressAudioTarget.style.display = 'flex'
            }
}
}