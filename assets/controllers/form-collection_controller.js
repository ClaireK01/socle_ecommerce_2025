import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["collectionContainer", "fileInput", "fileManager", "deleteForm", "frame", "closeFrame"]

    static values = {
        index    : Number,
        prototype: String,
    }

    addCollectionElement(event)
    {
        const item = document.createElement('li');
        let pr = event.target.parentNode.dataset.formCollectionPrototypeValue;
        // item.innerHTML = this.prototypeValue.replace(/__name__/g, this.indexValue);
        let value = this.indexValue;
        if(value <= 0){
            value = document.querySelectorAll('[data-path]').length;
            this.indexValue = value;
        }
        item.innerHTML = pr.replace(/__name__/g, value);
        this.collectionContainerTarget.appendChild(item);
        this.indexValue++;
    }

    closeFrameElement(event){
        document.querySelector('body').classList.remove('show-modal');
    }

    fileManagerTargetConnected(element) {
        element.addEventListener("click", (e) => {
            document.querySelector('body').classList.add('show-modal');
            let div = element.parentNode.parentNode;
            document.querySelector('.modal-file').dataset.input =  div.querySelector('[data-path]').id;
        })
    }

    deleteFormTargetConnected(element) {
        element.addEventListener("click", (e) => {
            let form = element.parentNode.parentNode.parentNode.parentNode; //degeu, utiliser closest
            form.parentNode.removeChild(form)
        })
    }

    frameTargetConnected(element){
        document.getElementById('btn-upload').addEventListener('click',function (e) {
            let innerDoc = (element.contentDocument) ? element.contentDocument : element.contentWindow.document;
            let select = innerDoc.querySelector('input:checked').closest('.file-wrapper.img').querySelector('.select');
            if (select) {
                //update path data - modal file contains the id of the path input to replace, set in fileManagertTarget
                let pathInput = document.getElementById(document.querySelector('.modal-file').dataset.input);
                pathInput.value = select.dataset.path;
                //update img src
                let li = pathInput.closest("li");
                let img = li.querySelector('.picture-preview') ? li.querySelector('.picture-preview').querySelector('img') : null;
                if (img) {
                    img.src = select.dataset.path
                } else {
                    let div = document.createElement('div');
                    div.classList.add('picture-preview');
                    img = document.createElement('img');
                    img.src = select.dataset.path
                    li.prepend(div);
                    div.appendChild(img);
                }
            }
            document.querySelector('body').classList.remove('show-modal');
        })
    }

    connect(){}
}

