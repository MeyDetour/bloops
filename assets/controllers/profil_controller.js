import {Controller} from '@hotwired/stimulus';
import axios from "axios";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
let emailBV = null
let usernameBV = null
let descriptionBV = null
 export default class extends Controller {
    static targets = ['error', 'username', 'email',   'description','imgDefaultProfil','imagePreview','inputDropzone']
static values={'page':String}
    connect() {

             usernameBV = this.usernameTarget.value
        descriptionBV = this.descriptionTarget.value
            emailBV = this.emailTarget.value


    }
preview(event){
   let file = event.target.files[0];
    if (file) {
        const reader = new FileReader();

        reader.onload = (e) => {

          this.imagePreviewTarget.src = e.target.result;
          this.imgDefaultProfilTarget.style.display = 'none';
            this.imagePreviewTarget.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}
    validate() {
        axios.post('/user/username/taken', {'username': this.usernameTarget.value}).then(response => {
            response = response.data
            if (response.message !== 'free' && this.usernameTarget.value !== usernameBV ) {
                this.errorTarget.innerHTML = 'Username indisponible : ' + this.usernameTarget.value
                this.usernameTarget.value = usernameBV
            } else {
                axios.post('/user/email/taken', {'email': this.emailTarget.value}).then(response2 => {
                    response2 = response2.data
                    if (response2.message !== 'free' && this.emailTarget.value !== emailBV ) {
                        this.errorTarget.innerHTML = 'Email indisponible : ' + this.emailTarget.value
                        this.emailTarget.value = emailBV

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
        this.errorTarget.innerHTML = ''
        this.emailTarget.value = emailBV
        this.usernameTarget.value = usernameBV

    }



}
