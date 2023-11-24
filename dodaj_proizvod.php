<?php

include 'php/connect.php';


if(isset($_COOKIE['id_korisnik'])){
    $user_id = $_COOKIE['id_korisnik'];
 }else{
    setcookie('id_korisnik', create_unique_id(), time() + 60*60*24*30);
 }

if($_SERVER["REQUEST_METHOD"] == "POST"){
   $ime = $_POST["ime"];
   $cijena = $_POST["cijena"];
   
   $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["slika"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["add_product"])) {
    $check = getimagesize($_FILES["slika"]["tmp_name"]);
    if($check !== false) {
        $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo '<script>alert "File is not an image."</script>';
        $uploadOk = 0;
    }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
    echo '<script>alert "Sorry, file already exists."</script>';
    $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["slika"]["size"] > 2000000) {
    echo '<script>alert "Sorry, your file is too large."</script>';
    $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
    echo '<script>alert "Sorry, only JPG, JPEG, PNG & GIF files are allowed."</script>';
    $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
    echo '<script>alert "Sorry, your file was not uploaded."</script>';
    // if everything is ok, try to upload file
    } else {
    if (move_uploaded_file($_FILES["slika"]["tmp_name"], $target_file)) {
        echo '';
    } else {
        echo '<script>alert "Sorry, there was an error uploading your file."</script>';
  }
}

   try{
    $query = "INSERT INTO proizvodi (ime, cijena, slika) VALUES(?,?,?);";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$ime,$cijena,$target_file]);
    
   }
    catch(PDOException $e){
        die("Došlo je do pogreške." . $e->getMessage());
    }
}
else{

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
<main>
<section class="dodaj_proizvod">
    <form action="" method="POST" enctype="multipart/form-data">
        <h3>O proizvodu</h3>
        <p>Naziv proizvoda <span>*</span></p>
        <input type="text" name="ime" placeholder="Upišite ime proizvoda" class="box">
        <p>Cijena proizvoda <span>*</span></p>
        <input type="number" name="cijena" placeholder="Upišite cijenu proizvoda" class="box">
        <p>Slika proizvoda <span>*</span></p>
        <input type="file" name="slika" class="box">
        <input type="submit" value="Dodaj proizvod" name="add_product" class="btn">
    </form>
</section>

<script scr="../js/script.js"></script>

</main>
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