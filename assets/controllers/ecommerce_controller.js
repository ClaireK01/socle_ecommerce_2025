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
    static targets = ["cart", "shopcount", "quantity", "quantbutton", "link", "target", "filter"];


    attributeTargetConnected(element){
        element.addEventListener('click', (e) => {
            let attrs = element.parentNode.querySelectorAll('.attribute');
            let list = document.querySelector('[data-list-attr]').dataset.listAttr
            attrs.forEach((a) => {
                if(a === element && !a.classList.contains('active')){
                    a.classList.add('active')
                }else{
                    if(a.classList.contains('active')){
                        a.classList.remove('active');
                    }
                }
            })
            //maj des attributes item selectionné
            let str = '';
            document.querySelectorAll('.attribute.active').forEach((a) => { str +=  a.dataset.attr + '|'; })
            document.querySelector('[data-list-attr]').dataset.listAttr = str
            //maj du prix avec attributs
            let url = document.querySelector('.price-tag').dataset.url
            postData(url, {'attributes' : str},  'POST').then((data) => {
                this.changeQuantity(data.total)
            });

            //TODO : maj des prix produit au fur et a mesure de la selection

        })

    }

    cartTargetConnected(element){
        element.addEventListener('click', (e) => {
            let url = element.dataset.add;
            let input = element.parentNode.querySelector('input[data-ecommerce-target]');
            let quantity = input.value;
            let attrs = document.querySelector('[data-list-attr]');
            if(attrs){
                attrs = attrs.dataset.listAttr;
            }
            postData(url, {"quantity" : quantity, 'attributes' : attrs},  'POST').then((data) => {
                this.shopcountTarget.innerHTML = data.cart['total_quantity'] ?? 0;
                input.value = input.min
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
                input.value = val;
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

    filterTargetConnected(element){
        let event = null;
        if(element.tagName === "input"){
           if(element.type === "checkbox"){
               event = "click";
           }else{
               event = "input";
           }
        }else if(element.tagName === "select"){
            event = "change";
        }

        element.addEventListener(event, (e) => {
            console.log(element.value)
        })
    }

    changeQuantity = (total) => {
        let priceTag = document.querySelector('.price-tag-num');
        // let quantity = parseInt(element.value);
        // let number = parseFloat(priceTag.dataset.price);
        // let total = number * quantity;
        if(!isNaN(total)){
           let strTotal = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(total)
            priceTag.innerHTML = strTotal.replace('€', '').replace(/\&nbsp;/g, '');
        }
    }

    connect(){}
}