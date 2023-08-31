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
});