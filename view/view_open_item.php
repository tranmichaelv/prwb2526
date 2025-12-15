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


    <!-- Un container de toute la page open item -->
<div class="container d-flex flex-row align-items-start gap-4 mb-2">
        
<!-- Right-container -->
    <div class="left-container d-flex flex-column gap-4 flex-grow-1">

        <section class="item-content">
            <!-- main-picture-container -->
            <div class="main-picture">
                <?php 
                    $baseUrl = '/prwb_2526_c05/';
                    $src = $baseUrl . $main_picture;
                ?>

                <img src="<?= $src ?>" alt="Image principale de Item">
            </div>

            <div class="item-infos">
                <p>
                    <h2> <?php echo $item->title; ?></h2>
                </p>
                <p> <?php echo $item->description; ?></p>

                <div class="startEndBid">
                    <p> start : <?php echo $item->created_at ?></p>
                    <p> End : <?php echo $item->end_at ?></p>
                </div>
            </div>
        </section>
        

        <section>
            <!-- additional-images-container -->
            <div>
                

            </div>


        </section>



        <section>
            <!-- bid-history-container -->
            <div>
                
            
            </div>

        </section>

    </div>



<!-- left-container -->
    <div class="right-container d-flex flex-column gap-4 ">

        <section>
            <!-- pricing-container -->
            <div>
                <?php 

                ?>
                <p> Pricing </p>

            </div>

        </section>
        

        <section>
            <!-- Seller-info-container -->
            <div>

            </div>

        </section>

    </div>
</div>    
</body>
</html>

