<?php

include 'php/connect.php';

if(isset($_COOKIE['id_korisnik'])){
    $id_korisnik = $_COOKIE['id_korisnik'];
 }else{
    setcookie('id_korisnik', create_unique_id(), time() + 60*60*24*30);
 }

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="https://img.icons8.com/plasticine/100/000000/food.png">
   <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
   <link rel="stylesheet" href="css/shop.css">
   <title>Moje narudžbe - Shop - Što je za ručak?</title>

</head>
<body>
   
<?php include 'php/header.php'; ?>

<section class="orders">

   <h1 class="heading">Moje narudžbe</h1>

   <div class="box-container">

   <?php
      $select_orders = $pdo->prepare("SELECT * FROM `narudzbe` WHERE id_korisnik = ? ORDER BY date DESC");
      $select_orders->execute([$id_korisnik]);
      if($select_orders->rowCount() > 0){
         while($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)){
            $select_product = $pdo->prepare("SELECT * FROM `proizvodi` WHERE id = ?");
            $select_product->execute([$fetch_order['product_id']]);
            if($select_product->rowCount() > 0){
               while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box" <?php if($fetch_order['status'] == 'Otkazano'){echo 'style="border:.2rem solid red";';}; ?>>
      <a href="view_order.php?get_id=<?= $fetch_order['id']; ?>">
         <p class="datum"><i class="fa fa-calendar"></i><span><?= $fetch_order['datum']; ?></span></p>
         <img src="images/<?= $fetch_product['slika']; ?>" class="slika" alt="">
         <h3 class="ime"><?= $fetch_product['ime']; ?></h3>
         <p class="cijena"> <?= $fetch_order['price']; ?> <i class="fa fa-euro"></i> x <?= $fetch_order['kolicina']; ?></p>
         <p class="status" style="color:<?php if($fetch_order['status'] == 'Dostavljeno'){echo 'green';}elseif($fetch_order['status'] == 'Otkazano'){echo 'red';}else{echo 'orange';}; ?>"><?= $fetch_order['status']; ?></p>
      </a>
   </div>
   <?php
            }
         }
      }
   }else{
      echo '<p class="empty">no orders found!</p>';
   }
   ?>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>