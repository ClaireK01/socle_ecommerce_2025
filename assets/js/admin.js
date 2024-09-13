//cle tinyMCE : m51d69rxpncc07ntwyxby0kmglvnuhvld8s6n40t8o4ec4ly

import tinymce from "tinymce";
import TomSelect from "tom-select";
import '../bootstrap';


let items = document.querySelectorAll('.nav-item');
items.forEach((item) => {
    item.addEventListener('click', (e) => {
            e.preventDefault();
            //faire en sorte qu'il ne prenne que enfant de translation fields
            let tabs = document.querySelectorAll('.tab-pane');
            if(tabs){
                let id = e.currentTarget.querySelector('a').href.split('#').pop();
                tabs.forEach((el) => {
                    if(el.id !== id){
                        el.classList.remove("active")
                    }else{
                        el.classList.add("active")
                    }
                })
            }
        })
})


tinymce.init({
    selector: 'textarea',
    plugins: '',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
});

var settings = {};
if(document.querySelector('#tva_rate')){
    new TomSelect('#tva_rate', settings);
}
if(document.querySelector('#product_categories')){
    new TomSelect('#product_categories', settings);
}
if(document.querySelector('#product_tva')){
    new TomSelect('#product_tva', settings);
}
if(document.querySelector('#category_parent')){
    new TomSelect('#category_parent', settings);
}

if(document.querySelector('#product_attributeItems')){
    new TomSelect('#product_attributeItems', settings);
}

