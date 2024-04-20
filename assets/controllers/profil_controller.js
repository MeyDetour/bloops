import {Controller} from '@hotwired/stimulus';
import axios from "axios";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
let emailBV = null
let usernameBV = null
 export default class extends Controller {
    static targets = ['error', 'username', 'email', 'save', 'update', 'cancel']

    connect() {
        this.closeButton()

        usernameBV = this.usernameTarget.value
        emailBV = this.emailTarget.value
        this.emailTarget.setAttribute('readonly', true)
        this.usernameTarget.setAttribute('readonly', true)
    }

    validate() {
        axios.post('/user/username/taken', {'username': this.usernameTarget.value}).then(response => {
            response = response.data
            console.log(this.usernameTarget.value,usernameBV)
            if (response.message !== 'free' && this.usernameTarget.value !== usernameBV ) {
                this.errorTarget.innerHTML = 'Username indisponible : ' + this.usernameTarget.value
                this.usernameTarget.value = ''
            } else {
                axios.post('/user/email/taken', {'email': this.emailTarget.value}).then(response2 => {
                    response2 = response2.data
                    if (response2.message !== 'free' && this.emailTarget.value !== emailBV ) {
                        this.errorTarget.innerHTML = 'Email indisponible : ' + this.emailTarget.value
                        this.emailTarget.value = ''

                    } else {
                        axios.post('/user/profil/update/username/email', {
                            'username': this.usernameTarget.value,
                            'email': this.emailTarget.value
                        }).then(response3 => {
                            response3 = response3.data
                            if (response3.message !== 'ok') {
                                this.errorTarget.innerHTML = 'erreur du server'

                                this.reset()
                            } else {
                                this.emailTarget.value = response3.email
                                this.usernameTarget.value = response3.username

                            }
                            this.closeButton()
                        }).catch((error => {
                            this.errorTarget.innerHTML = 'Erreur : ' + error
                        }))
                    }
                }).catch((error => {
                    this.errorTarget.innerHTML = 'Erreur : ' + error
                }))


            }

        }).catch((error => {
            this.errorTarget.innerHTML = 'Erreur : ' + error
        }))
    }

    reset() {
        this.errorTarget.innerHTML = 'Erreur du server'

        this.emailTarget.value = localStorage.getItem('email');
        this.usernameTarget.value = localStorage.getItem('username');

    }

    closeButton() {
        //to cancel
        this.saveTarget.style.display = 'none'
        this.cancelTarget.style.display = 'none'
        this.updateTarget.style.display = 'block'
        this.usernameTarget.readOnly = false
        this.emailTarget.readOnly = false
        this.errorTarget.innerHTML = ' '
    }

    openButton() {
        //to cancel
        this.saveTarget.style.display = 'block'
        this.cancelTarget.style.display = 'block'
        this.updateTarget.style.display = 'none'
        this.emailTarget.readOnly = false
        this.usernameTarget.readOnly = false

    }

    cancel() {
        this.reset()
        this.closeButton()
    }

    goToUpdate() {

        this.openButton()
        this.usernameTarget.focus()


    }
}
