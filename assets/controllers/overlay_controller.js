import {Controller} from '@hotwired/stimulus';
import axios from "axios";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
let current_user = {}
let user_author_bloop = {}
let bloopId = null
let podcastId = null
let fileIndex = null
let fileElementsList = null
let lastEvent = null
let startX = null
export default class extends Controller {
    static values = {id: Number}

    static targets = ['overlay', 'commentInput', 'text', 'fileVisible', 'content', 'reason', 'overlaySection', 'error', 'errorContainer', 'rightArrow', 'leftArrow']

    connect() {
        //to render full text

    }

    closeOverlay() {
        this.element.style.overflowY = 'scroll'
        this.overlayTarget.style.opacity = '0'
        this.overlayTarget.style.display = 'none'
        this.removeSections()

        this.contentTarget.innerHTML = ''

        this.hideErrorContainer()
        bloopId = null
        podcastId = null
    }

    removeSections() {
        this.overlaySectionTargets.forEach(section => {

            section.remove();
        });
    }

    openOverlay() {
        this.element.style.overflowY = 'hidden'
        this.overlayTarget.style.display = 'flex'
        this.overlayTarget.style.top = window.scrollY + "px";
        this.overlayTarget.style.opacity = '1'
        this.removeSections()
        this.contentTarget.innerHTML = ''

    }

    newError(content) {
        this.errorTarget.textContent = content;
        this.errorContainerTarget.classList.add('show-error');
    }

    hideErrorContainer() {
        this.errorContainerTarget.classList.remove('show-error');
    }

    reportBloop(event) {
        let element = event.currentTarget;
        console.log(element)
        lastEvent = event

        console.log('lastevent : ', event)
        bloopId = element.dataset.overlayIdValue;
        let bloopAuthorId = element.dataset.overlayBloopAuthorIdValue;
        console.log(bloopId, bloopAuthorId)
        this.openOverlay()
        axios.post('user/get/current').then(response => {
            current_user = response.data

            axios.post('user/get/information', {'id': bloopAuthorId})
                .then(response2 => {
                    user_author_bloop = response2.data

                    this.contentTarget.innerHTML = `
                       <div class="object">
                             <h1>Vous allez signaler ${user_author_bloop.username}</h1>
                        </div>  
                        <fieldset>
                            <legend>Motif :</legend>
                          <textarea name="reason" data-overlay-target="reason" id="signalementReason" cols="30" rows="10"></textarea>
                   
                        </fieldset>
                        <button data-action="click->overlay#successReport"   class="btnGreen">Signaler</button>
                    
                    `
                })
                .catch(error => {
                    // Handle any error that occurred during the request
                    console.error('Error making post request:', error);
                });
        }).catch(error => console.error('Toggle like failed:', error))
    }

    successReport() {

        if (this.reasonTarget.value.trim() === '') {
            this.newError('Vous devez écrire un motif pour signaler un utilisateur')
        } else {
            axios.post(`report`, {
                'id2': user_author_bloop.id, 'idBloop': bloopId, 'reason': this.reasonTarget.value
            }).then(response3 => {
                response3 = response3.data
                if (response3.message === 'ok') {
                    this.hideErrorContainer()
                    this.contentTarget.innerHTML = `
                 <p>${current_user.username} (${current_user.email}), vous, signalez le compte ${user_author_bloop.username} pour le motif : ${this.reasonTarget.value}</h1>   <span style="color :red">Tout signalement abusif sera sanctionné.</span></p> `
                } else if (response3.message === ' Accounts not found') {

                    this.contentTarget.innerHTML = `
                 <p> <span style="color :red">Comptes non trouvés</span></p>

                `
                } else if (response3.message === 'Bloop does not exist') {
                    this.contentTarget.innerHTML = `
                 <p> <span style="color :red">Publication non trouvée</span></p>

                `
                } else if (response3.message === 'owner error') {
                    this.contentTarget.innerHTML = `
                 <p> <span style="color :red">La publication signalée n'appartient pas au compte signalé</span></p> `
                } else {
                    this.contentTarget.innerHTML = `
                        <p><span style="color :red">Erreur indefinie</span></p>  `
                }

            }).catch(error => console.error('Erreur:', error))

        }
    }

    openOverlayComment(event) {
        bloopId = event.currentTarget.dataset.overlayIdValue
        this.showComment()
    }
    openOverlayCommentPodcast(event) {
        podcastId = event.currentTarget.dataset.overlayIdValue
        this.showCommentPodcast()
    }
    showComment() {
        this.openOverlay()
        axios.post(`/bloop/${bloopId}`).then(response => {
            response = response.data
            let defaultImageProfil = 'images/imgProfil.png';
            //auteur
            let auteur_id = response.author.id
            let authorUsername = response.author.username
            let authorImage = response.author.authorImage

            //current user
            let user_id = response.current_user.id
            let user_username = response.current_user.username
            let user_image = response.current_user.authorImage

            //bloop
            let bloopId = response.id
            let createdAt = response.createdAt
            let chapo = response.chapo
            let nbLikes = response.nbLikes
            let displayComment = response.displayComment
            let comments = response.comments
            let commentCount = comments.length;
            let isLikedByUser = response.isLikedByUser
            let likeButtonClass = response.isLikedByUser ? 'bloopBallonLiked' : 'bloopBallon';


            //video && images
            let images = response.images
            let videos = response.videos
            fileIndex = 0
            let hasVideo = videos.length > 0;
            let isFirstImageVisible = !hasVideo;
            let createMediaElements = (mediaArray, mediaType) => {
                return mediaArray.map((media, index) => {
                    let element
                    if (mediaType === 'video') {
                        element = `<div class="  "  data-action="touchstart->overlay#onTouchStart touchend->overlay#onTouchEnd"  ><video controls class=" videoOverlayBloop flowCorner bloopVideo autoplay-video " loop autoplay><source src='${media}'></video></div>`
                    } else {
                        element = `<div class=" " data-action="touchstart->overlay#onTouchStart touchend->overlay#onTouchEnd "><img src="${media}" class="imageOverlayBloop   flowCorner bloopImage " alt="Image"></div>`
                    }
                    return element
                });
            }

            function escapeHTML(html) {
                var text = document.createTextNode(html);
                var div = document.createElement('div');
                div.appendChild(text);
                return div.innerHTML;
            }

            let videoElements = createMediaElements(videos, 'video');
            let imageElements = createMediaElements(images, 'image');
            fileElementsList = [...videoElements, ...imageElements];
            let commentsContainer = ``

            if (displayComment) {
                //comment input
                commentsContainer += `
            <div class="inputComment overlaySection flex-row" data-overlay-target="overlaySection">
           
                    <input type="text" name="content" id="newCommentContent" data-overlay-target="commentInput" placeholder="Ecrivez un commentaire">
                    <i class="bi bi-chevron-double-right" data-action="click->overlay#sendComment"></i>
            </div>
       
            `

                //comment widget
                comments.forEach(comment => {
                    let isLikedByUser = comment.isLikedByUser
                    let likeButtonClass = comment.isLikedByUser ? 'bloopBallonLiked' : 'bloopBallon';

                    let imageElements = createMediaElements(images, 'image');

                    commentsContainer += `
                         <div class=" flex-row overlaySection" data-overlay-target="overlaySection">
                              <div class="flex-col innerParent">
                                  <div class="flex-row message__header">
                                        <div class="message__icon">
                                            <a href="/user/${comment.id}" class="link navbarLienProfil">
                                                <img class="imgPdp" src="${comment.author.authorImage}" alt="${comment.author.username}">
                                            </a>
                                        </div>
                                        <div class="message__name">
                                            <h3>${comment.author.username}<h3>
                                            <p class="sub-title">Publié le ${comment.humanReadableDate}</p>
                                        </div>
                                   </div>
                                     <div class="text-container text-container-cs" >
                                        <span class="text-content text-content-br" data-overlay-target="text" >${escapeHTML(comment.content)}</span>
                                        <span class="blur-overlay"></span>
                                    </div>
                                
                      
                                      
                              </div>
                                 <div class="flex-col message__interactions ">
                                 <div class="flex-col-between">
                                         <div class="message__options">
                                          
                                          ${user_id === comment.author.id ? `  
                                              <div class="dropdown">
                                                <button class="btn  " type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                            
                                                </button>
                                                <ul class="dropdown-menu">
                                                            <li><button class="dropdown-item deleteCommentBtn"  data-action="click->overlay#deleteComment" data-overlay-comment-id-value="${comment.id}" >Supprimer</button></li>
                                                          
                                                            
                                                </ul> 
                                              </div>
                                                        ` : ''}
                                         </div>
                                    </div>
                                          <div data-controller="like" data-action="click->like#toggleLikeComment" data-like-comment-id-value="${comment.id}"   class="flex-col grid-center    ${likeButtonClass} ">  
                         
                                            <svg  class="bloopBallonLikedSvg"   width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                  xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                      d="M12.7351 16.3799C29.1181 5.07288 16.9201 -3.58062 12.0001 1.47888C7.07858 -3.58062 -5.11942 5.07288 11.2651 16.3799L10.9141 17.0819C10.892 17.126 10.8789 17.174 10.8754 17.2232C10.8719 17.2725 10.8781 17.3219 10.8937 17.3687C10.9252 17.4632 10.993 17.5413 11.0821 17.5859C11.1712 17.6304 11.2744 17.6378 11.3689 17.6063C11.4634 17.5748 11.5415 17.507 11.5861 17.4179L11.6461 17.2979C11.6596 17.5529 11.6821 17.7704 11.7226 17.9729C11.8246 18.4889 12.0346 18.9059 12.3946 19.6259L12.4141 19.6679C12.7321 20.3009 12.6871 20.9429 12.4891 21.5369C12.2866 22.1399 11.9401 22.6634 11.6881 23.0414C11.6328 23.1241 11.6126 23.2255 11.632 23.3231C11.6514 23.4207 11.7088 23.5066 11.7916 23.5619C11.8743 23.6172 11.9757 23.6373 12.0733 23.6179C12.1709 23.5985 12.2568 23.5411 12.3121 23.4584L12.3181 23.4479C12.5671 23.0759 12.9646 22.4789 13.2001 21.7754C13.4401 21.0569 13.5181 20.1989 13.0861 19.3319C12.6991 18.5594 12.5386 18.2339 12.4576 17.8259C12.4286 17.6829 12.41 17.538 12.4021 17.3924L12.4141 17.4179C12.4586 17.507 12.5368 17.5748 12.6313 17.6063C12.7258 17.6378 12.829 17.6304 12.9181 17.5859C13.0072 17.5413 13.075 17.4632 13.1065 17.3687C13.138 17.2742 13.1306 17.171 13.0861 17.0819L12.7351 16.3799ZM10.0891 1.90338C8.33858 0.988378 5.88908 1.69038 4.90808 3.60588C4.55258 4.30038 4.36808 5.22588 4.60508 6.38088C4.68758 6.78588 5.30558 6.67638 5.39558 6.27438C5.82308 4.39038 7.16108 2.58138 9.87908 2.48238C10.2301 2.47038 10.4011 2.06538 10.0891 1.90338Z"
                                                      fill="#DA9EFF"/>
                                            </svg> 
                                             <svg class="bloopBallonSvg"  width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_42_331)">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M12 3.63001L10.9245 2.52451C9.22954 0.783006 6.06004 1.35901 4.90954 3.60451C4.38454 4.63201 4.23304 6.16501 5.26354 8.1975C6.24004 10.1295 8.28004 12.48 12 15.063C15.7185 12.4785 17.7585 10.128 18.7365 8.1975C19.767 6.16501 19.617 4.63201 19.0905 3.60601C17.94 1.35901 14.7705 0.783006 13.0755 2.52451L12 3.63001ZM11.265 16.38C-4.90496 5.22001 6.76504 -3.35249 11.8035 1.28551C11.8715 1.34751 11.9375 1.41201 12.0015 1.47901C12.0665 1.41201 12.1325 1.34751 12.1995 1.28551C17.2395 -3.35249 28.905 5.22151 12.735 16.38L13.0875 17.082C13.1096 17.1261 13.1228 17.1742 13.1263 17.2234C13.1297 17.2726 13.1235 17.322 13.1079 17.3688C13.0923 17.4156 13.0677 17.4589 13.0353 17.4961C13.003 17.5334 12.9637 17.5639 12.9195 17.586C12.8754 17.6081 12.8274 17.6212 12.7782 17.6247C12.729 17.6282 12.6795 17.622 12.6327 17.6064C12.5859 17.5908 12.5427 17.5661 12.5054 17.5338C12.4681 17.5015 12.4376 17.4621 12.4155 17.418L12.4035 17.3925C12.4155 17.5575 12.4335 17.6955 12.459 17.8275C12.54 18.2325 12.7005 18.5595 13.0875 19.332C13.5195 20.199 13.44 21.057 13.2015 21.7755C12.966 22.479 12.5685 23.076 12.3195 23.448L12.3135 23.4585C12.2862 23.4995 12.251 23.5347 12.21 23.562C12.169 23.5894 12.1231 23.6084 12.0747 23.6181C12.0264 23.6277 11.9766 23.6277 11.9283 23.618C11.88 23.6084 11.834 23.5894 11.793 23.562C11.7521 23.5346 11.7169 23.4994 11.6895 23.4585C11.6621 23.4175 11.6431 23.3715 11.6335 23.3232C11.6239 23.2749 11.6239 23.2251 11.6335 23.1768C11.6431 23.1284 11.6622 23.0825 11.6895 23.0415C11.9415 22.6635 12.2895 22.1415 12.489 21.537C12.6885 20.943 12.7335 20.301 12.4155 19.668L12.396 19.626C12.036 18.906 11.826 18.489 11.724 17.973C11.6811 17.7503 11.655 17.5246 11.646 17.298L11.586 17.418C11.5642 17.4624 11.5338 17.5021 11.4966 17.5347C11.4593 17.5673 11.416 17.5922 11.3691 17.608C11.3222 17.6238 11.2727 17.6301 11.2233 17.6267C11.1739 17.6232 11.1257 17.61 11.0815 17.5879C11.0372 17.5657 10.9978 17.535 10.9655 17.4975C10.9332 17.46 10.9086 17.4165 10.8932 17.3695C10.8777 17.3225 10.8718 17.2729 10.8756 17.2235C10.8795 17.1742 10.893 17.1261 10.9155 17.082L11.268 16.38H11.265ZM9.01954 3.09001C8.04604 2.82001 6.79504 3.21451 6.24454 4.28701C6.04804 4.67401 5.87704 5.32051 6.12454 6.28951C6.21904 6.65551 6.74554 6.58651 6.85504 6.22501C7.17004 5.17951 7.79554 4.05451 8.89354 3.68701C9.21904 3.57751 9.34954 3.18151 9.01954 3.09001Z"
                                                          fill="black"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_42_331">
                                                        <rect width="24" height="24" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                            <span data-like-target="likesCount">${comment.nbLikes}</span>
                                     </div>
                                 </div>
                             </div>
                        </div>
`
                })
            }
            //generation all content
            this.contentTarget.innerHTML = `
                    <div class="flex-row  ">
                        <div class="flex-col innerParent">
                            <div class="flex-row message__header">
                                <div class="message__icon">
                                    <a href="/user/${auteur_id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="${authorImage}" alt="${authorUsername}">
                                    </a>
                                </div>
                                <div class="message__name">
                                    <h3>${authorUsername}</h3>
                                    <p class="sub-title">Publié le ${createdAt}</p>
                                </div>
                            </div>
                            <div class="message__content">
                                <div class="${hasVideo || images.length ? 'text-container text-container-cs ' : 'text-container '}" }>
                                    <span class="text-content text-content-br" ${hasVideo || images.length ? 'data-overlay-target="text" ' : ''}>${escapeHTML(chapo)}</span>
                                    ${hasVideo || images.length ? '<span class="blur-overlay"></span>' : ''}
                                </div>
                                <div class="file-container">
                                ${fileElementsList.length > 1 ? `  
                             <i data-overlay-target="leftArrow" data-action="click->overlay#rightClick"   class=" arrowLeft bi bi-arrow-left-circle-fill"></i>
                                <i data-overlay-target="rightArrow" data-action="click->overlay#leftClick"  class=" arrowRight bi bi-arrow-right-circle-fill"></i>
                             ` : ''}
                                   <div class="fileVisible " data-overlay-target="fileVisible">
                               ${fileElementsList.length > 0 ? fileElementsList[fileIndex] : ''}     
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="flex-col-between">
                             <div class="message__options">
                                <div class="dropdown">
                                    <button class="btn  " type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                
                                    </button>
                                    <ul class="dropdown-menu">
                
                                       ${user_id === auteur_id ? `
                                                <li><a class="dropdown-item" href="/bloop/edit/${bloopId}">Edit</a></li>
                                                <li><a class="dropdown-item" href="#">Archivé</a></li>
                                            ` : ''}
                                             
                
                                                <li><button class="dropdown-item"
                                                      onclick="reportBloop( ${auteur_id} , ${bloopId})" >Signaler</button></li>
                
                
                                    </ul>
                                </div>
                            </div>
                            <div class="flex-col message__interactions ">
                                    
                            <div data-controller="like" data-action="click->like#toggleLike" data-like-bloop-id-value="${bloopId}"   class="flex-col grid-center    ${likeButtonClass} ">  
                                                <svg  class="bloopBallonLikedSvg"   width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                      xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M12.7351 16.3799C29.1181 5.07288 16.9201 -3.58062 12.0001 1.47888C7.07858 -3.58062 -5.11942 5.07288 11.2651 16.3799L10.9141 17.0819C10.892 17.126 10.8789 17.174 10.8754 17.2232C10.8719 17.2725 10.8781 17.3219 10.8937 17.3687C10.9252 17.4632 10.993 17.5413 11.0821 17.5859C11.1712 17.6304 11.2744 17.6378 11.3689 17.6063C11.4634 17.5748 11.5415 17.507 11.5861 17.4179L11.6461 17.2979C11.6596 17.5529 11.6821 17.7704 11.7226 17.9729C11.8246 18.4889 12.0346 18.9059 12.3946 19.6259L12.4141 19.6679C12.7321 20.3009 12.6871 20.9429 12.4891 21.5369C12.2866 22.1399 11.9401 22.6634 11.6881 23.0414C11.6328 23.1241 11.6126 23.2255 11.632 23.3231C11.6514 23.4207 11.7088 23.5066 11.7916 23.5619C11.8743 23.6172 11.9757 23.6373 12.0733 23.6179C12.1709 23.5985 12.2568 23.5411 12.3121 23.4584L12.3181 23.4479C12.5671 23.0759 12.9646 22.4789 13.2001 21.7754C13.4401 21.0569 13.5181 20.1989 13.0861 19.3319C12.6991 18.5594 12.5386 18.2339 12.4576 17.8259C12.4286 17.6829 12.41 17.538 12.4021 17.3924L12.4141 17.4179C12.4586 17.507 12.5368 17.5748 12.6313 17.6063C12.7258 17.6378 12.829 17.6304 12.9181 17.5859C13.0072 17.5413 13.075 17.4632 13.1065 17.3687C13.138 17.2742 13.1306 17.171 13.0861 17.0819L12.7351 16.3799ZM10.0891 1.90338C8.33858 0.988378 5.88908 1.69038 4.90808 3.60588C4.55258 4.30038 4.36808 5.22588 4.60508 6.38088C4.68758 6.78588 5.30558 6.67638 5.39558 6.27438C5.82308 4.39038 7.16108 2.58138 9.87908 2.48238C10.2301 2.47038 10.4011 2.06538 10.0891 1.90338Z"
                                                          fill="#DA9EFF"/>
                                                </svg>
                
                
                                                <svg class="bloopBallonSvg"  width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_42_331)">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M12 3.63001L10.9245 2.52451C9.22954 0.783006 6.06004 1.35901 4.90954 3.60451C4.38454 4.63201 4.23304 6.16501 5.26354 8.1975C6.24004 10.1295 8.28004 12.48 12 15.063C15.7185 12.4785 17.7585 10.128 18.7365 8.1975C19.767 6.16501 19.617 4.63201 19.0905 3.60601C17.94 1.35901 14.7705 0.783006 13.0755 2.52451L12 3.63001ZM11.265 16.38C-4.90496 5.22001 6.76504 -3.35249 11.8035 1.28551C11.8715 1.34751 11.9375 1.41201 12.0015 1.47901C12.0665 1.41201 12.1325 1.34751 12.1995 1.28551C17.2395 -3.35249 28.905 5.22151 12.735 16.38L13.0875 17.082C13.1096 17.1261 13.1228 17.1742 13.1263 17.2234C13.1297 17.2726 13.1235 17.322 13.1079 17.3688C13.0923 17.4156 13.0677 17.4589 13.0353 17.4961C13.003 17.5334 12.9637 17.5639 12.9195 17.586C12.8754 17.6081 12.8274 17.6212 12.7782 17.6247C12.729 17.6282 12.6795 17.622 12.6327 17.6064C12.5859 17.5908 12.5427 17.5661 12.5054 17.5338C12.4681 17.5015 12.4376 17.4621 12.4155 17.418L12.4035 17.3925C12.4155 17.5575 12.4335 17.6955 12.459 17.8275C12.54 18.2325 12.7005 18.5595 13.0875 19.332C13.5195 20.199 13.44 21.057 13.2015 21.7755C12.966 22.479 12.5685 23.076 12.3195 23.448L12.3135 23.4585C12.2862 23.4995 12.251 23.5347 12.21 23.562C12.169 23.5894 12.1231 23.6084 12.0747 23.6181C12.0264 23.6277 11.9766 23.6277 11.9283 23.618C11.88 23.6084 11.834 23.5894 11.793 23.562C11.7521 23.5346 11.7169 23.4994 11.6895 23.4585C11.6621 23.4175 11.6431 23.3715 11.6335 23.3232C11.6239 23.2749 11.6239 23.2251 11.6335 23.1768C11.6431 23.1284 11.6622 23.0825 11.6895 23.0415C11.9415 22.6635 12.2895 22.1415 12.489 21.537C12.6885 20.943 12.7335 20.301 12.4155 19.668L12.396 19.626C12.036 18.906 11.826 18.489 11.724 17.973C11.6811 17.7503 11.655 17.5246 11.646 17.298L11.586 17.418C11.5642 17.4624 11.5338 17.5021 11.4966 17.5347C11.4593 17.5673 11.416 17.5922 11.3691 17.608C11.3222 17.6238 11.2727 17.6301 11.2233 17.6267C11.1739 17.6232 11.1257 17.61 11.0815 17.5879C11.0372 17.5657 10.9978 17.535 10.9655 17.4975C10.9332 17.46 10.9086 17.4165 10.8932 17.3695C10.8777 17.3225 10.8718 17.2729 10.8756 17.2235C10.8795 17.1742 10.893 17.1261 10.9155 17.082L11.268 16.38H11.265ZM9.01954 3.09001C8.04604 2.82001 6.79504 3.21451 6.24454 4.28701C6.04804 4.67401 5.87704 5.32051 6.12454 6.28951C6.21904 6.65551 6.74554 6.58651 6.85504 6.22501C7.17004 5.17951 7.79554 4.05451 8.89354 3.68701C9.21904 3.57751 9.34954 3.18151 9.01954 3.09001Z"
                                                              fill="black"/>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_42_331">
                                                            <rect width="24" height="24" fill="white"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                    <span data-like-target="likesCount">${nbLikes}</span>
                                </div>
                                <div onclick="" class="flex-col grid-center">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_42_338)">
                                            <path d="M14 1C14.2652 1 14.5196 1.10536 14.7071 1.29289C14.8946 1.48043 15 1.73478 15 2V10C15 10.2652 14.8946 10.5196 14.7071 10.7071C14.5196 10.8946 14.2652 11 14 11H4.414C3.88361 11.0001 3.37499 11.2109 3 11.586L1 13.586V2C1 1.73478 1.10536 1.48043 1.29289 1.29289C1.48043 1.10536 1.73478 1 2 1H14ZM2 0C1.46957 0 0.960859 0.210714 0.585786 0.585786C0.210714 0.960859 0 1.46957 0 2L0 14.793C2.10149e-05 14.8919 0.0293926 14.9886 0.0843973 15.0709C0.139402 15.1531 0.217567 15.2172 0.308999 15.255C0.400432 15.2928 0.501021 15.3026 0.598036 15.2832C0.695051 15.2638 0.784131 15.216 0.854 15.146L3.707 12.293C3.89449 12.1055 4.14881 12.0001 4.414 12H14C14.5304 12 15.0391 11.7893 15.4142 11.4142C15.7893 11.0391 16 10.5304 16 10V2C16 1.46957 15.7893 0.960859 15.4142 0.585786C15.0391 0.210714 14.5304 0 14 0L2 0Z"
                                                  fill="black"/>
                                            <path d="M3 3.5C3 3.36739 3.05268 3.24021 3.14645 3.14645C3.24021 3.05268 3.36739 3 3.5 3H12.5C12.6326 3 12.7598 3.05268 12.8536 3.14645C12.9473 3.24021 13 3.36739 13 3.5C13 3.63261 12.9473 3.75979 12.8536 3.85355C12.7598 3.94732 12.6326 4 12.5 4H3.5C3.36739 4 3.24021 3.94732 3.14645 3.85355C3.05268 3.75979 3 3.63261 3 3.5ZM3 6C3 5.86739 3.05268 5.74021 3.14645 5.64645C3.24021 5.55268 3.36739 5.5 3.5 5.5H12.5C12.6326 5.5 12.7598 5.55268 12.8536 5.64645C12.9473 5.74021 13 5.86739 13 6C13 6.13261 12.9473 6.25979 12.8536 6.35355C12.7598 6.44732 12.6326 6.5 12.5 6.5H3.5C3.36739 6.5 3.24021 6.44732 3.14645 6.35355C3.05268 6.25979 3 6.13261 3 6ZM3 8.5C3 8.36739 3.05268 8.24021 3.14645 8.14645C3.24021 8.05268 3.36739 8 3.5 8H8.5C8.63261 8 8.75979 8.05268 8.85355 8.14645C8.94732 8.24021 9 8.36739 9 8.5C9 8.63261 8.94732 8.75979 8.85355 8.85355C8.75979 8.94732 8.63261 9 8.5 9H3.5C3.36739 9 3.24021 8.94732 3.14645 8.85355C3.05268 8.75979 3 8.63261 3 8.5Z"
                                                  fill="black"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_42_338">
                                                <rect width="16" height="16" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <span>${commentCount}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                  
`;

            if (displayComment) {
                this.overlayTarget.innerHTML += commentsContainer
            }
            //to change image

            //to delete com


            //to render full text
            this.textTargets.forEach(function (el) {
                if (el.textContent.length > 400) {
                    if (el.nextElementSibling) { // Ensure that there is an element sibling
                        el.nextElementSibling.style.display = 'block'; // This will show the next element sibling
                    } else {
                        console.log('No next element sibling found for', el);
                    }
                }
                el.addEventListener('click', () => {
                    if (el.parentElement.style.maxHeight === 'fit-content') {
                        el.nextElementSibling.style.display = 'block';
                        el.parentElement.style.maxHeight = '100px'
                    } else {
                        el.nextElementSibling.style.display = 'none';
                        el.parentElement.style.maxHeight = 'fit-content'
                    }

                })

            });

        }).catch(error => console.error('Toggle like failed:', error))
    }

     onTouchStart (event)  {
        startX = event.touches ? event.touches[0].pageX : event.pageX;
    };

     onTouchEnd  (event) {
        const endX = event.changedTouches ? event.changedTouches[0].pageX : event.pageX;
        const diffX = endX - startX;

        if (diffX > 50) {
          this.rightClick()
        } else if (diffX < -50) {
          this.leftClick()
        }
    };
    rightClick() {
        if (fileIndex === 0) {
            fileIndex = fileElementsList.length - 1
        } else {
            fileIndex -= 1
        }
        this.fileVisibleTarget.innerHTML = fileElementsList[fileIndex]
    }

    leftClick() {
        if (fileIndex === fileElementsList.length - 1) {
            fileIndex = 0
        } else {
            fileIndex += 1
        }
        this.fileVisibleTarget.innerHTML = fileElementsList[fileIndex]

    }
    showCommentPodcast() {
        this.openOverlay()
        axios.post(`/podcast/${podcastId}`).then(response => {
            response = response.data
            let defaultImageProfil = 'images/imgProfil.png';
            //auteur
            let auteur_id = response.author.id
            let authorUsername = response.author.username
            let authorImage = response.author.authorImage

            //current user
            let user_id = response.current_user.id
            let user_username = response.current_user.username
            let user_image = response.current_user.authorImage

            //Podcast
            let podcastId = response.id
            let createdAt = response.createdAt
            let description = response.description
          let  audioName = response.audioName
            let nbLikes = response.nbLikes
            let displayComment = response.displayComment
            let comments = response.comments
            let commentCount = comments.length;
            let isLikedByUser = response.isLikedByUser
            let likeButtonClass = response.isLikedByUser ? 'bloopBallonLiked' : 'bloopBallon';


            //video && images
            let images = response.image


            function escapeHTML(html) {
                var text = document.createTextNode(html);
                var div = document.createElement('div');
                div.appendChild(text);
                return div.innerHTML;
            }

            let commentsContainer = ``

            //comment input
            if (displayComment) {
                commentsContainer += `
                    <div class="inputComment overlaySection flex-row" data-overlay-target="overlaySection">
                            <input type="text" name="content" id="newCommentContent" data-overlay-target="commentInput" placeholder="Ecrivez un commentaire">
                            <i class="bi bi-chevron-double-right" data-action="click->overlay#sendCommentPodcast"></i>
                    </div>`

                //comment widget
                comments.forEach(comment => {
                    let isLikedByUser = comment.isLikedByUser
                    let likeButtonClass = comment.isLikedByUser ? 'bloopBallonLiked' : 'bloopBallon';


                    commentsContainer += `
                         <div class=" flex-row overlaySection" data-overlay-target="overlaySection">
                              <div class="flex-col innerParent">
                                  <div class="flex-row message__header">
                                        <div class="message__icon">
                                            <a href="/user/${comment.id}" class="link navbarLienProfil">
                                                <img class="imgPdp" src="${comment.author.authorImage}" alt="${comment.author.username}">
                                            </a>
                                        </div>
                                        <div class="message__name">
                                            <h3>${comment.author.username}<h3>
                                            <p class="sub-title">Publié le ${comment.humanReadableDate}</p>
                                        </div>
                                   </div>
                                     <div class="text-container text-container-cs" >
                                        <span class="text-content text-content-br" data-overlay-target="text" >${escapeHTML(comment.content)}</span>
                                        <span class="blur-overlay"  ></span>
                                    </div>   
                                
                      
                                      
                              </div>
                                 <div class="flex-col message__interactions ">
                                         <div class="flex-col-between">
                                             <div class="message__options">
                                              
                                              ${user_id === comment.author.id ? `  
                                                  <div class="dropdown">
                                                    <button class="btn  " type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                                <li><button class="dropdown-item deleteCommentBtn"  data-action="click->overlay#deleteCommentPodcast" data-overlay-comment-id-value="${comment.id}" >Supprimer</button></li>
                                                              
                                                                
                                                    </ul> 
                                                  </div>
                                                            ` : ''}
                                             </div>
                                        </div>
                                      <div data-controller="like" data-action="click->like#toggleLikeComment" data-like-comment-id-value="${comment.id}"   class="flex-col grid-center    ${likeButtonClass} ">  
                     
                                        <svg  class="bloopBallonLikedSvg"   width="24" height="24" viewBox="0 0 24 24" fill="none"
                                              xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M12.7351 16.3799C29.1181 5.07288 16.9201 -3.58062 12.0001 1.47888C7.07858 -3.58062 -5.11942 5.07288 11.2651 16.3799L10.9141 17.0819C10.892 17.126 10.8789 17.174 10.8754 17.2232C10.8719 17.2725 10.8781 17.3219 10.8937 17.3687C10.9252 17.4632 10.993 17.5413 11.0821 17.5859C11.1712 17.6304 11.2744 17.6378 11.3689 17.6063C11.4634 17.5748 11.5415 17.507 11.5861 17.4179L11.6461 17.2979C11.6596 17.5529 11.6821 17.7704 11.7226 17.9729C11.8246 18.4889 12.0346 18.9059 12.3946 19.6259L12.4141 19.6679C12.7321 20.3009 12.6871 20.9429 12.4891 21.5369C12.2866 22.1399 11.9401 22.6634 11.6881 23.0414C11.6328 23.1241 11.6126 23.2255 11.632 23.3231C11.6514 23.4207 11.7088 23.5066 11.7916 23.5619C11.8743 23.6172 11.9757 23.6373 12.0733 23.6179C12.1709 23.5985 12.2568 23.5411 12.3121 23.4584L12.3181 23.4479C12.5671 23.0759 12.9646 22.4789 13.2001 21.7754C13.4401 21.0569 13.5181 20.1989 13.0861 19.3319C12.6991 18.5594 12.5386 18.2339 12.4576 17.8259C12.4286 17.6829 12.41 17.538 12.4021 17.3924L12.4141 17.4179C12.4586 17.507 12.5368 17.5748 12.6313 17.6063C12.7258 17.6378 12.829 17.6304 12.9181 17.5859C13.0072 17.5413 13.075 17.4632 13.1065 17.3687C13.138 17.2742 13.1306 17.171 13.0861 17.0819L12.7351 16.3799ZM10.0891 1.90338C8.33858 0.988378 5.88908 1.69038 4.90808 3.60588C4.55258 4.30038 4.36808 5.22588 4.60508 6.38088C4.68758 6.78588 5.30558 6.67638 5.39558 6.27438C5.82308 4.39038 7.16108 2.58138 9.87908 2.48238C10.2301 2.47038 10.4011 2.06538 10.0891 1.90338Z"
                                                  fill="#DA9EFF"/>
                                        </svg> 
                                         <svg class="bloopBallonSvg"  width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_42_331)">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                      d="M12 3.63001L10.9245 2.52451C9.22954 0.783006 6.06004 1.35901 4.90954 3.60451C4.38454 4.63201 4.23304 6.16501 5.26354 8.1975C6.24004 10.1295 8.28004 12.48 12 15.063C15.7185 12.4785 17.7585 10.128 18.7365 8.1975C19.767 6.16501 19.617 4.63201 19.0905 3.60601C17.94 1.35901 14.7705 0.783006 13.0755 2.52451L12 3.63001ZM11.265 16.38C-4.90496 5.22001 6.76504 -3.35249 11.8035 1.28551C11.8715 1.34751 11.9375 1.41201 12.0015 1.47901C12.0665 1.41201 12.1325 1.34751 12.1995 1.28551C17.2395 -3.35249 28.905 5.22151 12.735 16.38L13.0875 17.082C13.1096 17.1261 13.1228 17.1742 13.1263 17.2234C13.1297 17.2726 13.1235 17.322 13.1079 17.3688C13.0923 17.4156 13.0677 17.4589 13.0353 17.4961C13.003 17.5334 12.9637 17.5639 12.9195 17.586C12.8754 17.6081 12.8274 17.6212 12.7782 17.6247C12.729 17.6282 12.6795 17.622 12.6327 17.6064C12.5859 17.5908 12.5427 17.5661 12.5054 17.5338C12.4681 17.5015 12.4376 17.4621 12.4155 17.418L12.4035 17.3925C12.4155 17.5575 12.4335 17.6955 12.459 17.8275C12.54 18.2325 12.7005 18.5595 13.0875 19.332C13.5195 20.199 13.44 21.057 13.2015 21.7755C12.966 22.479 12.5685 23.076 12.3195 23.448L12.3135 23.4585C12.2862 23.4995 12.251 23.5347 12.21 23.562C12.169 23.5894 12.1231 23.6084 12.0747 23.6181C12.0264 23.6277 11.9766 23.6277 11.9283 23.618C11.88 23.6084 11.834 23.5894 11.793 23.562C11.7521 23.5346 11.7169 23.4994 11.6895 23.4585C11.6621 23.4175 11.6431 23.3715 11.6335 23.3232C11.6239 23.2749 11.6239 23.2251 11.6335 23.1768C11.6431 23.1284 11.6622 23.0825 11.6895 23.0415C11.9415 22.6635 12.2895 22.1415 12.489 21.537C12.6885 20.943 12.7335 20.301 12.4155 19.668L12.396 19.626C12.036 18.906 11.826 18.489 11.724 17.973C11.6811 17.7503 11.655 17.5246 11.646 17.298L11.586 17.418C11.5642 17.4624 11.5338 17.5021 11.4966 17.5347C11.4593 17.5673 11.416 17.5922 11.3691 17.608C11.3222 17.6238 11.2727 17.6301 11.2233 17.6267C11.1739 17.6232 11.1257 17.61 11.0815 17.5879C11.0372 17.5657 10.9978 17.535 10.9655 17.4975C10.9332 17.46 10.9086 17.4165 10.8932 17.3695C10.8777 17.3225 10.8718 17.2729 10.8756 17.2235C10.8795 17.1742 10.893 17.1261 10.9155 17.082L11.268 16.38H11.265ZM9.01954 3.09001C8.04604 2.82001 6.79504 3.21451 6.24454 4.28701C6.04804 4.67401 5.87704 5.32051 6.12454 6.28951C6.21904 6.65551 6.74554 6.58651 6.85504 6.22501C7.17004 5.17951 7.79554 4.05451 8.89354 3.68701C9.21904 3.57751 9.34954 3.18151 9.01954 3.09001Z"
                                                      fill="black"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_42_331">
                                                    <rect width="24" height="24" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        <span data-like-target="likesCount">${comment.nbLikes}</span>
                                     </div>
                         
                             </div>
                        </div>
`
                })
            }
            //generation all content
            this.contentTarget.innerHTML = `
                    <div class="flex-row  ">
                        <div class="flex-col innerParent">
                            <div class="flex-row message__header">
                                <div class="message__icon">
                                    <a href="/user/${auteur_id}" class="link navbarLienProfil">
                                        <img class="imgPdp" src="${authorImage}" alt="${authorUsername}">
                                    </a>
                                </div>
                                <div class="message__name">
                                    <h3>${authorUsername}</h3>
                                    <p class="sub-title">Publié le ${createdAt}</p>
                                </div>
                            </div>
                            <div class="message__content">
                                    <div class="text-container text-container-cs">
                                        <span class="text-content"  'data-overlay-target="text"  {{ stimulus_target('text','description') }}>${escapeHTML(description)}
                                         </span>
                                        <span class="blur-overlay"  ></span>
                                    </div> 
                                     <div class="file-container file-container-audio" data-controller="audio">

                                        <audio id="audio" data-audio-target="audio" data-action="timeupdate->audio#audio"  >
                                            <source src="audios/${audioName}" type="audio/mp3">
                                            Votre navigateur ne supporte pas l'élément audio.
                                        </audio>
                                        <button class="btnRondViolet" id="playPauseBtn" data-audio-target="play" data-action="click->audio#playPauseBtn" >
                                            <img src="/images/playPause.png" alt="" draggable="false">
                                            <i class="bi bi-pause-fill"  data-audio-target="pause"></i>
                                        </button>
                                        <input type="range"
                                               id="progressBar" data-audio-target="progress" data-action="input->audio#progressBar"  
                                               value="0" max="100">
                                        <div class="soundVolumeContainer">
                                            <img src="/images/icPoulpeSound.png" data-audio-target="icSound" data-action="click->audio#clickIcAudio"
                                          
                                                 alt="">
                                            <input type="range" id="volumeControl" min="0" max="1" step="0.01"
                                                   value="1" data-audio-target="progressAudio" data-action="input->audio#progressAudio"
                                                

                                      </div>
                                  </div>  
                                </div>
                            </div>
                        </div>
                        <div class="flex-col-between">
                             <div class="message__options">
                                  ${user_id === auteur_id ? 
                `   <div class="dropdown">
                                    <button class="btn  " type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                
                                    </button>
                                    <ul class="dropdown-menu">
                
                                  
                                                <li><a class="dropdown-item" href="/podcast/edit/${podcastId}">Edit</a></li>
                                           
                                             
                
                                    </ul>     <li><a class="dropdown-item" href="/podcast/archiver/${podcastId}">Archivé</a></li>
                                           `  : ''}
                                </div>
                            </div>
                            <div class="flex-col message__interactions ">
                                    
                            <div data-controller="like" data-action="click->like#toggleLikePodcast" data-like-podcast-id-value="${podcastId}"   class="flex-col grid-center    ${likeButtonClass} ">  
                                                <svg  class="bloopBallonLikedSvg"   width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                      xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M12.7351 16.3799C29.1181 5.07288 16.9201 -3.58062 12.0001 1.47888C7.07858 -3.58062 -5.11942 5.07288 11.2651 16.3799L10.9141 17.0819C10.892 17.126 10.8789 17.174 10.8754 17.2232C10.8719 17.2725 10.8781 17.3219 10.8937 17.3687C10.9252 17.4632 10.993 17.5413 11.0821 17.5859C11.1712 17.6304 11.2744 17.6378 11.3689 17.6063C11.4634 17.5748 11.5415 17.507 11.5861 17.4179L11.6461 17.2979C11.6596 17.5529 11.6821 17.7704 11.7226 17.9729C11.8246 18.4889 12.0346 18.9059 12.3946 19.6259L12.4141 19.6679C12.7321 20.3009 12.6871 20.9429 12.4891 21.5369C12.2866 22.1399 11.9401 22.6634 11.6881 23.0414C11.6328 23.1241 11.6126 23.2255 11.632 23.3231C11.6514 23.4207 11.7088 23.5066 11.7916 23.5619C11.8743 23.6172 11.9757 23.6373 12.0733 23.6179C12.1709 23.5985 12.2568 23.5411 12.3121 23.4584L12.3181 23.4479C12.5671 23.0759 12.9646 22.4789 13.2001 21.7754C13.4401 21.0569 13.5181 20.1989 13.0861 19.3319C12.6991 18.5594 12.5386 18.2339 12.4576 17.8259C12.4286 17.6829 12.41 17.538 12.4021 17.3924L12.4141 17.4179C12.4586 17.507 12.5368 17.5748 12.6313 17.6063C12.7258 17.6378 12.829 17.6304 12.9181 17.5859C13.0072 17.5413 13.075 17.4632 13.1065 17.3687C13.138 17.2742 13.1306 17.171 13.0861 17.0819L12.7351 16.3799ZM10.0891 1.90338C8.33858 0.988378 5.88908 1.69038 4.90808 3.60588C4.55258 4.30038 4.36808 5.22588 4.60508 6.38088C4.68758 6.78588 5.30558 6.67638 5.39558 6.27438C5.82308 4.39038 7.16108 2.58138 9.87908 2.48238C10.2301 2.47038 10.4011 2.06538 10.0891 1.90338Z"
                                                          fill="#DA9EFF"/>
                                                </svg>
                
                
                                                <svg class="bloopBallonSvg"  width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_42_331)">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M12 3.63001L10.9245 2.52451C9.22954 0.783006 6.06004 1.35901 4.90954 3.60451C4.38454 4.63201 4.23304 6.16501 5.26354 8.1975C6.24004 10.1295 8.28004 12.48 12 15.063C15.7185 12.4785 17.7585 10.128 18.7365 8.1975C19.767 6.16501 19.617 4.63201 19.0905 3.60601C17.94 1.35901 14.7705 0.783006 13.0755 2.52451L12 3.63001ZM11.265 16.38C-4.90496 5.22001 6.76504 -3.35249 11.8035 1.28551C11.8715 1.34751 11.9375 1.41201 12.0015 1.47901C12.0665 1.41201 12.1325 1.34751 12.1995 1.28551C17.2395 -3.35249 28.905 5.22151 12.735 16.38L13.0875 17.082C13.1096 17.1261 13.1228 17.1742 13.1263 17.2234C13.1297 17.2726 13.1235 17.322 13.1079 17.3688C13.0923 17.4156 13.0677 17.4589 13.0353 17.4961C13.003 17.5334 12.9637 17.5639 12.9195 17.586C12.8754 17.6081 12.8274 17.6212 12.7782 17.6247C12.729 17.6282 12.6795 17.622 12.6327 17.6064C12.5859 17.5908 12.5427 17.5661 12.5054 17.5338C12.4681 17.5015 12.4376 17.4621 12.4155 17.418L12.4035 17.3925C12.4155 17.5575 12.4335 17.6955 12.459 17.8275C12.54 18.2325 12.7005 18.5595 13.0875 19.332C13.5195 20.199 13.44 21.057 13.2015 21.7755C12.966 22.479 12.5685 23.076 12.3195 23.448L12.3135 23.4585C12.2862 23.4995 12.251 23.5347 12.21 23.562C12.169 23.5894 12.1231 23.6084 12.0747 23.6181C12.0264 23.6277 11.9766 23.6277 11.9283 23.618C11.88 23.6084 11.834 23.5894 11.793 23.562C11.7521 23.5346 11.7169 23.4994 11.6895 23.4585C11.6621 23.4175 11.6431 23.3715 11.6335 23.3232C11.6239 23.2749 11.6239 23.2251 11.6335 23.1768C11.6431 23.1284 11.6622 23.0825 11.6895 23.0415C11.9415 22.6635 12.2895 22.1415 12.489 21.537C12.6885 20.943 12.7335 20.301 12.4155 19.668L12.396 19.626C12.036 18.906 11.826 18.489 11.724 17.973C11.6811 17.7503 11.655 17.5246 11.646 17.298L11.586 17.418C11.5642 17.4624 11.5338 17.5021 11.4966 17.5347C11.4593 17.5673 11.416 17.5922 11.3691 17.608C11.3222 17.6238 11.2727 17.6301 11.2233 17.6267C11.1739 17.6232 11.1257 17.61 11.0815 17.5879C11.0372 17.5657 10.9978 17.535 10.9655 17.4975C10.9332 17.46 10.9086 17.4165 10.8932 17.3695C10.8777 17.3225 10.8718 17.2729 10.8756 17.2235C10.8795 17.1742 10.893 17.1261 10.9155 17.082L11.268 16.38H11.265ZM9.01954 3.09001C8.04604 2.82001 6.79504 3.21451 6.24454 4.28701C6.04804 4.67401 5.87704 5.32051 6.12454 6.28951C6.21904 6.65551 6.74554 6.58651 6.85504 6.22501C7.17004 5.17951 7.79554 4.05451 8.89354 3.68701C9.21904 3.57751 9.34954 3.18151 9.01954 3.09001Z"
                                                              fill="black"/>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_42_331">
                                                            <rect width="24" height="24" fill="white"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                    <span data-like-target="likesCount">${nbLikes}</span>
                                </div>
                                <div onclick="" class="flex-col grid-center">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_42_338)">
                                            <path d="M14 1C14.2652 1 14.5196 1.10536 14.7071 1.29289C14.8946 1.48043 15 1.73478 15 2V10C15 10.2652 14.8946 10.5196 14.7071 10.7071C14.5196 10.8946 14.2652 11 14 11H4.414C3.88361 11.0001 3.37499 11.2109 3 11.586L1 13.586V2C1 1.73478 1.10536 1.48043 1.29289 1.29289C1.48043 1.10536 1.73478 1 2 1H14ZM2 0C1.46957 0 0.960859 0.210714 0.585786 0.585786C0.210714 0.960859 0 1.46957 0 2L0 14.793C2.10149e-05 14.8919 0.0293926 14.9886 0.0843973 15.0709C0.139402 15.1531 0.217567 15.2172 0.308999 15.255C0.400432 15.2928 0.501021 15.3026 0.598036 15.2832C0.695051 15.2638 0.784131 15.216 0.854 15.146L3.707 12.293C3.89449 12.1055 4.14881 12.0001 4.414 12H14C14.5304 12 15.0391 11.7893 15.4142 11.4142C15.7893 11.0391 16 10.5304 16 10V2C16 1.46957 15.7893 0.960859 15.4142 0.585786C15.0391 0.210714 14.5304 0 14 0L2 0Z"
                                                  fill="black"/>
                                            <path d="M3 3.5C3 3.36739 3.05268 3.24021 3.14645 3.14645C3.24021 3.05268 3.36739 3 3.5 3H12.5C12.6326 3 12.7598 3.05268 12.8536 3.14645C12.9473 3.24021 13 3.36739 13 3.5C13 3.63261 12.9473 3.75979 12.8536 3.85355C12.7598 3.94732 12.6326 4 12.5 4H3.5C3.36739 4 3.24021 3.94732 3.14645 3.85355C3.05268 3.75979 3 3.63261 3 3.5ZM3 6C3 5.86739 3.05268 5.74021 3.14645 5.64645C3.24021 5.55268 3.36739 5.5 3.5 5.5H12.5C12.6326 5.5 12.7598 5.55268 12.8536 5.64645C12.9473 5.74021 13 5.86739 13 6C13 6.13261 12.9473 6.25979 12.8536 6.35355C12.7598 6.44732 12.6326 6.5 12.5 6.5H3.5C3.36739 6.5 3.24021 6.44732 3.14645 6.35355C3.05268 6.25979 3 6.13261 3 6ZM3 8.5C3 8.36739 3.05268 8.24021 3.14645 8.14645C3.24021 8.05268 3.36739 8 3.5 8H8.5C8.63261 8 8.75979 8.05268 8.85355 8.14645C8.94732 8.24021 9 8.36739 9 8.5C9 8.63261 8.94732 8.75979 8.85355 8.85355C8.75979 8.94732 8.63261 9 8.5 9H3.5C3.36739 9 3.24021 8.94732 3.14645 8.85355C3.05268 8.75979 3 8.63261 3 8.5Z"
                                                  fill="black"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_42_338">
                                                <rect width="16" height="16" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <span>${commentCount}</span>
                                </div>
                            </div>
                        </div>
                    </div> `


            if (displayComment) {

                this.overlayTarget.innerHTML += commentsContainer

            }

            //to render full text
            this.textTargets.forEach(function (el) {
                console.log(el)
                console.log(el.textContent.length)
                if (el.textContent.length > 400) {
                    if (el.nextElementSibling) { // Ensure that there is an element sibling
                        el.nextElementSibling.style.display = 'block'; // This will show the next element sibling
                    } else {
                        console.log('No next element sibling found for', el);
                    }
                }
                el.addEventListener('click', () => {
                    if (el.parentElement.style.maxHeight === 'fit-content') {
                        el.nextElementSibling.style.display = 'block';
                        el.parentElement.style.maxHeight = '100px'
                    } else {
                        el.nextElementSibling.style.display = 'none';
                        el.parentElement.style.maxHeight = 'fit-content'
                    }

                })

            });

        }).catch(error => console.error('Toggle like failed:', error))
    }

    //to send com
    sendComment() {

        if (this.commentInputTarget.value.trim() !== '') {
            console.log(this.commentInputTarget.value, bloopId)
            axios.post('comment/new', {'content': this.commentInputTarget.value, 'idBloop': bloopId}).then(response => {
                response = response.data
                if (response.message === 'ok') {
                    this.commentInputTarget.value = ''
                    this.showComment()
                }
            })
        }
    }
    sendCommentPodcast() {

        if (this.commentInputTarget.value.trim() !== '') {
            axios.post('comment/podcast/new', {'content': this.commentInputTarget.value, 'idPodcast': podcastId}).then(response => {
                response = response.data
                if (response.message === 'ok') {
                    this.commentInputTarget.value = ''
                    this.showCommentPodcast()
                }
            })
        }
    }


    deleteComment(event) {
        let id = event.currentTarget.dataset.overlayCommentIdValue
        axios.post('/comment/delete', {'id': id}).then(response => {
            response = response.data
            if (response.message === 'ok') {
                this.showComment()
            } else if (response.message === 'Propriety error') {
                this.newError("Vous n'etes pas propriétaire de ce commentaire")
            } else {
                this.newError('Probleme lors de la suppression du commentaire')
            }
        }).catch((error) => {
            this.newError(`Erreur ${error}`)
        })
    }
    deleteCommentPodcast(event) {
        let id = event.currentTarget.dataset.overlayCommentIdValue
        axios.post('/comment/delete', {'id': id}).then(response => {
            response = response.data
            if (response.message === 'ok') {
                this.showCommentPodcast()
            } else if (response.message === 'Propriety error') {
                this.newError("Vous n'etes pas propriétaire de ce commentaire")
            } else {
                this.newError('Probleme lors de la suppression du commentaire')
            }
        }).catch((error) => {
            this.newError(`Erreur ${error}`)
        })
    }

}