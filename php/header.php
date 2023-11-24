<?php error_reporting (E_ALL ^ E_NOTICE); ?>
<header class="header">

    <section class="flex">
        <a href="index.html" class="logo"><i class="fa fa-cutlery fa-1x"></i>što je za ručak?</a>
        <nav class="navbar">
            <a href="dodaj_proizvod.php">Dodaj proizvod</a>
            <a href="svi_proizvodi.php">Svi proizvodi</a>
            <a href="moje_narudzbe.php">Moje narudžbe</a>
            <?php 
                $count_cart_items = $pdo -> prepare("SELECT * FROM kosarica WHERE id_korisnik = ?");
                $count_cart_items -> execute([$id_korisnik]);
                $total_cart_items = $count_cart_items -> rowCount();
            ?>
            <a href="kosarica.php">Košarica <span><?= $total_cart_items; ?></span></a>
        </nav>

<div id="menu-btn" class="fa fa-bars"></div>

    </section>

</header>