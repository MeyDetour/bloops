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
    static targets = ['error', 'username', 'email','counter',   'description','imgDefaultProfil','imagePreview','inputDropzone']
static values={'page':String , 'hasImage':Boolean}
    connect() {

             usernameBV = this.usernameTarget.value
        descriptionBV = this.descriptionTarget.value
            emailBV = this.emailTarget.value
        this.updateCounter();

    }
preview(event){
   let file = event.target.files[0];
    if (file) {
        const reader = new FileReader();

        reader.onload = (e) => {

          this.imagePreviewTarget.src = e.target.result;
            console.log(this.hasImageValue)
          if(this.hasImageValue){
              this.element.querySelector('.newProfilImage').style.display = 'none'
          }else{
              this.imgDefaultProfilTarget.style.display = 'none';
          }
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
                this.usernameTarget.value = ''
            } else {
                axios.post('/user/email/taken', {'email': this.emailTarget.value}).then(response2 => {
                    response2 = response2.data
                    if (response2.message !== 'free' && this.emailTarget.value !== emailBV ) {
                        this.errorTarget.innerHTML = 'Email indisponible : ' + this.emailTarget.value
                        this.emailTarget.value =''

                    }
                    else{

                    }

                }).catch((error => {
                    this.errorTarget.innerHTML = 'Erreur : ' + error
                }))


            }

        }).catch((error => {
            this.errorTarget.innerHTML = 'Erreur : ' + error
        }))
    }
     updateCounter() {
         const count = this.descriptionTarget.value.length;
         const maxChars = 400;
         this.counterTarget.textContent = `${count}/${maxChars}`;
     }

    reset(e) {
        e.preventDefault()
        this.errorTarget.innerHTML = ''
        this.emailTarget.value = emailBV
        this.descriptionTarget.value = descriptionBV
        this.usernameTarget.value = usernameBV
        this.updateCounter()

    }



}
