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
    static targets = ["cart", "shopcount", "quantity", "quantbutton", "link"];

    cartTargetConnected(element){
        element.addEventListener('click', (e) => {
            let url = element.dataset.add;
            let quantity = element.parentNode.querySelector('input[data-ecommerce-target]').value
            postData(url, {"quantity" : quantity},  'POST').then((data) => {
                this.shopcountTarget.innerHTML = data.cart['total_quantity'] ?? 0;
            });
        })
    }

    quantityTargetConnected(element){
        element.addEventListener('input', (e) => {
            let url = element.dataset.url;
            let urlAdd = element.dataset.add;
            let quantity = element.value

            postData(urlAdd, {"quantity" : quantity},  'POST').then((data) => {
                this.shopcountTarget.innerHTML = data.cart['total_quantity'] ?? 0;
                postData(url).then((view) => {
                    document.querySelector('.table-products').innerHTML = view.view;
                });
            });
        })
    }

    quantbuttonTargetConnected(element){
        let input = element.parentNode.querySelector('input');
        element.addEventListener("click", (e) => {
            let val = parseInt(input.value);
            if(!val){
                val = 0;
            }
            if(element.value === "minus"){
                val =  val - 1;
            } else{
                val = val + 1;
            }
            if(val >= 1){
                input.value = val
            }
        })
    }

    linkTargetConnected(element){
        element.addEventListener('click', function(e){
            let el = e.target ;
            if(e.target.nodeName !== "BUTTON" && e.target.nodeName !== "I" && e.target.nodeName !== "INPUT"){
                window.location.href = element.dataset.link;
            }
        })
    }

    // changeQuantity = (element) => {
    //     let priceTag = document.querySelector('.price-tag-num');
    //     let quantity = parseInt(element.value);
    //     let number = parseFloat(priceTag.dataset.price);
    //     let total = number * quantity;
    //     if(!isNaN(total)){
    //        let strTotal = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(total)
    //         priceTag.innerHTML = strTotal.replace('€', '').replace(/\&nbsp;/g, '');
    //     }
    // }

    connect(){}
}