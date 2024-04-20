import { Controller } from '@hotwired/stimulus';
import axios from "axios";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
   static targets = []
    connect() {
         axios.post('/friend/list').then(response=>{

         }).catch(()=>{
             console.log('error')
         })
    }
}
