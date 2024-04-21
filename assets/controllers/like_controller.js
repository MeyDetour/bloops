import {Controller} from '@hotwired/stimulus';
import axios from 'axios';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {bloopId: Number, commentId: Number , podcastId : Number}

    static targets = ['likesCount']
    toggleLikePodcast() {
        axios.post(`like/podcast/${this.podcastIdValue}`).then(response => {
            const {isLiked, count} = response.data
            this.toggle(this.element,{isLiked, count})
        }).catch(error => console.error('Toggle like failed:', error))
    }
    toggleLike() {
        axios.post(`like/bloop/${this.bloopIdValue}`).then(response => {
            const {isLiked, count} = response.data
            this.toggle(this.element,{isLiked, count})
        }).catch(error => console.error('Toggle like failed:', error))
    }
    toggle(element,{isLiked, count}){
        this.likesCountTarget.textContent = count
        console.log({isLiked, count})
        if (isLiked) {
            this.element.classList.add('bloopBallonLiked')
            this.element.classList.remove('bloopBallon')
        } else {
            this.element.classList.remove('bloopBallonLiked')
            this.element.classList.add('bloopBallon')
        }
    }
    toggleLikeComment() {
        axios.post(`like/comment/${this.commentIdValue}`).then(response => {
            const {isLiked, count} = response.data
             this.toggle(this.element,{isLiked, count})
        }).catch(error => console.error('Toggle like failed:', error))
    }
}
