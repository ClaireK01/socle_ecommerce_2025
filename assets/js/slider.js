// core version + navigation, pagination modules:
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, Grid } from 'swiper/modules';
import 'swiper/swiper-bundle.css'; // Import Swiper styles



/***************** slider *******************************/




// import Swiper and modules styles


new Swiper('.swiper-header', {
    // configure Swiper to use modules
    modules: [Navigation, Pagination, Autoplay],
    direction: 'horizontal',
    slidesPerView: 1,
    autoplay:true,
    loop: true,
    // If we need pagination
    pagination: {
        el: '.swiper-pagination-header',
    },
    // Navigation arrows
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});

new Swiper('.swiper-artweek', {
    // configure Swiper to use modules
    modules: [Navigation, Pagination, Autoplay],
    slidesPerView: 1,
    autoplay:true,
    loop: true,
    // If we need pagination
    pagination: {
        el: '.swiper-pagination-artweek',
    },
});

if(document.querySelector('.swiper-artist')){
    let btnPrevArtist = document.querySelector('.swiper-artist').querySelector('.swiper-button-prev');
    let btnNextArtist = document.querySelector('.swiper-artist').querySelector('.swiper-button-next');
    new Swiper('.swiper-artist', {

        // configure Swiper to use modules
        modules: [Navigation, Pagination, Autoplay],
        slidesPerView: 4,
        autoplay:false,
        loop: true,
        // If we need pagination
        pagination: {
            el: '.swiper-pagination-artist',
        },
        navigation: {
            nextEl: btnNextArtist,
            prevEl: btnPrevArtist,
        },
    });
}

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

if(document.querySelector('.swiper-offer')){
    let btnPrevOffer = document.querySelector('.swiper-offer').querySelector('.swiper-button-prev');
    let btnNextOffer = document.querySelector('.swiper-offer').querySelector('.swiper-button-next');
    new Swiper('.swiper-offer', {

        // configure Swiper to use modules
        modules: [Navigation, Pagination, Autoplay],
        slidesPerView: 3,
        autoplay:false,
        loop: true,
        // If we need pagination
        pagination: {
            el: '.swiper-pagination-offer',
        },
        navigation: {
            nextEl: btnNextOffer,
            prevEl: btnPrevOffer,
        },
    });
}

if(document.querySelector('.swiper-offer-double')){
    let btnPrevOffer = document.querySelector('.swiper-offer-double').querySelector('.swiper-button-prev');
    let btnNextOffer = document.querySelector('.swiper-offer-double').querySelector('.swiper-button-next');
    new Swiper('.swiper-offer-double', {
        // configure Swiper to use modules
        modules: [Navigation, Pagination, Autoplay, Grid],
        slidesPerView: 3, // Number of slides visible per row
        slidesPerGroup: 6, // Number of slides visible per row
        grid: {
            rows: 2, // Number of rows
            fill: 'row', // Fill mode; can also be 'column'
        },
        autoplay: false,
        loop: false,
        // If we need pagination
        pagination: {
            el: '.swiper-pagination-offer-double',
            clickable: true
        },
        navigation: {
            nextEl: btnNextOffer,
            prevEl: btnPrevOffer,
        },
    });
}





if(document.querySelector('.swiper-art')){
    let btnPrevArt = document.querySelector('.swiper-art').querySelector('.swiper-button-prev');
    let btnNextArt = document.querySelector('.swiper-art').querySelector('.swiper-button-next');
    new Swiper('.swiper-art', {
        // configure Swiper to use modules
        modules: [Navigation, Pagination, Autoplay],
        slidesPerView: 3,
        autoplay:false,
        loop: true,
        // If we need pagination
        pagination: {
            el: '.swiper-pagination-art',
        },
        navigation: {
            nextEl: btnNextArt,
            prevEl: btnPrevArt,
        },
    });
}


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