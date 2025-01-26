// core version + navigation, pagination modules:
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, Grid } from 'swiper/modules';


/***************** slider *******************************/




// import Swiper and modules styles

// let btnPrevNews = document.querySelector('.swiper-news').querySelector('.swiper-button-prev');
// let btnNextNews = document.querySelector('.swiper-news').querySelector('.swiper-button-next');
new Swiper('.swiper-news', {
    // configure Swiper to use modules
    modules: [Navigation, Pagination, Autoplay],
    slidesPerView: 1,
    autoplay:true,
    loop: true,
    // If we need pagination
    pagination: {
        el: '.swiper-pagination-news',
    },
    navigation: {
        // nextEl: btnNextNews,
        // prevEl: btnPrevNews,
    },
});

/******************************** old*********************************************/

// const swiper = new Swiper('.swiper', {
//     // configure Swiper to use modules
//     modules: [Navigation, Pagination],
//     slidesPerView: 1,
//     autoplay:true,
//     loop: true,
//     // If we need pagination
//     pagination: {
//         el: '.swiper-pagination',
//     },
//     // Navigation arrows
//     navigation: {
//         nextEl: '.swiper-button-next',
//         prevEl: '.swiper-button-prev',
//     },
// });
//
// if(document.querySelector('.swiper-product')){
//     let btnPrevProduct = document.querySelector('.swiper-product').querySelector('.swiper-button-prev');
//     let btnNextProduct = document.querySelector('.swiper-product').querySelector('.swiper-button-next');
//     new Swiper('.swiper-product', {
//         // configure Swiper to use modules
//         modules: [Navigation, Pagination],
//         slidesPerView: 2,
//         autoplay:true,
//         loop: true,
//         // If we need pagination
//         pagination: {
//             el: '.swiper-pagination-product',
//         },
//         navigation: {
//             nextEl: btnNextProduct,
//             prevEl: btnPrevProduct,
//         },
//     });
// }
//
// if(document.querySelector('.swiper-article')){
//     let btnPrevProduct = document.querySelector('.swiper-article').querySelector('.swiper-button-prev');
//     let btnNextProduct = document.querySelector('.swiper-article').querySelector('.swiper-button-next');
//     new Swiper('.swiper-article', {
//         // configure Swiper to use modules
//         modules: [Navigation, Pagination],
//         slidesPerView: 2,
//         autoplay:true,
//         loop: true,
//         // If we need pagination
//         pagination: {
//             el: '.swiper-pagination-article',
//         },
//         navigation: {
//             nextEl: btnNextProduct,
//             prevEl: btnPrevProduct,
//         },
//     });
// }