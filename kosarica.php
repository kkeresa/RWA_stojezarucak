<?php

include 'php/connect.php';

if(isset($_COOKIE['id_korisnik'])){
    $id_korisnik = $_COOKIE['id_korisnik'];
 }else{
    setcookie('id_korisnik', create_unique_id(), time() + 60*60*24*30);
 }

if(isset($_POST['update_cart'])){

   $id_kosarica = $_POST['id_kosarica'];
   $id_kosarica = filter_var($id_kosarica, FILTER_SANITIZE_STRING);
   $kolicina = $_POST['kolicina'];
   $kolicina = filter_var($kolicina, FILTER_SANITIZE_STRING);

   $update_kolicina = $pdo->prepare("UPDATE `kosarica` SET kolicina = ? WHERE id = ?");
   $update_kolicina->execute([$kolicina, $id_kosarica]);

   $success_msg[] = 'Cart quantity updated!';

}

if(isset($_POST['delete_item'])){

   $id_kosarica = $_POST['id_kosarica'];
   $id_kosarica = filter_var($id_kosarica, FILTER_SANITIZE_STRING);
   
   $verify_delete_item = $pdo->prepare("SELECT * FROM `kosarica` WHERE id = ?");
   $verify_delete_item->execute([$id_kosarica]);

   if($verify_delete_item->rowCount() > 0){
      $delete_id_kosarica = $pdo->prepare("DELETE FROM `kosarica` WHERE id = ?");
      $delete_id_kosarica->execute([$id_kosarica]);
      $success_msg[] = 'Cart item deleted!';
   }else{
      $warning_msg[] = 'Cart item already deleted!';
   } 

}

if(isset($_POST['empty_cart'])){
   
   $verify_empty_cart = $pdo->prepare("SELECT * FROM `kosarica` WHERE id_korisnik = ?");
   $verify_empty_cart->execute([$id_korisnik]);

   if($verify_empty_cart->rowCount() > 0){
      $delete_id_kosarica = $pdo->prepare("DELETE FROM `kosarica` WHERE id_korisnik = ?");
      $delete_id_kosarica->execute([$id_korisnik]);
      $success_msg[] = 'Cart emptied!';
   }else{
      $warning_msg[] = 'Cart already emptied!';
   } 

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
   <title>Košarica - Shop - Što je za ručak?</title>

</head>
<body>
   
<?php include 'php/header.php'; ?>

<section class="products">

   <h1 class="heading">Košarica</h1>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $pdo->prepare("SELECT * FROM `kosarica` WHERE id_korisnik = ?");
      $select_cart->execute([$id_korisnik]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){

         $select_products = $pdo->prepare("SELECT * FROM `proizvodi` WHERE id = ?");
         $select_products->execute([$fetch_cart['id_proizvod']]);
         if($select_products->rowCount() > 0){
            $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
      
   ?>
   <form action="" method="POST" class="box">
      <input type="hidden" name="id_kosarica" value="<?= $fetch_cart['id']; ?>">
      <img src="<?= $fetch_product['slika']; ?>" class="slika" alt="">
      <h3 class="ime"><?= $fetch_product['ime']; ?></h3>
      <div class="flex">
         <p class="cijena"> <?= $fetch_cart['cijena']; ?> <i class="fa fa-euro"></i></p>
         <input type="number" name="kolicina" required min="1" value="<?= $fetch_cart['kolicina']; ?>" max="99" maxlength="2" class="kolicina">
         <button type="submit" name="update_cart" class="fas fa-edit">
         </button>
      </div>
      <p class="sub-total">Ukupno : <span> <?= $sub_total = ($fetch_cart['kolicina'] * $fetch_cart['cijena']); ?> <i class="fa fa-euro"></i></span></p>
      <input type="submit" value="Ukloni" name="delete_item" class="delete-btn" onclick="return confirm('Ukloni ovu stavku?');">
   </form>
   <?php
      $grand_total += $sub_total;
      }else{
         echo '<p class="empty">product was not found!</p>';
      }
      }
   }else{
      echo '<p class="empty">your cart is empty!</p>';
   }
   ?>

   </div>

   <?php if($grand_total != 0){ ?>
      <div class="cart-total">
         <p>Ukupno : <span> <?= $grand_total; ?> <i class="fa fa-euro"></i></span></p>
         <form action="" method="POST">
          <input type="submit" value="Isprazni košaricu" name="empty_cart" class="delete-btn" onclick="return confirm('Isprazniti košaricu?');">
         </form>
         <a href="blagajna.php" class="btn">Nastavi na blagajnu</a>
      </div>
   <?php } ?>

</section>

<script src="js/script.js"></script>

</body>
</html>