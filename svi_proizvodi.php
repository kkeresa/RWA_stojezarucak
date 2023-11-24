<?php

include 'php/connect.php';

if(isset($_COOKIE['id_korisnik'])){
   $id_korisnik = $_COOKIE['id_korisnik'];
}else{
   setcookie('id_korisnik', create_unique_id(), time() + 60*60*24*30);
}

if(isset($_POST['dodaj_u_kosaricu'])){

   $id_proizvod = $_POST['id_proizvod'];
   $id_proizvod = filter_var($id_proizvod, FILTER_SANITIZE_STRING);
   $kolicina = $_POST['kolicina'];
   $kolicina = filter_var($kolicina, FILTER_SANITIZE_STRING);
   
   $verify_cart = $pdo->prepare("SELECT * FROM `kosarica` WHERE id_korisnik = ? AND id_proizvod = ?");   
   $verify_cart->execute([$id_korisnik, $id_proizvod]);

   $max_cart_items = $pdo->prepare("SELECT * FROM `kosarica` WHERE id_korisnik = ?");
   $max_cart_items->execute([$id_korisnik]);

   if($verify_cart->rowCount() > 0){
      echo '<script>alert("Već dodano u košaricu!")</script>';
   }elseif($max_cart_items->rowCount() == 10){
      echo '<script>alert("Košarica je puna!")</script>';
   }else{

      $select_price = $pdo->prepare("SELECT * FROM `proizvodi` WHERE id = ? LIMIT 1");
      $select_price->execute([$id_proizvod]);
      $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

      $insert_cart = $pdo->prepare("INSERT INTO `kosarica`(id_korisnik, id_proizvod, cijena, kolicina) VALUES(?,?,?,?)");
      $insert_cart->execute([$id_korisnik, $id_proizvod, $fetch_price['cijena'], $kolicina]);
      echo '<script>alert("Uspješno dodano u košaricu")</script>';
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
    <title>Shop - Što je za ručak?</title>

</head>
<body>
   
<?php include 'php/header.php'; ?>

<section class="products">

   <h1 class="heading">Što je za ručak? - Shop</h1>

   <div class="box-container">

   <?php 
      $select_products = $pdo->prepare("SELECT * FROM proizvodi");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_prodcut = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="POST" class="box">
      <img src="<?= $fetch_prodcut['slika']; ?>" class="slika" alt="">
      <h3 class="ime"><?= $fetch_prodcut['ime'] ?></h3>
      <input type="hidden" name="id_proizvod" value="<?= $fetch_prodcut['id']; ?>">
      <div class="flex">
         <p class="cijena"><?= $fetch_prodcut['cijena'] ?> <i class="fa fa-euro"></i></p>
         <input type="number" name="kolicina" required min="1" value="1" max="99" maxlength="2" class="kolicina">
      </div>
      <input type="submit" name="dodaj_u_kosaricu" value="Dodaj u košaricu" class="btn">
      <a href="blagajna.php?get_id=<?= $fetch_prodcut['id']; ?>" class="delete-btn">Kupi odmah</a>
   </form>
   <?php
      }
   }else{
      echo '<script>alert("Nije pronađen niti jedan proizvod!")</script>';
   }
   ?>

   </div>

</section>

<script src="js/script.js"></script>

<footer>
        <div class="footer-sadrzaj">
            <h3>Što je za ručak?</h3>
            <p>'Što je za ručak?' jedina je online kuharica koju ćete ikada trebati. Uživajte u ukusnim receptima,
                svakodnevno se upustite u nove kulinarske avanture i zadivite svoje najbliže!</p>
            <ul class="sns">
                <li><a href="https://www.facebook.com/" target="_blank"><i class="fa fa-facebook"></i></a></li>
                <li><a href="https://twitter.com/?lang=en" target="_blank"><i class="fa fa-twitter"></i></a></li>
                <li><a href="https://www.instagram.com/" target="_blank"><i class="fa fa-instagram"></i></a></li>
                <li><a href="https://www.youtube.com/" target="_blank"><i class="fa fa-youtube"></i></a></li>
                <li><a href="https://www.pinterest.com/" target="_blank"><i class="fa fa-pinterest"></i></a></li>
            </ul>
        </div>
    </footer>

</body>
</html>