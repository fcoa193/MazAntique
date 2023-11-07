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
  // Select all elements with the class "add-to-like-button"
  const likeButtons = document.querySelectorAll(".add-to-like-button");

  // Loop through each "add-to-like-button" element
  likeButtons.forEach((button) => {
    // Find the heart icon within the button
    const heartIcon = button.querySelector(".card_heart");
    
    // Add a click event listener to the button
    button.addEventListener("click", function (event) {
      event.preventDefault();

      // Check if the heart icon is currently clicked (liked)
      if (!heartIcon.classList.contains("clicked")) {
        // If not liked, add the "clicked" class and change the tooltip
        heartIcon.classList.add("clicked");
        heartIcon.setAttribute("data-tooltip", "Je n'aime plus ce produit.");

        // Get the product ID from the data attribute
        const productId = heartIcon.getAttribute("data-product-id");

        // Send a POST request to like the product
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
        // If already liked, remove the "clicked" class and change the tooltip
        heartIcon.classList.remove("clicked");
        heartIcon.setAttribute("data-tooltip", "J'aime ce produit!");

        // Get the product ID from the data attribute
        const productId = heartIcon.getAttribute("data-product-id");

        // Send a POST request to unlike the product
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



