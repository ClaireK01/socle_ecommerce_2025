import { Controller } from "@hotwired/stimulus";

async function postData(url = "", donnees = null, method = 'GET') {
    const response = await fetch(url, {
        method: method,
        headers: { "Content-Type": "application/json", "X-Requested-With":"XMLHttpRequest" },
        body: donnees ? JSON.stringify(donnees) : null,
    });
    return response.json();
}

async function postFormData(url = "", donnees = null, method = 'POST') {
    const response = await fetch(url, {
        method: method,
        body: donnees,
    });
    return response.json();
}


export default class extends Controller{
    // static targets = ["formButton"];

    popupFormElement(event){
        event.preventDefault();

        let url = event.currentTarget.dataset.url;
        document.querySelector("#btn-add").dataset.rte = url;

        postData(url, null,  'GET').then((data) => {
            document.querySelector('.popup-view').innerHTML = data.view
            document.querySelector('body').classList.add('show-popup');
        });

    }

    popupActionElement(event){
        event.preventDefault();
        let form = document.querySelector('.popup-view form');
        let url = event.currentTarget.dataset.rte;
        let data = new FormData(form);

        postFormData(url, data,  'POST').then((dt) => {
            document.querySelector('body').classList.remove('show-popup');
            document.querySelector('.popup-view').innerHTML = '';
            document.querySelector('.view').innerHTML = dt.view ;
        });
    }

    popupCloseFormElement(event){
        event.preventDefault();
        document.querySelector('body').classList.remove('show-popup');
        document.querySelector('.popup-view').innerHTML = '';
    }


    connect(){}
}