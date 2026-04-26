<!DOCTYPE html>
<html lang ="en">

<head>
    <meta charset="UTF-8">
    <title>Add Item</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/add_edit_item.css">
    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script src="scripts/validation_item.js" type="text/javascript"></script>
    <!-- <script src="scripts/friends_vjs.js" type="text/javascript"></script> -->
</head>
    
<body class="page-add-edit-item">
    

      <form id="itemForm" method="post" action="<?= $mode === "edit" ? "item/edit/$id" : "item/add" ?>">

       <?php
          
            $page = $mode === "edit" ? "edit_item" : "add_item";
            $title = $mode === "edit" ? "Edit item" : "Add item";
            $page_before = $mode === "edit" ? "item/open/$id/from-items" . ($encoded ? "/" . $encoded : ""): "item/browse";

            include("header.php");
        ?>
   <div class = "container">
   
    <div class ="card basic-info">
            <h2>Basic information</h2>
            
         <div class="form-group"> 
            <label>Item Title*</label>
            <input type="hidden" value="<?= $item->get_id()?>" id="item_id">
            <input type="text" name ="title"
                class="<?= !empty($errors['title'] ?? []) ? 'input-error' : '' ?>"
                placeholder="Ex: iphone 13 Pro Max 256GB"
                value="<?= $item->get_title() ?>" 
                id = "item_title"  
                oninput="check_title()">

            <small class="field-errors-js" id="err_title"></small>
             <?php if (!empty($errors['title'] ?? [])): ?>
                  <ul class="field-errors">
                      <?php foreach ($errors['title'] as $e): ?>
                          <li><?= $e ?></li>
                      <?php endforeach; ?>
                  </ul>
             <?php endif; ?>
         </div>    
         <div class="form-group">            
            <label>Description</label>
            <textarea name="description"
                class="<?= !empty($errors['description'] ?? []) ? 'input-error' : '' ?>"
                rows="5"
                placeholder="Describe your item in detail..." 
                id = "item_description" 
                oninput="check_description()"><?= $item->get_description() ?></textarea>
                <small class="field-errors-js" id="err_description"></small>
                <?php if (!empty($errors['description'] ?? [])): ?>
                  <ul class="field-errors">
                      <?php foreach ($errors['description'] as $e): ?>
                          <li><?= $e ?></li>
                      <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
         <div class="form-group">                
            <label>Sale Duration(days)*</label>
            <input type="number" name ="duration"
              class="<?= !empty($errors['duration_days'] ?? []) ? 'input-error' : '' ?>"          
              value="<?= $item->get_duration_days() ?>"
              id="sale_duration"
              oninput="check_sale_duration()">
              <small class="field-errors-js" id="err_sale_duration"></small>
             <?php if (!empty($errors['duration_days'] ?? [])): ?>
                <ul class="field-errors">
                    <?php foreach ($errors['duration_days'] as $e): ?>
                        <li><?= $e ?></li>
                    <?php endforeach; ?>
                </ul>
             <?php endif; ?> 
          </div>  
        </div>

        <div class ="card sale-type">
            <h2>Sale Type</h2>

            <div class="card sale-option">
                <h3> Option 1 : Auction</h3>

              <div class="form-group">  
                <label>Starting bid</label>
                <input type = "number" name="bid"
                  class="<?= !empty($errors['starting_bid'] ?? []) ? 'input-error' : '' ?>"
                  placeholder="e.g., 50.00"
                  value="<?= $item->get_starting_bid() > 0 ? $item->get_starting_bid() : '' ?>"
                  id="starting_bid"
                  oninput="check_sale()">
                  <small class="field-errors-js" id="err_starting_bid"></small>
                 <?php if (!empty($errors['starting_bid'] ?? [])): ?>
                    <ul class="field-errors">
                        <?php foreach ($errors['starting_bid'] as $e): ?>
                            <li><?= $e ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>     
                </div>     
              
              <div class="form-group"> 
                <label>Instant purchase price (optional)</label>
                <input type= "number" name ="buy_now"
                 class="<?= !empty($errors['buy_now_price'] ?? []) ? 'input-error' : '' ?>"
                 placeholder="e.g., 200.00"
                value="<?= $item->get_instant_purchase() !== null ? $item->get_instant_purchase() : '' ?>"
                id="instant_purchase_price"
                oninput="check_sale()">
                <small class="field-errors-js" id="err_instant_purchase_price"></small>
              <?php if (!empty($errors['buy_now_price'] ?? [])): ?>
                  <ul class="field-errors">
                      <?php foreach ($errors['buy_now_price'] as $e): ?>
                          <li><?= $e ?></li>
                      <?php endforeach; ?>
                  </ul>
              <?php endif; ?>              
              </div>  
            </div>

            <div class="card sale-option">
                <h3>Option 2 : Direct sale</h3>

              <div class="form-group"> 
                <label>Sale Price</label>
                <input type ="number" name="sale_price" placeholder="e.g., 150.00"
                value="<?= $item->get_sale_price() !== null ? $item->get_sale_price() : '' ?>"
                id="sale_price" 
                oninput="check_sale()">
                <small class="field-errors-js" id="err_sale_price"></small>
                <?php if (!empty($errors['sale_price'] ?? [])): ?>
                  <ul class="field-errors">
                      <?php foreach ($errors['sale_price'] as $e): ?>
                          <li><?= $e ?></li>
                      <?php endforeach; ?>
                  </ul>
             <?php endif; ?>
              </div>  
            </div>
        </div>   
    </form>        
    </div>
         <div class="modal fade" id="leaveModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">

                <div class="modal-header">
                    <h1 class="modal-title fs-5">Unsaved changes</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

            <div class="modal-body">
                You have unsaved changes. Leave anyway?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button" class="btn btn-primary" id="confirmLeave">
                    Leave
                </button>
            </div>

            </div>
        </div>
    </div>               
        <?php include('footer.php'); ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="scripts/popup.js" type="text/javascript"></script>
</body>
</html>
