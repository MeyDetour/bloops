import { Controller } from '@hotwired/stimulus';
console.log('%c Bonjour!! ', 'font-weight: bold; font-size: 50px;color: #d6bcf8; text-shadow: 3px 3px 0  #7B3DC7FF  ');
console.log("%c🌟 Développeuse passionnée, amatrice de café et codeuse nocturne. 💻\n🚀 Sur GitHub :  https://github.com/MeyDetour\n🌐 Connectons-nous sur LinkedIn : https://www.linkedin.com/in/mey-detour/\n🎨 Créative, curieuse et toujours prête pour de nouveaux défis !\n🌈 Let's code the world brighter together! 🌟✨", "color: #d6bcf8; font-family: serif; font-size: 17px");

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
    connect() {
        this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js';
    }
}
