import {Controller} from '@hotwired/stimulus';
/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static targets = ['description', 'blur', 'descriptionBloop', 'blurBloop']

    connect() {

            this.descriptionTargets.forEach((descriptionElement, index) => {
                 if (descriptionElement.textContent.length > 340) {

                    const blurElement = this.blurTargets[index];
                    if (blurElement) {
                        blurElement.style.display = 'block';
                    } else {
                        console.log('No next element sibling found for', descriptionElement);
                    }
                }
            })

            this.descriptionBloopTargets.forEach((descriptionElement, index) => {
                console.log(descriptionElement,descriptionElement.length)
                if (descriptionElement.textContent.length > 800) {

                    const blurElement = this.blurBloopTargets[index];
                    if (blurElement) {
                        blurElement.style.display = 'block';
                    } else {
                        console.log('No next element sibling found for', descriptionElement);
                    }
                }
            })

    }
}
