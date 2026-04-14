
let query;

$(function () {
    $("#search-box").show();

    const $items = $(".item-wrapper");
    const $sections = $(".my-items, .other-items");
    const $titles = $("h1");

    $("#search").on("keyup", function () {

        query = $(this).val().toLowerCase().trim();
        let visibleCount = 0;     

        // reset 
        $items.show();
        $sections.show();
        $titles.show();
        $("#no-result").hide();

        // filtre
        $items.each(function () {
            let text = $(this).text().toLowerCase();

            if (text.includes(query)) {
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        // cache les sections vide
        $sections.each(function () {
            let visibleItems = $(this).find(".item-wrapper:visible").length;

            if (visibleItems === 0) {
                $(this).prev("h1").hide();
            }
        });

        // aucun résultat global
        if (visibleCount === 0) {
            $("#no-result").show();
        }
    });

    });
