
<div> 
    <div class="d-flex flex-column bg-dark mb-3 gap-2 w-150 p-3 rounded">
        <div class="mb-2 text-secondary" >Manage your Item </div>
        <a href="item/edit/<?= $item->getId() ?>" class="btn btn-outline-secondary "> <i class="bi bi-pencil me-2"></i>Edit Item Details </a>
        <a href="item/manage_images/<?= $item->getId()  ?>" class="btn btn-outline-info "> <i class="bi bi-image me-2"></i>Manage Images </a>
        <a href="item/delete/<?= $item->getId()  ?>" class="btn btn-outline-danger"><i class="bi bi-trash me-2"></i>Delete item </a>
    </div>
</div>