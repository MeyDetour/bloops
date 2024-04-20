import {Controller} from '@hotwired/stimulus';
import axios from "axios";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['error', 'inputEmail', 'firstPsw', 'secondPsw', 'eye0ps', 'eye1ps', 'eye0psc', 'eye1psc', 'fieldPs', 'fieldPsc']
    //eye 0(open) 1(close) ps=password psc=passwordconfirmation
    static values = {'lieuId': Number, 'page': String}



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
    emailChange() {
        axios.post('/isExistingAccount', {'email': this.inputEmailTarget.value}).then(response => {
            response = response.data
            if (response.message == 'existing') {
                this.errorTarget.innerHTML = "Cette adresse n'est pas disponible"
                this.inputEmailTarget.value = ''
            }
            if (response.message == 'free') {
                this.errorTarget.innerHTML = ""

            }

        }).catch(() => {
            this.errorTarget.innerHTML = "Erreur lors de la verification de l'email"})


    }

    //============================================FIRST CONNEXION
    verifyMatchPassword(event){
        if(this.firstPswTarget.value !== this.secondPswTarget.value){
            event.preventDefault()
            this.errorTarget.innerHTML = 'Les mots de passe ne sont pas identique.'
        }
    }


}
