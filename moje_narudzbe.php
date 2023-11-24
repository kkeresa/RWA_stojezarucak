<?php

include 'php/connect.php';

if(isset($_COOKIE['id_korisnik'])){
    $id_korisnik = $_COOKIE['id_korisnik'];
 }else{
    setcookie('id_korisnik', create_unique_id(), time() + 60*60*24*30);
 }

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
}

if(isset($_POST['cancel'])){

   $update_orders = $pdo->prepare("UPDATE `narudzbe` SET status = ? WHERE id = ?");
   $update_orders->execute(['canceled', $get_id]);

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
   <title>Narudžbe - Shop - Što je za ručak?</title>

</head>
<body>
   
<?php include 'php/header.php'; ?>

<section class="order-details">

   <h1 class="heading">Narudžbe</h1>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_orders = $pdo->prepare("SELECT * FROM `narudzbe` WHERE id = ? LIMIT 1");
      $select_orders->execute([$get_id]);
      if($select_orders->rowCount() > 0){
         while($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)){
            $select_product = $pdo->prepare("SELECT * FROM `proizvodi` WHERE id = ? LIMIT 1");
            $select_product->execute([$fetch_order['id_proizvod']]);
            if($select_product->rowCount() > 0){
               while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)){
                  $sub_total = ($fetch_order['cijena'] * $fetch_order['kolicina']);
                  $grand_total += $sub_total;
   ?>
   <div class="box">
      <div class="col">
         <p class="title"><i class="fas fa-calendar"></i><?= $fetch_order['datum']; ?></p>
         <img src="images/<?= $fetch_product['slika']; ?>" class="slika" alt="">
         <p class="price"><i class="fa fa-euro"></i> <?= $fetch_order['cijena']; ?> <i class="fa fa-euro"></i> x <?= $fetch_order['qty']; ?></p>
         <h3 class="name"><?= $fetch_product['ime']; ?></h3>
         <p class="grand-total">grand total : <span> <?= $grand_total; ?> <i class="fa fa-euro"></i></span></p>
      </div>
      <div class="col">
         <p class="title">Adresa</p>
         <p class="user"><i class="fas fa-user"></i><?= $fetch_order['ime']; ?></p>
         <p class="user"><i class="fas fa-phone"></i><?= $fetch_order['broj_tel']; ?></p>
         <p class="user"><i class="fas fa-envelope"></i><?= $fetch_order['email']; ?></p>
         <p class="user"><i class="fas fa-map-marker-alt"></i><?= $fetch_order['adresa']; ?></p>
         <p class="title">Status</p>
         <p class="status" style="color:<?php if($fetch_order['status'] == 'Dostavljeno'){echo 'green';}elseif($fetch_order['status'] == 'Otkazano'){echo 'red';}else{echo 'orange';}; ?>"><?= $fetch_order['status']; ?></p>
         <?php if($fetch_order['status'] == 'Otkazano'){ ?>
            <a href="checkout.php?get_id=<?= $fetch_product['id']; ?>" class="btn">Naruči ponovno</a>
         <?php }else{ ?>
         <form action="" method="POST">
            <input type="submit" value="Otkaži narudžbu" name="cancel" class="delete-btn" onclick="return confirm('Otkaži ovu narudžbu?');">
         </form>
         <?php } ?>
      </div>
   </div>
   <?php
            }
         }else{
            echo '<p class="empty">Proizvod nije pronađen.</p>';
         }
      }
   }else{
      echo '<p class="empty">Nije pronađena niti jedna narudžba.</p>';
   }
   ?>

   </div>

</section>

<script src="js/script.js"></script>
</body>
</html>