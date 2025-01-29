import { Controller } from "@hotwired/stimulus";

// async function postData(url = "", donnees = null, method = 'GET') {
//     const response = await fetch(url, {
//         method: method,
//         headers: { "Content-Type": "application/json", "X-Requested-With":"XMLHttpRequest" },
//         body: donnees ? JSON.stringify(donnees) : null,
//     });
//     return response.json();
// }


export default class extends Controller{

    menuElement(event){
        let nav  = document.querySelector('nav');

        if(nav.classList.contains('active')){
            nav.classList.remove('active')
        }else{
            nav.classList.add('active')
        }
    }

    connect(){}
}