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
        //si indexValue = 0 -> aller chercher le nombre de path
        item.innerHTML = pr.replace(/__name__/g, this.indexValue);
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
        console.log('co')
        element.addEventListener("click", (e) => {
            let form = element.parentNode.parentNode;
            form.parentNode.removeChild(form)
        })
    }

    connect(){}
}

