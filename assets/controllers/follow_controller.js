import {Controller} from '@hotwired/stimulus';
import axios from "axios";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ["relationContainer", 'profil', 'message', 'friends', 'followers', 'followings', 'requestContainer']
    static values = {'id': Number, 'page': String}

    connect() {
        if (this.pageValue === 'show') {
            this.followers();
        }
        if (this.pageValue === 'friendList') {
            this.friends();
            this.getRequest()
        }
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
           if(response.message === 'ok'){
               this.getRequest()
           }
        })
            .catch(error => {
                // Traiter l'erreur ici
                console.error(error);
            });
    }
    deleteRequest(event){
let elt = event.currentTarget
        let id = elt.dataset.followRequestIdValue
        console.log(id)
        axios.post('/request/delete',{'id':id}).then(response => {
            response = response.data
            console.log(response);
        })
            .catch(error => {
                // Traiter l'erreur ici
                console.error(error);
            });
    }
    getRequest() {
        axios.post('/request/get').then(response => {
            response = response.data

            let content = ''

            response.forEach(request => {
                let image = request.requesterImageUrl
                let idRequest = request.requestId
                let id = request.requesterId
                let username = request.requesterUsername
                let typeAction = request.action
                let actions = {
                    'COMMENT': 'a liké votre commentaire',
                    'BLOOP': 'a liké votre bloop',
                    'PODCAST': 'a liké votre podcast.',
                    'FOLLOW': 'vous suit maintenant !',
                }
                content += `      
                        <div class=" friendRequest">
                                <div class="message__icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                                    <img class="imgPdp" src="./${image}" alt="${username}">
                                    </a>
                                </div>
                                <div class="message__name">
                                    <span><b>${username}</b>  ${actions[typeAction]}</span>
                                </div>
                              <button data-action="click->follow#deleteRequest" data-follow-request-id-value="${idRequest}" id="croixCloseRequest"><i class="bi bi-x-lg"></i></button>  
                              
                            </div>
 
 `


            })
            this.requestContainerTarget.innerHTML = content
        })
            .catch(error => {
                // Traiter l'erreur ici
                console.error(error);
            });
    }

    render(response) {
        response = response.data

        let content = ''
        this.messageTarget.textContent = ""
        response.forEach(user => {

            let image = user.image
            let id = user.id
            let username = user.username

            content += `      
              <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>
  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>  <div class="little-profile" data-follow-target="profil">
                                <div class="icon">
                                     <a href="/user/${id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="../${image}" alt="${username}">
                                    </a>
                                </div>
                                <p>${username}</p>
                            </div>


`

        })
        this.relationContainerTarget.innerHTML += content
    }

    clear() {
        this.profilTargets.forEach(profil => {
            profil.remove()
        })
    }

    followers() {
        if (this.pageValue === 'show') {
            this.friendsTarget.classList.remove('focus')
            this.followingsTarget.classList.remove('focus')
            this.followersTarget.classList.add('focus')
        }

        axios.post('/followers/get', {'id': this.idValue}).then(response => {
            this.clear()
            if (response.data.length === 0) {
                this.messageTarget.textContent = "Vous n'avez aucun followers"
            } else {
                this.render(response)
            }
        }).catch(error => {
            // Traiter l'erreur ici
            console.error(error);
        });
    }

    followings() {
        if (this.pageValue === 'show') {

            this.friendsTarget.classList.remove('focus')
            this.followingsTarget.classList.add('focus')
            this.followersTarget.classList.remove('focus')
        }
        axios.post('/followings/get', {'id': this.idValue}).then(response => {
            this.clear()
            if (response.data.length === 0) {

                this.messageTarget.textContent = "Vous ne suivez personne"
            } else {

                this.render(response)
            }
        }).catch(error => {
            // Traiter l'erreur ici
            console.error(error);
        });
    }

    friends() {
        if (this.pageValue === 'show') {
            this.friendsTarget.classList.add('focus')
            this.followingsTarget.classList.remove('focus')
            this.followersTarget.classList.remove('focus')
        }
        axios.post('/friends/get', {'id': this.idValue}).then(response => {
            this.clear()
            if (response.data.length === 0) {


                this.messageTarget.textContent = "Vous n'avez aucun ami"
            } else {
                this.render(response)
            }


        }).catch(error => {
            // Traiter l'erreur ici
            console.error(error);
        });
    }
}
