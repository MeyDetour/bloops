import {Controller} from '@hotwired/stimulus';
import axios from "axios";

//SCHANGE PASSWORD FOR CLIENT
//USE INtemplates/client/profil/aide.html.twig
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
let isVerified = false
export default class extends Controller {
    static targets = ['confirm', 'last', 'psw', 'error','valider']

    connect() {
        this.validerTarget.style.display='none'
    }

    lastFocus() {
        console.log('focus')
        if (!isVerified) {
            this.confirmTarget.classList.remove('focusInput')
            this.pswTarget.classList.remove('focusInput')
            this.pswTarget.setAttribute('readonly', true)
            this.confirmTarget.setAttribute('readonly', true)

            this.lastTarget.classList.add('focusInput')
            this.lastTarget.focus()
        }
    }

    lastFct() {

        axios.post('/user/last/password', {'password': this.lastTarget.value}).then(response => {
            response = response.data
            console.log(response)
            if (response.message === 'ok') {
                this.lastTarget.classList.remove('focusInput')
                this.confirmTarget.classList.remove('focusInput')
                this.lastTarget.setAttribute('readonly', true)
                this.pswTarget.readOnly = false
                this.pswTarget.classList.add('focusInput')
                this.pswTarget.focus()
                this.errorTarget.textContent = " ";
                this.validerTarget.style.display = 'flex'
                isVerified = true
            } else {
                this.errorTarget.textContent = response.message
                this.lastTarget.focus()
            }
        }).catch(() => {
            this.errorTarget.innerHTML = 'Erreur lors de la vérification du mot de passe'
        })
    }

    passwordFct() {
        console.log('psw')
        if (this.pswTarget.value.length < 5) {
            this.errorTarget.textContent = "Le mot de passe doit faire plus de 5 caractères";
        } else {
            this.lastTarget.classList.remove('focusInput')
            this.confirmTarget.classList.remove('focusInput')
            this.errorTarget.textContent = " ";
            this.confirmTarget.readOnly = false
            this.confirmTarget.focus()

        }
    }
    confirmFocus(){
        this.lastTarget.classList.remove('focusInput')
        this.pswTarget.classList.remove('focusInput')

        this.confirmTarget().classList.add('focusInput')
    }
    verifyMatch() {
        if (this.pswTarget.value !== this.confirmTarget.value) {
            this.errorTarget.textContent = "Les mots de passe ne correspondent pas";

        }
    }
    confirmFct() {
        if (this.pswTarget.value === this.confirmTarget.value) {
            axios.post('/user/new/password', {"password": this.pswTarget.value}).then(response => {
                response = response.data
                if (response.message === 'ok') {
                    this.lastTarget.classList.remove('focusInput')
                    this.pswTarget.classList.remove('focusInput')
                    this.confirmTarget.classList.remove('focusInput')
                    this.errorTarget.textContent = " ";
                    this.lastTarget.value = ''
                    this.pswTarget.value = ''
                    this.confirmTarget.value = ''
                    this.element.classList.add('successfuly')
                    isVerified = true
                } else {
                    this.errorTarget.textContent = response.message;
                }
            })
        } else {
            this.errorTarget.textContent = "Les mots de passe ne correspondent pas";
            this.confirmTarget.readOnly = false
        }
    }

    closePanel(event) {
        this.element.classList.remove('successfuly')
        event.currentTarget.style.display = 'none'
    }


}
