var currentPage = $("#homePage").attr("id");

if (currentPage === "homePage") {
  var swiper = new Swiper(".swiper-container", {
    effect: "coverflow",
    grabCursor: true,
    // width: "100%",
    slidesPerView: 4,
    spaceBetween: 20,
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

  slideChange.call(swiper);

  function slideChange() {
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const addToCartButtons = document.querySelectorAll('.add-to-cart-button');

  addToCartButtons.forEach(button => {
      button.addEventListener('click', function (event) {
          event.preventDefault();

          const productId = button.getAttribute('data-product-id');
          
          fetch(`/add_to_cart/${productId}`, {
              method: 'POST',
          })
          .then(response => {
            try {
                return response.json();
            } catch (error) {
                console.error('Error parsing JSON:', error);
                throw error;
            }
        })
        .then(data => {
            console.log(data);
        })
        .catch(error => {
          console.error('An error occurred:', error);
      
          if (error.response && error.response.status && error.response.statusText) {
              console.error('Status:', error.response.status, 'Status Text:', error.response.statusText);
          } else {
              console.error('Unable to retrieve status information.');
          }
      });            
      });
  });
});

// JavaScript to trigger the modal after adding to cart

document.addEventListener("DOMContentLoaded", function () {
  const heartIcons = document.querySelectorAll(".card_heart");
  const cartModal = new bootstrap.Modal(document.getElementById("cartModal"));

  heartIcons.forEach((heartIcon) => {
    heartIcon.addEventListener("click", function (event) {
      event.preventDefault();
      if (!heartIcon.classList.contains("clicked")) {
        heartIcon.classList.add("clicked");
        cartModal.show();
        heartIcon.setAttribute("data-tooltip", "Retirer du Panier"); // Update tooltip text
        setTimeout(function() {
          cartModal.hide();
        }, 1000);
      } else {
        heartIcon.classList.remove("clicked");
        cartModal.show();
        heartIcon.setAttribute("data-tooltip", "Ajouter au Panier"); // Restore tooltip text
        setTimeout(function() {
          cartModal.hide();
        }, 1000);
      }
    });
  });

  document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-button');
    const productList = document.querySelector('.product-list');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const criteria = button.getAttribute('data-criteria');
            filterProducts(criteria);
        });
    });

    function filterProducts(criteria) {
        fetch(`/filter-products/${criteria}`, {
            method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            productList.innerHTML = '';

            data.forEach(product => {
                const productItem = document.createElement('li');
                productItem.textContent = product.productTitle;
                productList.appendChild(productItem);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
  });
});