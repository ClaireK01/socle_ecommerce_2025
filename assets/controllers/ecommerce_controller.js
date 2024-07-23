import { Controller } from "@hotwired/stimulus";

async function postData(url = "", donnees = {}) {
    const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(donnees),
    });
    return response.json();
}

export default class extends Controller{
    static targets = ["cart", "shopcount"];

    connect(){
        this.cartTarget.addEventListener('click', (e) => {
            let url = e.target.dataset.add;
            postData(url, {"quantity" : 1}).then((data) => {
                this.shopcountTarget.innerHTML = data.cart['total_quantity'];
            });
        })
    }
}