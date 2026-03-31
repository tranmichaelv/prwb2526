// char >= 3 and char <= 255 
        // unicity (cannot have two items with same name)
        // function's parameters : title, owner, item_id
let title_exists;

let title_min_length;
let $title_max_length;
let description_min_length;
let duration_days_min; 
let duration_days_max;

let title;
let err_title;
let description;
let err_description;
let sale_duration;
let err_sale_duration;
let starting_bid;
let err_starting_bid;
let instant_purchase_price;
let err_instant_purchase_price;
let sale_price;
let err_sale_price;

document.onreadystatechange = function () {
    if (document.readyState == 'complete') {
        let VALIDATION_CONSTANTS;
        
        async function loadConstants() {
            const data = await $.ajax({
                url : "item/item_validation_constants_service",
                method : "GET",
                dataType : "json"
            });
            VALIDATION_CONSTANTS = data.constants;
        }

        async function title_exists() {
            const response = await $.ajax({
                url : "item/validate_title_service", 
                method : "GET",
                dataType : "json"
            });
            title_exists = response.exists;
        }
        title_min_length = VALIDATION_CONSTANTS.title_min_length;
        $title_max_length = VALIDATION_CONSTANTS.title_max_length;
        description_min_length = VALIDATION_CONSTANTS.description_min_length;
        duration_days_min = VALIDATION_CONSTANTS.duration_days_min; 
        duration_days_max = VALIDATION_CONSTANTS.duration_days_ml
        title = document.getElementById("item_title");
        err_title = document.getElementById("err_title");
        description = document.getElementById("item_description");
        err_description = document.getElementById("err_description");
        sale_duration = document.getElementById("sale_duration");
        err_sale_duration = document.getElementById("err_sale_duratiole");
        starting_bid = document.getElementById("starting_bid");
        err_starting_bid = document.getElementById("err_starting_bid");
        instant_purchase_price = document.getElementById("instant_purchase_price");
        err_instant_purchase_price = document.getElementById("err_instant_purchase_price");
        sale_price = document.getElementById("sale_price");
        err_sale_price = document.getElementById("err_sale_price");

    }

    function check_title() {
        let valid = true;
        err_title.innerHTML = "";
        if (title < title_min_length || title > title_max_length) {
            innerHTML = "Title lenght must be between" + title_min_length + "and" + title_max_length;
        }

        
    }
}

