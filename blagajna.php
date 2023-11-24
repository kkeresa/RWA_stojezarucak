<?php

include 'php/connect.php';

if(isset($_COOKIE['id_korisnik'])){
    $user_id = $_COOKIE['id_korisnik'];
 }else{
    setcookie('id_korisnik', create_unique_id(), time() + 60*60*24*30);
 }

if(isset($_POST['place_order'])){

   $ime = $_POST['ime'];
   $ime = filter_var($ime, FILTER_SANITIZE_STRING);
   $broj_tel = $_POST['broj_tel'];
   $broj_tel = filter_var($broj_tel, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $adresa = $_POST['ulica'].', '.$_POST['grad'].', '.$_POST['p_broj'].', '.$_POST['zemlja'];
   $adresa = filter_var($adresa, FILTER_SANITIZE_STRING);

   $verify_cart = $pdo->prepare("SELECT * FROM `kosarica` WHERE id_korisnik = ?");
   $verify_cart->execute([$id_korisnik]);
   
   if(isset($_GET['get_id'])){

      $get_product = $pdo->prepare("SELECT * FROM `proizvodi` WHERE id = ? LIMIT 1");
      $get_product->execute([$_GET['get_id']]);
      if($get_product->rowCount() > 0){
         while($fetch_p = $get_product->fetch(PDO::FETCH_ASSOC)){
            $insert_order = $pdo->prepare("INSERT INTO `narudzbe`(id, id_korisnik, ime, broj_tel, email, adresa, id_proizvod, cijena, kolicina) VALUES(?,?,?,?,?,?,?,?,?)");
            $insert_order->execute([create_unique_id(), $id_korisnik, $ime, $broj_tel, $email, $adresa, $fetch_p['id'], $fetch_p['cijena'], 1]);
         }
      }else{
         $warning_msg[] = 'Something went wrong!';
      }

   }elseif($verify_cart->rowCount() > 0){

      while($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)){

         $insert_order = $pdo->prepare("INSERT INTO `narudzbe`(id, id_korisnik, ime, broj_tel, email, adresa, id_proizvod, cijena, kolicina) VALUES(?,?,?,?,?,?,?,?,?)");
         $insert_order->execute([create_unique_id(), $id_korisnik, $ime, $broj_tel, $email, $adresa, $f_cart['id_proizvod'], $f_cart['cijena'], $f_cart['kolicina']]);

      }

      if($insert_order){
         $delete_cart_id = $pdo->prepare("DELETE FROM `kosarica` WHERE id_korisnik = ?");
         $delete_cart_id->execute([$id_korisnik]);
      }

   }else{
      $warning_msg[] = 'Your cart is empty!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="https://img.icons8.com/plasticine/100/000000/food.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="stylesheet" href="css\shop.css">
    <title>Blagajna - Shop - Što je za ručak?</title>

</head>
<body>
   
<?php include 'php/header.php'; ?>

<section class="checkout">

   <h1 class="heading">Blagajna</h1>

   <div class="row">

      <form action="" method="POST">
         <h3>Detalji o kupcu</h3>
         <div class="flex">
            <div class="box">
               <p>Vaše ime <span>*</span></p>
               <input type="text" name="ime" required maxlength="50" placeholder="Upišite svoje ime" class="input">
               <p>Vaš kontakt broj <span>*</span></p>
               <input type="number" name="broj_tel" required maxlength="10" placeholder="Upišite svoj broj telefona" class="input" min="0" max="9999999999">
               <p>Vaš e-mail <span>*</span></p>
               <input type="email" name="email" required maxlength="50" placeholder="Upišite svoj e-mail" class="input">
               
            </div>
            <div class="box">
               <p>Adresa <span>*</span></p>
               <input type="text" name="ulica" required maxlength="50" placeholder="Upišite svoju ulicu i kućni broj" class="input">
               <p>Grad <span>*</span></p>
               <input type="text" name="grad" required maxlength="50" placeholder="Upišite grad u kojem živite" class="input">
               <p>Poštanski broj <span>*</span></p>
               <input type="text" name="p_broj" required maxlength="5" placeholder="Upišite svoj poštanski broj, npr. 10290" class="input">
               <p>Država <span>*</span></p>
               <input type="text" name="zemlja" required maxlength="50" placeholder="Hrvatska" class="input">
            </div>
         </div>
         <input type="submit" value="Naruči" name="place_order" class="btn">
      </form>

      <div class="summary">
         <h3 class="title">Proizvodi u košarici</h3>
         <?php
            $grand_total = 0;
            if(isset($_GET['get_id'])){
               $select_get = $pdo->prepare("SELECT * FROM `proizvodi` WHERE id = ?");
               $select_get->execute([$_GET['get_id']]);
               while($fetch_get = $select_get->fetch(PDO::FETCH_ASSOC)){
         ?>
         <div class="flex">
            <img src="<?= $fetch_get['slika']; ?>" class="image" alt="">
            <div>
               <h3 class="ime"><?= $fetch_get['ime']; ?></h3>
               <p class="cijena"> <?= $fetch_get['cijena']; ?> <i class="fa fa-euro"></i> x 1</p>
            </div>
         </div>
         <?php
               }
            }else{
               $select_cart = $pdo->prepare("SELECT * FROM `kosarica` WHERE id_korisnik = ?");
               $select_cart->execute([$id_korisnik]);
               if($select_cart->rowCount() > 0){
                  while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                     $select_products = $pdo->prepare("SELECT * FROM `proizvodi` WHERE id = ?");
                     $select_products->execute([$fetch_cart['id_proizvod']]);
                     $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
                     $sub_total = ($fetch_cart['kolicina'] * $fetch_product['cijena']);

                     $grand_total += $sub_total;
            
         ?>
         <div class="flex">
            <img src="<?= $fetch_product['slika']; ?>" class="image" alt="">
            <div>
               <h3 class="ime"><?= $fetch_product['ime']; ?></h3>
               <p class="cijena"></i> <?= $fetch_product['cijena']; ?> <i class="fa fa-euro"> x <?= $fetch_cart['kolicina']; ?></p>
            </div>
         </div>
         <?php
                  }
               }else{
                  echo '<p class="empty">your cart is empty</p>';
               }
            }
         ?>
         <div class="grand-total"><span>Ukupno za platiti :</span><p> <?= $grand_total; ?> <i class="fa fa-euro"></i></p></div>
      </div>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>