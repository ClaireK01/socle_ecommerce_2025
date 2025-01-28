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
        let previewCont = document.querySelector('.product-carousel-preview-container');
        let divs = document.querySelectorAll('.product-carousel-preview-card');
        let end = false;

        if(btn.dataset.type === "previous"){
            next = active.previousElementSibling;
            if(!next){
                next = divs[divs.length - 1]
                end = true;
            }

            //gestion scroll
            if(end){
                previewCont.scrollTop = next.offsetTop;
            }else {
                if (previewCont.scrollTop > next.offsetTop) {
                    previewCont.scrollTop = next.offsetTop;
                }
            }

        }else{
            next = active.nextElementSibling;
            if(!next){
                end = true;
                next = divs[0];
            }

            //gestion scroll
            if(end){
                previewCont.scrollTop = next.scrollTop;
            }else{
                let stylePreviewCont = getComputedStyle(previewCont);
                let stylePreviewContHeight = parseInt(stylePreviewCont.height.replace('px', ''));

                if(previewCont.scrollTop + stylePreviewContHeight < next.offsetTop){
                    previewCont.scrollTop = next.offsetTop;
                }
            }
        }

        document.querySelector(".carousel-product-img-main img").src = next.querySelector('img').src;
        next.classList.add('active');
        active.classList.remove('active');
    }

    connect(){}
}