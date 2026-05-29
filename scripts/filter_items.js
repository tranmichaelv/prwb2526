let items;
let query;

$(function () {
    $("#filters-box").show();
    


    $(window).on("pageshow", function () {
       filterItems();
    });

    $("#search").on("input", filterItems);
    $("#category-filter").on("change", filterItems);
        
        
        
async function filterItems() {

        try {
            const search_query = {
                query: $("#search").val(),
                category_id: $("#category-filter").val() || 0
            };

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
    }


async function displayBrowseItems(){
    let html = "";
    console.log(items);
    if (user !== "") {
        if (items.bids && items.bids.length > 0) {
            html += "<h1>Items I'm Participating in</h1>" +
            '<div class="items-box">';
    
            for(let item of items.bids) {
                html += renderItem(item);
            }

            html += '</div>'
        }

        // Autres items
        if (items.other && items.other.length > 0) {
                html += "<h1>Other Available Items</h1>" +
                        '<div class="items-box">';
                
                for(let item of items.other) {
                    html += renderItem(item);
                }
        
                html += '</div>'  
        }

    } else {
        if (items.all && items.all.length > 0) {
            for(let item of items.all) {
                html += renderItem(item);
            }
        }
    }

    $("#items-container").html(html);
    
}

function displayMyItems(){
    let html = "";

    console.log(items);
    if (items.bids && items.bids.length > 0) {
        html += "<h1>Active Items</h1>" +
                '<div class="items-box">';
        
        for(let item of items.bids) {
            html += renderItem(item);
        }

        html += '</div>'

    }

    if (items.other && items.other.length > 0) {
        html += "<h1>Closed Unsold Items</h1>" +
                '<div class="items-box">';
        
        for(let item of items.other) {
            html += renderItem(item);
        }
         html += '</div>'
    }

    if (items.all && items.all.length > 0) {
        html += "<h1>Sold Items</h1>" +
                '<div class="items-box">';
        
        for(let item of items.all) {
            html += renderItem(item);
        }
         html += '</div>'
    }

    $("#items-container").html(html);
    
}

function renderItem(item) {

    let labels = "";

    if (item.is_auction && item.has_buy_now) {

        labels += `
            <span class="labels label-buy-now-and-more">
                <i class="bi bi-bag"></i> Buy Now
            </span>

            <span class="labels label-auction-and-more">
                <i class="bi bi-piggy-bank"></i> Auction
            </span>
        `;

    } else if (item.is_direct_sale) {

        labels += `
            <span class="labels label-buy-now">
                <i class="bi bi-bag"></i> Buy Now
            </span>
        `;

    } else {

        labels += `
            <span class="labels label-auction">
                <i class="bi bi-piggy-bank"></i> Auction
            </span>
        `;
    }

    let bidderLabel = "";

    if (user !== "") {

    
        if (item.highest_bidder_id == currentUserId) {

            bidderLabel = `
                <span class="highest_bidder">
                    <i class="bi bi-trophy"></i> Highest Bidder
                </span>
            `;

        } else if (item.is_bidder ) {

            bidderLabel = `
                <span class="bidder">
                    Bidder
                </span>
            `;
        }
    
    }

    let picturesLabel = "";

    if (item.number_of_pics > 1) {

        picturesLabel = `
            <div class="labels-number-of-pictures">
                <i class="bi bi-images"></i>
                ${item.number_of_pics} images
            </div>
        `;
    }

    let priceSection = "";

    if (item.is_auction && item.has_buy_now) {

        priceSection = `
            <p class="price-buy-now">
                € ${item.buy_now_price}
            </p>
        `;

        if (item.has_bids) {

            priceSection += `
                <div class="label-current-bid">
                    <p>Current bid</p>
                    <p class="current-bid">
                        € ${item.max_bid}
                    </p>
                </div>
            `;
        }

    } else if (item.is_direct_sale) {

        priceSection = `
            <p class="price-buy-now">
                € ${item.buy_now_price}
            </p>
        `;

    } else {

        priceSection = `
            <p class="price-auction">
                € ${item.starting_bid}
            </p>
        `;

        if (item.has_bids) {

            priceSection += `
                <div class="label-current-bid">
                    <p>Current bid</p>
                    <p class="current-bid">
                        € ${item.max_bid}
                    </p>
                </div>
            `;
        }
    }

    let timeLeft = "";

    if (
        (item.time[0] <= 0 && item.time[1] <= 0 && item.time[2] <= 0 && item.time[3] <= 0)  ||
        (item.is_auction && item.buy_now_reached) ||
        (item.is_direct_sale && !item.not_purchased_direct_sale)
    ) {

        timeLeft = `<p>closed</p>`;

    } else if (item.time[0] < 1 && item.time[1] < 1) {

        timeLeft = `
            <p>
                ${Math.max(0, item.time[2])}m
                ${Math.max(0, item.time[3])}s left
            </p>
        `;

    } else if (item.time[0] < 1) {

        timeLeft = `
            <p>
                ${Math.max(0, item.time[1])}h
                ${Math.max(0, item.time[2])}m left
            </p>
        `;

    } else {

        timeLeft = `
            <p>
                ${Math.max(0, item.time[0])}d
                ${Math.max(0, item.time[1])}h left
            </p>
        `;
    }

    let purchaseSection = "";

    if (page === "purchases" || page === "sales") {

        purchaseSection = `
            <div class="price-and-closeTime">

                <p>
                    <i class="bi bi-tag"></i>
                    Paid € ${item.max_bid}
                </p>

                <p>
                    <i class="bi bi-clock"></i>
                    Closed on
                    ${item.buy_now_reached
                        ? item.date_buy_now
                        : item.end_at}
                </p>

            </div>
        `;
    }

    return `
        <div class="item-wrapper">

            <div class="item-card">

                <a href="item/open/${item.id}/${from}${items.encoded ? '/' + items.encoded : ''}">

                    <div class="image-container">

                        ${
                            item.first_picture_path
                            ? `
                                <img
                                    src="${item.thumbnail}"
                                    alt="${item.title}"
                                    width="300"
                                >
                            `
                            : `
                                <svg width="300"
                                     height="200"
                                     style="background:#f5f5f5; border:1px">

                                    <text x="50%"
                                          y="50%"
                                          dominant-baseline="middle"
                                          text-anchor="middle"
                                          fill="#aaa"
                                          font-size="14">

                                        Pas d'image

                                    </text>

                                </svg>
                            `
                        }

                        ${labels}

                        ${bidderLabel}

                        ${picturesLabel}

                    </div>

                    <div class="item-info">

                        <p class="item-title">
                            ${item.title}
                        </p>

                        <p class="by">
                            by ${item.owner_name}
                        </p>

                        <div class="price-labels">
                            ${priceSection}
                        </div>

                        <div class="time-left">
                            ${timeLeft}
                        </div>

                    </div>

                </a>

            </div>

            ${purchaseSection}

        </div>
    `;
}
});