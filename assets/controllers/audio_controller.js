import {Controller} from '@hotwired/stimulus';
import axios from 'axios';
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static targets = ['audio', 'pause', 'play', 'progress', 'progressAudio', 'icSound']

    connect() {

        this.progressAudioTarget.style.display = 'none'
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.play().then(() => {

                    }).catch(error => {
                        console.error("Autoplay with sound is not allowed without user interaction.");
                        entry.target.muted = true;
                    });
                } else {
                    entry.target.pause();
                }
            });
        }, {
            rootMargin: '0px',
            threshold: 0.5 // Trigger when 50% of the video is visible
        });

        this.audioTargets.forEach(audio => {
            this.observer.observe(audio);
        });
    }
    disconnect() {
        if (this.observer) {
            this.audioTargets.forEach(audio => {
                this.observer.unobserve(audio);
            });
        }
    }
    playPauseBtn() {
        if (this.audioTarget.paused) {
            this.audioTarget.play();
            this.playTarget.style.display = 'none'
            this.pauseTarget.style.display = 'block'

        } else {
            this.audioTarget.pause();
            this.pauseTarget.style.display = 'none'
            this.playTarget.style.display = 'block'
        }
    };

    audio() {
        const progress = (this.audioTarget.currentTime / this.audioTarget.duration) * 100;
        this.progressTarget.value = progress;
    }

    progressBar() {
        const time = (this.progressTarget.value * this.audioTarget.duration) / 100;
        this.audioTarget.currentTime = time;
    }

    progressAudio() {
        const volume = this.progressAudioTarget.value;

        this.audioTarget.volume = volume;
    }

    clickIcAudio() {
        if (this.progressAudioTarget.style.display === 'flex') {
            this.progressAudioTarget.style.display = 'none'
        } else {

            this.progressAudioTarget.style.display = 'flex'
        }
    }
}
