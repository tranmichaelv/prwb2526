// Walid's version

let pictures = [];
let item_id;
let pictureToDeletePriority = null;

document.onreadystatechange = async function () {

    if (document.readyState == 'complete') {

        item_id = document.getElementById("item_id").value;

        document.getElementById("confirm-delete-picture").addEventListener("click", async function () {
       
            if (pictureToDeletePriority === null) return;

            await $.ajax({
                url: "item/remove_picture_service",
                method: "POST", 
                data: {
                    param1: item_id,
                    param2: pictureToDeletePriority
                },
                dataType: "json"
            });

            pictureToDeletePriority = null;

            bootstrap.Modal.getInstance(document.getElementById("deletePictureModal")).hide();

            await loadPictures();
        });

        await loadPictures();
    }
}

function getPictures() {
        return pictures;
}



function renderPictures() {
    const container = document.getElementById("pictures-container");
    const template = document.getElementById("picture-template");

    container.innerHTML = "";

    getPictures().forEach(function (picture, index) {
        const clone = template.content.cloneNode(true);

        const img = clone.querySelector("img");
        img.src = picture.src;

        const priority = index + 1;
        const thumb = clone.querySelector(".thumb");
        thumb.dataset.priority = priority;

        const leftButton = clone.querySelector(".move-left");
        const rightButton = clone.querySelector(".move-right");
        const deleteButton = clone.querySelector(".move-delete");
        

        leftButton.disabled = index === 0;
        rightButton.disabled = index === getPictures().length - 1;

        deleteButton.dataset.priority = priority;
        leftButton.dataset.priority = priority;
        rightButton.dataset.priority = priority;

        leftButton.addEventListener("click", async function () {
           await movePicture(priority, "left"); 
        });

        rightButton.addEventListener("click", async function () {
           await movePicture(priority, "right");
        });

        deleteButton.addEventListener("click", async function () {
            pictureToDeletePriority = priority;

            const modalElement = document.getElementById("deletePictureModal");
            const modal = new bootstrap.Modal(modalElement);

            modal.show();
        })
        container.appendChild(clone);
    });

    activateSortable();
}

async function movePicture(priority, direction) {
    await $.ajax({
        url: "item/move_picture_service",
        method: "POST", 
        data: {
            param1: item_id,
            param2: priority, 
            param3: direction
        }, 
        dataType: "json"
    });

    await loadPictures();
}

async function loadPictures() {
    const data = await $.ajax({
            url : "item/get_item_pictures_service/" + item_id, 
            method : "GET",
            dataType : "json"
        });
        pictures = data.pictures;
        renderPictures();
}

