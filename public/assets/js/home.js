var currentPage = $("#homePage").attr("id");

if (currentPage === "homePage") {
  var swiper = new Swiper(".swiper-container", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    coverflowEffect: {
      rotate: 1,
      stretch: 0,
      depth: 50,
      modifier: 2,
    },
    pagination: {
      el: ".swiper-pagination",
    },
    on: {
      init: function () {
        slideChange.call(this);
      },
      slideChange: slideChange,
    },
  });

  function setSlidesPerView() {
    var screenWidth = window.innerWidth;

    if (screenWidth >= 1000) {
      swiper.params.slidesPerView = 4;
    } else if (screenWidth >= 768) {
      swiper.params.slidesPerView = 3;
    } else if (screenWidth >= 300) {
      swiper.params.slidesPerView = 2;
    } else{
      swiper.params.slidesPerView = 1;
    }

    swiper.update();
  }

  setSlidesPerView();

  window.addEventListener("resize", setSlidesPerView);

  slideChange.call(swiper);

  function slideChange() {
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const likeButtons = document.querySelectorAll(".add-to-like-button");

  likeButtons.forEach((button) => {
    const heartIcon = button.querySelector(".card_heart");
    
    button.addEventListener("click", function (event) {
      event.preventDefault();

      if (!heartIcon.classList.contains("clicked")) {
        heartIcon.classList.add("clicked");
        heartIcon.setAttribute("data-tooltip", "Je n'aime plus ce produit.");

        const productId = heartIcon.getAttribute("data-product-id");
        fetch(`/like_product/${productId}`, {
          method: 'POST',
        })
        .then(response => {
          if (response.ok) {
            console.log('Product liked:', productId);
          } else {
            console.error('Failed to add like:', productId);
          }
        })
        .catch(error => {
          console.error('An error occurred:', error);
        });
      } else {
        heartIcon.classList.remove("clicked");
        heartIcon.setAttribute("data-tooltip", "J'aime ce produit!");

        const productId = heartIcon.getAttribute("data-product-id");
        fetch(`/unlike_product/${productId}`, {
          method: 'POST',
        })
        .then(response => {
          if (response.ok) {
            console.log('Product unliked:', productId);
          } else {
            console.error('Failed to remove like:', productId);
          }
        })
        .catch(error => {
          console.error('An error occurred:', error);
        });
      }
    });
  });
});



//   document.addEventListener('DOMContentLoaded', function () {
//     const filterButtons = document.querySelectorAll('.filter-button');
//     const productList = document.querySelector('.product-list');

//     filterButtons.forEach(function (button) {
//         button.addEventListener('click', function () {
//             const criteria = button.getAttribute('data-criteria');
//             filterProducts(criteria);
//         });
//     });

//     function filterProducts(criteria) {
//         fetch(`/filter-products/${criteria}`, {
//             method: 'GET',
//         })
//         .then(response => response.json())
//         .then(data => {
//             productList.innerHTML = '';

//             data.forEach(product => {
//                 const productItem = document.createElement('li');
//                 productItem.textContent = product.productTitle;
//                 productList.appendChild(productItem);
//             });
//         })
//         .catch(error => {
//             console.error('Error:', error);
//         });
//     }
//   });
// });