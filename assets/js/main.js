import "bootstrap";
import 'jquery';
import '../bootstrap';


const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if(entry.isIntersecting){
            entry.target.classList.add('show');
        }else{
            entry.target.classList.remove('show');
        }
    })
})

const sections = document.querySelectorAll('.delayed');
sections.forEach((el) => { observer.observe(el)})


