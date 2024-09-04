import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["collectionContainer", "fileInput", "fileManager", "deleteForm"]

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

    fileManagerTargetConnected(element) {
        element.addEventListener("click", (e) => {
            document.querySelector('body').classList.add('show-modal');
            let div = element.parentNode.parentNode;
            document.querySelector('.modal-file').dataset.input =  div.querySelector('[data-path]').id;
        })
    }

    deleteFormTargetConnected(element) {
        element.addEventListener("click", (e) => {
            let form = element.parentNode.parentNode.parentNode;
            form.parentNode.removeChild(form)
        })
    }

    connect(){}
}

