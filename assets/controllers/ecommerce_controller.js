import { Controller } from "@hotwired/stimulus";

async function postData(url = "", donnees = null, method = 'GET') {
    const response = await fetch(url, {
        method: method,
        headers: { "Content-Type": "application/json" , "X-Requested-With":"XMLHttpRequest"},
        body: donnees ? JSON.stringify(donnees) : null,
    });
    return response.json();
}

export default class extends Controller{
    static targets = ["cart", "shopcount", "quantity", "quantbutton", "link", "target", "filter"];


    cartPreviewElement(event){
        event.preventDefault();
        postData(document.querySelector('.cart-preview-container').dataset.url).then((data) => {
            document.querySelector('.cart-preview').innerHTML = data.view;
            if(!document.querySelector("body").classList.contains('show-prev')){
                document.querySelector("body").classList.add('show-prev');
            }
        });

    }

    cartPreviewRemoveElement(event){
        event.preventDefault();
        postData(event.currentTarget.dataset.url).then((data) => {
            document.querySelector('.cart-preview').innerHTML = data.cartview;
            document.querySelector('.shop-count').innerHTML = data.shopcount;
        });

    }

    cartPreviewCloseElement(event){
        event.preventDefault();
        if(document.querySelector("body").classList.contains('show-prev')){
            document.querySelector("body").classList.remove('show-prev');
        }
    }

    promoCodeElement(event){
        event.preventDefault();
        let url = event.currentTarget.dataset.url;
        let input = event.currentTarget.parentNode.querySelector('.code-input');
        let code = input.value;

        postData(url, {"promo" : code},  'POST').then((data) => {
            if( document.querySelector('.cart-table')){
                document.querySelector('.cart-table').innerHTML = data.view;
                let mess = document.querySelector(".promo-message");
                mess.innerHTML = data.message;
                if(data.code === 201){
                    mess.classList.remove('form-succ');
                    mess.classList.add('form-err');
                }else{
                    mess.classList.add('form-succ');
                    mess.classList.remove('form-err');
                }
            }
        });
    }

    attributeTargetConnected(element){
        element.addEventListener('click', (e) => {
            this.updateAttribute(element);
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
                document.querySelector('.cart-preview').innerHTML = data.cartview;
                if(!document.querySelector("body").classList.contains('show-prev')){
                    document.querySelector("body").classList.add('show-prev');
                }
            });
            // postData(document.querySelector('.cart-preview-container').dataset.url).then((data) => {
            //
            // });
        })
    }

    quantityTargetConnected(element){
        element.addEventListener('input', (e) => {
            let url = element.dataset.url;
            let urlAdd = element.dataset.add;
            let quantity = element.value

            postData(urlAdd, {"quantity" : quantity},  'POST').then((data) => {
                if(document.querySelector("[data-ecommerce-target=shopcount]")){
                    this.shopcountTarget.innerHTML = data.cart['total_quantity'] ?? 0;
                }
                document.querySelector('.cart-preview').innerHTML = data.cartview;
                if( document.querySelector('.cart-table')){
                    postData(url).then((view) => {
                        document.querySelector('.cart-table').innerHTML = view.view;
                    });
                }
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
        if(element.tagName === "INPUT" || element.tagName === "A"){
            if(element.type === "checkbox" || element.tagName === "A"){
                event = "click";
            }else{
                event = "input";
            }
        }else if(element.tagName === "SELECT"){
            event = "change";
        }

        element.addEventListener(event, (e) => {
            e.preventDefault();
            let form = document.querySelector('.filter form')
            var formData = new FormData(form);
            let data = {};
            let url = element.dataset.url ? element.dataset.url  : form.dataset.url;

            for (var p of formData) {
                if( data[p[0]]){
                    //deconstruction qui pour une meme valeur ajoute aux contenue precedent (pour param multiple)
                    data[p[0]] =  [ p[1], ...[ data[p[0]] ]];
                }else{
                    data[p[0]] =  p[1];
                }

            }

            postData(url,{"filter" : data},  'POST').then((data) => {
                document.querySelector('.list-view').innerHTML = data.view
            });
        })
    }

    changeQuantity = (total) => {
        let priceTag = document.querySelector('.price-tag-num');
        if(!isNaN(total)){
            let strTotal = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(total)
            priceTag.innerHTML = strTotal.replace('€', '').replace(/\s/g, '');
        }
    }

    updateAttribute = (element) => {
        let attrs = document.querySelectorAll('.attribute');
        // let list = document.querySelector('[data-list-attr]').dataset.listAttr
        attrs.forEach((a) => {
            if(a === element && !a.classList.contains('active')){
                a.classList.add('active')
            }

            if(a.classList.contains('active') && a !== element){
                a.classList.remove('active');
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
    }

    connect(){}
}