// char >= 3 and char <= 255 
        // unicity (cannot have two items with same name)
        // function's parameters : title, owner, item_id

let current_item_id;

//========= Constantes=========//
let title_min_length;
let title_max_length;
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
let raw_id;

document.onreadystatechange = async function () {
    if (document.readyState == 'complete') {
        
            const data = await $.ajax({
                url : "item/item_validation_constants_service",
                method : "GET",
                dataType : "json"
            });
        title_min_length = data.title_min_length;
        title_max_length = data.title_max_length;
        description_min_length = data.description_min_length;
        duration_days_min = data.duration_days_min; 
        duration_days_max = data.duration_days_max;

        raw_id = document.getElementById("item_id").value;
        current_item_id = raw_id === "" || raw_id === "0" ? null : raw_id;
        title = document.getElementById("item_title");
        err_title = document.getElementById("err_title");
        description = document.getElementById("item_description");
        err_description = document.getElementById("err_description");
        sale_duration = document.getElementById("sale_duration");
        err_sale_duration = document.getElementById("err_sale_duration");
        starting_bid = document.getElementById("starting_bid");
        err_starting_bid = document.getElementById("err_starting_bid");
        instant_purchase_price = document.getElementById("instant_purchase_price");
        err_instant_purchase_price = document.getElementById("err_instant_purchase_price");
        sale_price = document.getElementById("sale_price");
        err_sale_price = document.getElementById("err_sale_price");

    }

    
}

async function check_title() {

    let value = title.value;
    err_title.innerHTML = "";

        if (value.length < title_min_length || value.length > title_max_length) {
            err_title.innerHTML = "Title length must be between " + title_min_length + " and " + title_max_length + " characters. ";
            setInvalid(title);
            return false;
        } 
      
    try {
        const response = await $.ajax({
        url : "item/validate_title_service", 
        method : "POST",
        data : {
            title: title.value, 
            id: current_item_id 
        },
        dataType : "json"
    });

    if (response.exists_already) {
        err_title.innerHTML = "Your already have an item with this title.";
        setInvalid(title);
        return false;
    } 
    } catch (err) {
        console.error("Erreur AJAX", err);
        return false;    
    }
   
    setValid(title);
    return true;
    
}


function check_description() {
    let value = description.value;
    err_description.innerHTML = "";

    if (value === "") {
        return true;
    }
    if (value.length < description_min_length) {
        err_description.innerHTML = "Description length must be at least " + description_min_length +  " characters.";
        return false;
    }

}

function check_sale_duration() {
    let value = sale_duration.value;
    err_sale_duration.innerHTML = "";

    if (value !== "") {
        value = parseInt(value);
    } 
    if (value < duration_days_min || value > duration_days_max) {
        err_sale_duration.innerHTML = "Duration days must be between" + duration_days_min + " and " + duration_days_max;
        return false;
    }

}


function check_sale() {

    let starting_bid_value = starting_bid.value;
    let instant_purchase_price_value = instant_purchase_price.value;
    let sale_price_value = sale_price.value;

    err_starting_bid.innerHTML = "";
    err_instant_purchase_price.innerHTML = "";
    err_sale_price.innerHTML = "";

    starting_bid_value = starting_bid_value === "" ? null : parseInt(starting_bid_value);
    instant_purchase_price_value = instant_purchase_price_value === "" ? null : parseInt(instant_purchase_price_value);
    sale_price_value = sale_price_value === "" ? null : parseInt(sale_price_value);
    
    // si starting_bid remplie direct_sale doit être vide 
    // si starting bid remplie instante_purchase peut être vide, 
        // mais si je saisie une valeur elle doit etre sup a starting_bid

    // si sale_price remplie starting bid doit être vide
        // si instant_purchase remplie et sale_price remplie je prend sale_price 
        // donc si sale_price remplie starting_bid doit etre vide peu importe l'état de instant_purchase 
        
    if (starting_bid_value !== null) {
    
        if (sale_price_value !== null) {
            err_starting_bid.innerHTML = "Cannot create both auction and direct sale.";
            err_sale_price.innerHTML = "Cannot create both auction and direct sale.";
            // setInvalid();
            return false;
        }

        if (instant_purchase_price_value === null) {
            return true;
        } else {
            if (instant_purchase_price_value <= starting_bid_value) {
                err_instant_purchase_price.innerHTML = "Buy now price must be strictly higher than the starting bid";
                return false;
            }
        }
    }

    if (sale_price_value !== null) {

        if (starting_bid_value !== null) {
            err_starting_bid.innerHTML = "Cannot create both auction and direct sale.";
            err_sale_price.innerHTML = "Cannot create both auction and direct sale.";
            return false;
        }

        if (instant_purchase_price_value !== null) {
            err_instant_purchase_price.innerHTML = "Cannot have both instant purchase and direct sale";
            err_sale_price.innerHTML = "Cannot have both instant purchase and direct sale";
            return false;
        }

        return true;
    } 

    return true;
   
}

function setInvalid(intput, message, errorEl)  {
    intput.classList.add("is-invalid");
    intput.classList.remove("is-valid");
    errorEl.context = message;
}

function setValid(input, errorEl) {
    input.classList.remove("is-invalid");
    input.classList.add("is-valid");
    errorEl.context = "";
}

