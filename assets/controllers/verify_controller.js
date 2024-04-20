import {Controller} from '@hotwired/stimulus';
import axios from "axios";
//verifying if email is   TAKEN
//USE IN templates/admin/reservations/profilClient.html.twig
//USE IN templates/admin/client/edit.html.twig
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['error', 'email', 'validation', 'phone', 'errorPhone']

    toValid() {
        axios.post('/user/email/taken', {"email": this.emailTarget.value}).then(response => {
            response = response.data
            if (response.message == 'taken') {
                this.errorTarget.innerHTML = "Cette adresse n'est pas disponible"
                this.emailTarget.value = ''
            }
            if (response.message == 'free') {
                this.errorTarget.innerHTML = ""

            }

        }).catch(() => {
            console.log('error')
        })

    }


}
