import { Controller } from "@hotwired/stimulus";

async function postData(url = "", donnees = null, method = 'GET') {
    const response = await fetch(url, {
        method: method,
        headers: { "Content-Type": "application/json" },
        body: donnees ? JSON.stringify(donnees) : null,
    });
    return response.json();
}
export default class extends Controller{
    static targets = ["editBtn", "pen", "validate", "cancel"];

    editBtnTargetConnected(element){
        element.addEventListener('click', (e) => {
            this.penTargets.forEach((el) => {
                this.toggleClass(el.parentNode, "show-edit");
            })
        })
    }

    penTargetConnected(element){
        element.addEventListener('click', (e) => {
            // let icons = element.querySelector('.edit-btn');
            this.toggleClass(element, "show-actions");
            this.toggleClass(element, "show-pen");
            this.toggleClass(element.parentNode, 'show');
        })
    }

    validateTargetConnected(element) {
        element.addEventListener('click', (e) => {
            let data = {};
            let token = document.querySelector('[data-token]').dataset.token;
            let url = document.querySelector('.profile-container').dataset.url;
            let infoContainer = element.closest('.profile').querySelector('.infos');
            let input = element.closest('.profile').querySelector('input') != undefined ? element.closest('.profile').querySelector('input') : element.closest('.profile').querySelector('textarea') ;

            data[input.name] = input.value;
            data["token"] = token;

            postData(url, data, "POST").then((response) => {
                infoContainer.innerHTML = response.data;
                this.toggleClass(element, "show-actions");
                this.toggleClass(element, "show-pen");
            });
        });
    }

    cancelTargetConnected(element) {
        element.addEventListener('click', (e) => {
            let infoContainer = element.closest('.profile').querySelector('.infos');
            let input = element.closest('.profile').querySelector('input') != undefined ? element.closest('.profile').querySelector('input') : element.closest('.profile').querySelector('textarea') ;

            input.value = infoContainer.innerHTML;
            this.toggleClass(element.closest('.profile-edit'), "show")
        });
    }

    toggleClass(target, original, replacement = null){
        if(!replacement){
            if(!target.classList.contains(original)){
                target.classList.add(original);
            }else{
                target.classList.remove(original);
            }
        }else{
            if(target.classList.contains(original)){
                target.classList.remove(original);
                target.classList.add(replacement);
            }else{
                target.classList.remove(replacement);
                target.classList.add(original);
            }
        }
    }


    connect(){}
}