import {Controller} from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static targets = ['optionsNavbar', 'logoContainer', 'mainNavbar', 'contentContainer', 'friendOption', 'burgerOption', 'friendListContainer']
    static values = {
        'status': String,
        'statusFriends': String,
    }

    connect() {

    }

    toOpen() {
        //open navbar
        if (this.statusFriendsValue === 'open') {
            this.toCloseFriends()
        }

        this.element.style.overflowY = 'hidden'
        this.mainNavbarTarget.style.display = 'block'
        this.optionsNavbarTarget.style.display = 'block'
        this.logoContainerTarget.style.display = 'flex'
        this.contentContainerTarget.style.display = 'none'
        this.statusValue = 'open'
        this.friendOptionTarget.style.display = 'none'

    }

    toClose() {
        //close navbar
        this.element.style.overflowY = 'scroll'

        this.mainNavbarTarget.style.display = 'none'
        this.optionsNavbarTarget.style.display = 'none'
        this.logoContainerTarget.style.display = 'none'
        this.contentContainerTarget.style.display = 'flex'
        this.statusValue = 'close'
        this.friendOptionTarget.style.display = 'flex'
    }

    toggle() {
        if (this.statusValue === 'open') {
            this.toClose()
        } else {
            this.toOpen()
        }
    }


    toOpenFriends() {
        if (this.statusValue === 'open') {

            this.toClose()
        }
        this.element.style.overflowY = 'hidden'
        this.friendListContainerTarget.style.display = 'block'
        window.requestAnimationFrame(() => {
            if (window.innerWidth <= 850) {

                this.friendListContainerTarget.style.width = '100vw';
            }
                this.friendListContainerTarget.style.width = '88vw';
        });
        this.statusFriendsValue = 'open'

    }

    toCloseFriends() {
        //close friends panel

        this.element.style.overflowY = 'scroll'
        window.requestAnimationFrame(() => {
            this.friendListContainerTarget.style.width = '0vw';
        });

        this.friendListContainerTarget.style.display = 'none'
        this.statusFriendsValue = 'close'
    }

    toggleFriends() {
        if (this.statusFriendsValue === 'open') {
            this.toCloseFriends()
        } else {
            this.toOpenFriends()
        }
    }

}