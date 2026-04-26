let items;
let query;

$(function () {
    $("#search-box").show();


    $(window).on("pageshow", function () {
        $("#search").trigger("input");
    });

    $("#search").on("input", async function () {

        try {
            const search_query = {query: $("#search").val()};

            const url = page === "browse"
                ? "item/get_filtered_items_browse"
                : "item/get_filtered_items_list";

          
                items = await $.ajax({
                    url,
                    type: "POST",
                    data: search_query,
                    dataType: "json"
                });

                // console.log(items);

                $("#no-result").toggle(
                    !(items.bids.length || items.other.length || items.all.length)
                );
    
                if (page === "browse") {
                    displayBrowseItems();
                } else {
                    displayMyItems();
                }

        } catch (error) {
            console.error("AJAX ERROR:", error);
            console.error("STATUS:", error.status);
            console.error("RESPONSE:", error.responseText);
            $("#items-container").html("Error encountered while retrieving the messages!");
        }
    });
});

async function displayBrowseItems(){
    let html = "";
    if (user !== "") {
        if (items.bids && items.bids.length > 0) {
            html += await renderCategory("Items I'm Participating in", items.bids);
        }

        // Autres items
        if (items.other && items.other.length > 0) {
            html += await renderCategory("Other Available Items", items.other);
        }

    } else {
        if (items.all && items.all.length > 0) {
            html += await renderCategory("Available Items", items.all);
        }
    }

    $("#items-container").html(html);
    
}

async function displayMyItems(){
    let html = "";

    if (items.bids && items.bids.length > 0) {
        html += await renderCategory("Active Items", items.bids);
    }

    if (items.other && items.other.length > 0) {
        html += await renderCategory("Closed Unsold Items", items.other);
    }

    if (items.all && items.all.length > 0) {
        html += await renderCategory("Sold Items", items.all);
    }

    $("#items-container").html(html);
    
}

async function renderCategory(title, itemsList) {

    let html = "";

    html += '<div class="category-items">' +
            `<h1>${title}</h1>` +
            '<div class="items-box">';

    const rendered = await $.post("item/render_items", {
        items: itemsList,
        encoded: items.encoded,
        page: page
    });

    // console.log(rendered);

    html += rendered;

    html += `</div></div>`;

    return html;
}
