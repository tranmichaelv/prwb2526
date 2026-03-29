$(() => {
    loadPictures();
});

// va chercher les images via ajax + affichage
async function loadPictures() {
    const page = $("#open-item-page");

    const itemId = page.data("item-id");

    try {
        const data = await $.ajax({
          url: "item/get_item_pictures_service/" + itemId,
          method: "GET",
          dataType: "json"
    });

         buildCarousel(data.pictures);

        $("#item-gallery-nojs").hide();
        $("#item-thumbnails-nojs").hide();

        $("#item-gallery-js").show();
        $("#item-thumbnails-js").show();
        
    } catch (e) {
        console.error("Error loading pictures");
    }
}

// transforme les données json en html du carousel/vignettes
function buildCarousel(pictures) {
    let carouselItemsHtml = "";
    let thumbnailsHtml = "";

    for(let i = 0; i < pictures.length; i++) {
        const picture = pictures[i];
        const activeClass = i === 0 ? "active" : "";

        // images du carousel
        carouselItemsHtml +=
            '<div class="carousel-item ' + activeClass + '">' +
                '<img src="' + picture.src + '" class="d-block w-100" alt="Item picture ' + (i + 1) + '">' +
            '</div>';

        // vignettes sous le carousel    
       thumbnailsHtml +=
            '<img ' +
                'src="' + picture.thumbnail_src + '" ' +
                'class="thumbnail js-thumbnail ' + (activeClass ? 'active-thumb' : '') + '" ' +
                'data-index="' + i + '" ' +
                'alt="Thumbnail ' + (i + 1) + '"' +
            '>';

    }

    // crée le carousel complet
    const carouselHtml =
        '<div id="itemCarousel" class="carousel slide">' +
            '<div class="carousel-inner">' +
                carouselItemsHtml +
            '</div>' +

            '<button class="carousel-control-prev" type="button" data-bs-target="#itemCarousel" data-bs-slide="prev">' +
                '<span class="carousel-control-prev-icon" aria-hidden="true"></span>' +
                '<span class="visually-hidden">Previous</span>' +
            '</button>' +

            '<button class="carousel-control-next" type="button" data-bs-target="#itemCarousel" data-bs-slide="next">' +
                '<span class="carousel-control-next-icon" aria-hidden="true"></span>' +
                '<span class="visually-hidden">Next</span>' +
            '</button>' +

        '</div>';

    // insère carousel + vignette dans open_item

    $("#item-gallery-js").html(carouselHtml);
    $("#item-thumbnails-js").html(thumbnailsHtml);

    bindCarouselEvents();

}

// vignette + synchro des flèches (carousel)
function bindCarouselEvents() {
    const carouselElement = document.getElementById("itemCarousel");
    const carousel = new bootstrap.Carousel(carouselElement, {
        interval: false
    });

    $("#item-thumbnails-js").on("click", ".js-thumbnail", function () {
        const index = $(this).data("index");

        carousel.to(index);

        $(".js-thumbnail").removeClass("active-thumb");
        $(this).addClass("active-thumb");
    });

    $("#itemCarousel").on("slid.bs.carousel", function (event) {
        $(".js-thumbnail").removeClass("active-thumb");
        $(`.js-thumbnail[data-index="${event.to}"]`).addClass("active-thumb");
    });
}
