import { Controller } from "@hotwired/stimulus";

async function postData(url = "", donnees = null, method = 'GET') {
    const response = await fetch(url, {
        method: method,
        headers: { "Content-Type": "application/json", "X-Requested-With":"XMLHttpRequest" },
        body: donnees ? JSON.stringify(donnees) : null,
    });
    return response.json();
}


export default class extends Controller{

    cardHoverElement(event){
        let div = event.currentTarget;
        let divs = document.querySelectorAll('.product-carousel-preview-card');
        divs.forEach(function(el){
            if(el !== div && el.classList.contains('active')){
                el.classList.remove('active');
            }
        })
        document.querySelector(".carousel-product-img-main img").src = div.querySelector('img').src;
        div.classList.add('active');
    }

    previewButtonElement(event){
        event.preventDefault();
        let btn = event.currentTarget;
        let active = document.querySelector('.active');
        let next = null;
        if(btn.dataset.type === "previous"){
            next = active.previousElementSibling;
        }else{
            next = active.nextElementSibling;
        }
        document.querySelector(".carousel-product-img-main img").src = next.querySelector('img').src;
        next.classList.add('active');
        active.classList.remove('active');
    }

    connect(){}
}