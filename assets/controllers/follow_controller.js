import {Controller} from '@hotwired/stimulus';
import axios from "axios";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ["relationContainer",'friends','followers','followings']
    static values = {'id': Number}

    connect() {
        this.followers();
    }

    follow() {
        axios.post('/follow/user', {'id': this.idValue}).then(response => {
            response = response.data
            console.log(response);
        })
            .catch(error => {
                // Traiter l'erreur ici
                console.error(error);
            });
    }

    unfollow() {
        axios.post('/unfollow/user', {'id': this.idValue}).then(response => {
            response = response.data
            console.log(response);
        })
            .catch(error => {
                // Traiter l'erreur ici
                console.error(error);
            });
    }

    render(response) {
        response = response.data
        let content = ''

        this.relationContainerTarget.innerHTML = ''
        response.forEach(user => {
            let image = user.image
            let id = user.id
            let username = user.username
            content += `      
              <div class="little-profile">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>`
            this.relationContainerTarget.innerHTML = content
        })
    }

    followers() {
        this.friendsTarget.classList.remove('focus')
        this.followingsTarget.classList.remove('focus')
        this.followersTarget.classList.add('focus')

        axios.post('/followers/get', {'id': this.idValue}).then(response => {
            this.render(response)
        }).catch(error => {
            // Traiter l'erreur ici
            console.error(error);
        });
    }

    followings() {
        this.friendsTarget.classList.remove('focus')
        this.followingsTarget.classList.add('focus')
        this.followersTarget.classList.remove('focus')
        axios.post('/followings/get', {'id': this.idValue}).then(response => {
            this.render(response)
        }).catch(error => {
            // Traiter l'erreur ici
            console.error(error);
        });
    }

    friends() {
        this.friendsTarget.classList.add('focus')
        this.followingsTarget.classList.remove('focus')
        this.followersTarget.classList.remove('focus')

        axios.post('/friends/get', {'id': this.idValue}).then(response => {
            this.render(response)
        }).catch(error => {
            // Traiter l'erreur ici
            console.error(error);
        });
    }
}
