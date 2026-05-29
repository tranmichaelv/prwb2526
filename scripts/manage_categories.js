let categories = [];
let deleteCategoryId;
let deleteCategoryModal;
let config;

$(async function () {
    await loadConfig();

    $("#categories-nojs").hide();
    $("#categories-js").show();

    deleteCategoryModal = new bootstrap.Modal(
        document.getElementById("deleteCategoryModal")
    );

    await loadCategories();

    $("#show-add-category").on("click", showAddInput);
    $("#cancel-add-category").on("mousedown", cancelAddInput);
    $("#new-category-name").on("blur", addCategory);

    $("#confirm-delete-category").on("click", deleteCategory);
});

async function loadConfig() {
    config = await fetch("category/get_validation_config_service")
        .then(r => r.json());
}

async function loadCategories() {
    const data = await $.ajax({
                url : "category/get_categories_service",
                method : "GET",
                dataType : "json"
            });
    categories = data.categories;
    showCategories();        
}

function showCategories() {
    let html = "";

    categories.forEach(category => {
        html += ` 
                <div class="category-row-js" data-id="${category.id}">
                    <span class="drag-handle">⋮⋮</span>

                    <div class="category-name-box">
                        <span class="category-name">${category.name}</span>
                        <span class="category-count">(${category.count})</span>
                        <small class="category-error"></small>
                    </div>

                    <button type="button" class="btn btn-secondary btn-sm edit-category"> Edit </button>
                        
                    

                    <button type="button" class="btn btn-delete btn-sm delete-category"
                        ${category.can_delete ? "" : "disabled"}>
                        Delete
                    </button>
                 </div>
        
                `;
    })

    $("#categories-list").html(html);
    $(".edit-category").on("click", function () {
        const row = $(this).closest(".category-row-js");
        editCategory(row);
    });

    $(".delete-category").on("click", function () {
        const row = $(this).closest(".category-row-js");
        const id = row.data("id");
        const name = row.find(".category-name").text();

        deleteCategoryId = id;
        $("#deleteCategoryModalBody").text(`Delete category "${name}" ?`);
        deleteCategoryModal.show();
    });

    enableDragAndDrop();
    
}

function checkCategoryName(name, errName) {
    errName.html("");

    if(name.length < config.category_min_length || name.length > config.category_max_length) {
        errName.html("<p> Category length must be between " + config.category_min_length + " and " + config.category_max_length + " characters. </p>")
        return false;
    }
    return true;
}



function showAddInput() {
    $("#add-category-row").show();
    $("#show-add-category").hide();

    $("#new-category-name").val("");
    $("#add-category-error").html("");
    $("#new-category-name").focus();
}

function cancelAddInput(e) {
    if (e) {
        e.preventDefault();
    }

    $("#add-category-row").hide();
    $("#show-add-category").show();

    $("#new-category-name").val("");
    $("#add-category-error").html("");
}

async function addCategory() {
    const name = $("#new-category-name").val().trim();
    const errorBox = $("#add-category-error");

    errorBox.html("");

    if(name === "") return;

    if(!checkCategoryName(name,errorBox)) return;

    const data = await $.post("category/add_category_service/",  {param1 :name }, null , "json" );

    if(!data.success) {
        errorBox.html("<p>" + data.errors.category_name[0] + "</p>")
        return;
    }

    categories = data.categories;

    cancelAddInput();
    showCategories();
}

function editCategory(row) {
    const id = row.data("id");
    const oldName = row.find(".category-name").text();

    row.find(".category-name-box").html(`
        <input type="text" class="edit-category-input" value="${oldName}">
        <small class="category-error"></small>
    `);

    const input = row.find(".edit-category-input");
    const errorBox = row.find(".category-error");

    input.focus();
    input.select();

    input.on("blur", async function () {
        const newName = input.val().trim();

        if (newName === oldName) {
            showCategories();
            return;
        }

        if (!checkCategoryName(newName, errorBox)) {
            return;
        }

        const data = await $.post("category/update_name_category_service", { param1: id, param2: newName }, null, "json");
             
        if (!data.success) {
            errorBox.html("<p>" + data.errors.category_name[0] + "</p>");
            return;
        }

        categories = data.categories;
        showCategories();
    });

    input.on("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            input.blur();
        }

        if (e.key === "Escape") {
            showCategories();
        }
    });
}

async function deleteCategory() {
    if (deleteCategoryId === null || deleteCategoryId === undefined) {
        return;
    }

    const data = await $.post("category/delete_category_service",{ param1: deleteCategoryId },null, "json");
        
        
    
    deleteCategoryModal.hide();

    if (!data.success) {
        alert(data.errors);
        return;
    }

    deleteCategoryId = null;
    categories = data.categories;
    showCategories();
}

function enableDragAndDrop() {
    $("#categories-list").sortable({
        handle: ".drag-handle",
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true,

        update: async function () {
            let ids = [];

            $("#categories-list .category-row-js").each(function () {
                ids.push($(this).data("id"));
            });

            await $.post(
                "category/update_categories_order_service",
                { param1: ids },
                null,
                "json"
            );
        }
    });
}


