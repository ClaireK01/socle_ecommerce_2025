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
            this.toggleClass(element.parentNode, "show");
            this.penTargets.forEach((el) => {
                if(el.classList.contains('show-actions')){
                    el.classList.remove('show-actions');
                    el.parentNode.classList.remove('show-input');
                    this.resetInput(el);
                }else if(el.classList.contains('show-pen')){
                    el.classList.remove('show-pen');
                }else{
                    this.toggleClass(el, "show-pen");
                }
            })
        })
    }

    penTargetConnected(element){
        element.addEventListener('click', (e) => {
            this.toggleClass(element, "show-pen", "show-actions");
            this.toggleClass(element.parentNode, "show-input");
        })
    }

    validateTargetConnected(element) {
        element.addEventListener('click', (e) => {
            let data = {};
            let token = document.querySelector('[data-token]').dataset.token;
            let url = document.querySelector('.profile-container').dataset.url;
            let infoContainer = element.closest('.profile').querySelector('.infos');
            let input = this.getInput(element);

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
           this.resetInput(element);
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

    getInput(element){
        return  element.closest('.profile').querySelector('input') != undefined ? element.closest('.profile').querySelector('input') :
                element.closest('.profile').querySelector('textarea');
    }

    resetInput(element){
        let infoContainer = element.closest('.profile').querySelector('.infos');
        let input = this.getInput(element)
        input.value = infoContainer.innerHTML;
    }


    connect(){}
}