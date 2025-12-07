<!DOCTYPE html>
<html lang ="en">

<head>
    <meta charset="UTF-8">
    <title>Add Item</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/add_edit_item.css">
</head>
    
<body>
    <div class = "container">

        <h1>Add Item</h1>

     <form method="post" action="item/add">
         
    <div class ="card basic-info">
            <h2>Basic information</h2>
            
         <div class="form-group"> 
            <label>Item Title*</label>
            <input type="text" name ="title" placeholder="Ex: iphone 13 Pro Max 256GB">
         </div>    
         <div class="form-group">            
            <label>Description</label>
            <textarea name ="description" rows="5" placeholder="Describe your item in detail..."></textarea>
         </div> 
         <div class="form-group">                
            <label>Sale Duration(days)*</label>
            <input type="number" name ="duration" value="7">
          </div>  
        </div>

        <div class ="card sale-type">
            <h2>Sale Type</h2>

            <div class="card sale-option">
                <h3> Option 1 : Auction</h3>

              <div class="form-group">  
                <label>Starting bid</label>
                <input type = "number" name="bid"  placeholder="e.g., 50.00">
              </div>     
              
              <div class="form-group"> 
                <label>Instant purchase price (optional)</label>
                <input type= "number" name ="buy_now" placeholder="e.g., 200.00">
              </div>  
            </div>

            <div class="card sale-option">
                <h3>Option 2 : Direct sale</h3>

              <div class="form-group"> 
                <label>Sale Price</label>
                <input type ="number" name="sale_price" placeholder="e.g., 150.00">
              </div>  
            </div>
        </div>    
    </form>        
    </div>
</body>
</html>
