import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {  static targets = ["videoFile"]

    connect() {
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

        this.videoFileTargets.forEach(video => {
            this.observer.observe(video);
        });
    }

    disconnect() {
        if (this.observer) {
            this.videoFileTargets.forEach(video => {
                this.observer.unobserve(video);
            });
        }
    }
}
