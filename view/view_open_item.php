<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/prwb_2526_c05/css/OpenItem.css" >
    <title>Item images</title>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="main-picture d-flex flex-column align-items-start text-start">
            <?php 
                $baseUrl = '/prwb_2526_c05/'; 
                $src = $baseUrl . $main_picture;
            ?>

            <div class="image"> <img src="<?= $src ?>" alt="Image principale de Item" class="img-fluid mb-3" style="max-width: 800px;"> </div>
            <p class="title mt-3 px-2">
                <?php echo $item->title ; ?>
            </p> 
            <div class="description w-50  mt-3 px-2" >
                <p><?php echo $item->description; ?></p>
            </div>
            <div class="startEndBid  mt-3 px-2" >
                <p> start : <?php echo $item->created_at ?></p>
                <p> End : <?php echo $item->end_at ?></p>
            </div>   
        </div>
    </div>
</body>
</html>

