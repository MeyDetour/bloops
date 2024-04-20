import {Controller} from '@hotwired/stimulus';
import axios from "axios";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static targets = ['error', 'inputEmail', 'eye0ps', 'eye1ps', 'eye0psc', 'eye1psc', 'fieldPs', 'fieldPsc']
    //eye 0(open) 1(close) ps=password psc=passwordconfirmation
    static values = {'lieuId': Number, 'page': String}

    connect() {
        // Après la redirection, force le rechargement de la page.


    }

    //============================================REGISTER AND LOGIN EYE
    eyeClose() {

        this.eye0psTarget.style.display = "none";
        this.eye1psTarget.style.display = "block";
        this.fieldPsTarget.type = "password";
    }

    eyeOpen() {
        this.eye1psTarget.style.display = "none";
        this.eye0psTarget.style.display = "block";
        this.fieldPsTarget.type = "text";
    }

    eyecClose() {
        this.eye0pscTarget.style.display = "none";
        this.eye1pscTarget.style.display = "block";
        this.fieldPscTarget.type = "password";

    }

    eyecOpen() {
        this.eye1pscTarget.style.display = "none";
        this.eye0pscTarget.style.display = "block";
        this.fieldPscTarget.type = "text";

    }

    //=========================================== RESGISTER


    //============================================FIRST CONNEXION
    verifyPassword(event){
        if(this.fieldPsTarget.value.length <=3){
            event.preventDefault()
            this.errorTarget.innerHTML = 'Le mot de passe doit faire plus de 3 caracteres.'
        }
        if(this.usernameTarget.value.length <=3){
            event.preventDefault()
            this.errorTarget.innerHTML = "Le nom d'utilisateur doit faire plus de 3 caracteres."
        }
    }


}
