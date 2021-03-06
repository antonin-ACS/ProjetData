<?php session_start();

  require "secret.php"; //le app_secret est dans un fichier à part qu'on ne commit pas sur github
  if ((!isset($_COOKIE['token']) || empty($_COOKIE['token'])) && isset($_COOKIE['refresh_token']) && !empty($_COOKIE['refresh_token'])) :
      /* Si on a pas de token dans les cookies (expiré) MAIS qu'on a un refresh_token,
      on fait un requête pour obtenir un nouveau token*/
    

      /***************************
          Début requête CURL
      /***************************/
      $ch = curl_init();

      //les données obligatoires :  type de demande (ici, refresh), app_id, app_secret et focément le refresh_token
      $data = [
          'grant_type' => "refresh_token",
          'refresh_token' => $_COOKIE['refresh_token'],
          'client_id' => "3aabab9b39d94a038411b964540ac02d",
          'client_secret' => $secret,
      ];

      curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $server_output = curl_exec($ch);
      $server_output = json_decode($server_output, true); //On convertit une réponse JSON en tableau

      setrawcookie("token", $server_output['access_token'], time() + 60 );
      //Quand le serveur a répondu avec un token, on le stock dans les cookies pour 1 heure (timestamp actuel + 60 fois une minute )

      curl_close($ch);
      /***************************
            Fin requête CURL
      /***************************/

  elseif(!isset($_GET['code']) && !isset($_COOKIE['token']) && !isset($_COOKIE['refresh_token'])) :
      /*Si on a ni code d'authentification, ni token, ni refresh_token, c'est une première visite,
      on redirige vers spotify pour se connecter au compte. la variable GET "callback" indique où
      spotify renvoie après la connexion.*/
    ?><script>
    window.location.href = "https://accounts.spotify.com/authorize?client_id=3aabab9b39d94a038411b964540ac02d&response_type=code&redirect_uri=http://localhost/ProjetData";
    </script><?php
  elseif(!isset($_COOKIE['token']) && !isset($_COOKIE['refresh_token'])) :
      /*Si ni token, ni refresh_token, mais qu'on a le code d'acceptation, on demande un token*/

      $ch = curl_init();

      $data = [
        'grant_type' => "authorization_code",
        'code' => $_GET['code'],
        'redirect_uri' => "http://localhost/ProjetData",
        'client_id' => "3aabab9b39d94a038411b964540ac02d",
        'client_secret' => $secret
      ];

      curl_setopt($ch, CURLOPT_URL,"https://accounts.spotify.com/api/token");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $server_output = curl_exec($ch);


      curl_close ($ch);

      $server_output = json_decode($server_output, true);

      setrawcookie ( "token" , $server_output['access_token'], time() + 60  );
      setrawcookie("refresh_token", $server_output['refresh_token'], time() + 60 * 60 * 24);
      //on stock le token pour 1 heure et le refresh_token pour 24heures

    endif;



?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Erro World Sound</title>
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css?family=Righteous|Shrikhand|Poppins" rel="stylesheet">
    <link rel="shortcut icon" type="image/svg" href="Images/logoicon.svg">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" href="style.css">

</head>

<body>

<header class="img-fluid text-center ">
  <div class="header" >
    <img src="images/banner.png" class="img-fluid imgfond" alt="Responsive image">

  </div>
  <img id="logo" class="img-fluid text-center justify-content-center" src="images/logo.svg" alt="logo ERRO World Sound">
  <h1 class="titre">La musique autour de vous</h1>
</header>

<main>


<section class="container">
  <div class="row justify-content-center">
    <div class="card col-md-4" >
      <p>
        Playlist des nouvelles sorties
      </p>
       <div id="playlist-new-sorties">
        <div class="card-body">
        </div>
</div>
    </div>
    <div class="card col-md-4 offset-md-1">
      <p>
        Les 50 titres les plus écoutés dans le pays
      </p>
      <div id="reponse-50titres">
        <script id="user-profile-template"  type="text/x-handlebars-template">
          <a href="{{playlists.items.0.external_urls.spotify}}"> <img class="media-object" width="150" src="{{playlists.items.0.images.0.url}}"/></a>
          <p>{{playlists.items.0.name}}</p>
        </div>
      </script>
      </div>

      <div class="card-body">
    <!-- <a href="#" class="btn btn-primary">Go somewhere</a> -->
      </div>
    </div>
</div>
</section>


<section id="carouselExampleControls" class="carousel slide" data-ride="carousel">
  <section class="container">
      <div class="row justify-content-center">
        <script>

          fetch('https://translation.googleapis.com/language/translate/v2&')
          .then(function(response) {
            return response.json();
          })
          .then(function(myJson) {
            console.log(JSON.stringify(myJson));
          });
          let url = "";

        </script>
          <p id="reponse" class="col-md-12">Le top des playlists écoutées</p>
      </div>
  </section>
  <div class="carousel-inner">
    <div class="carousel-item active">
        <div class="carousel__container">
          <div class="car__item" id="playlist-basic">
            <h1>9</h1>

              <p>Basic plan</p>

          </div>
          <div class="car__item" id="playlist-medium">
            <h1>49</h1>
            <p>Medium plan</p>
          </div>
          <div class="car__item"  id="business">
            <h1>99</h1>
            <p>Business plan</p>
          </div>
          <div class="car__item" id="master">
            <h1>149</h1>
            <p>Master plan</p>
          </div>
        </div>
    </div>
    <div class="carousel-item">
    <div class="carousel__container">
          <div class="car__item">
            <h1>9</h1>
            <p>Basic plan</p>
          </div>
          <div class="car__item">
            <h1>49</h1>
            <p>Medium plan</p>
          </div>
          <div class="car__item">
            <h1>99</h1>
            <p>Business plan</p>
          </div>
          <div class="car__item">
            <h1>149</h1>
            <p>Master plan</p>
          </div>
        </div>
    </div>
    <div class="carousel-item">
    <div class="carousel__container">
          <div class="car__item">
            <h1>9</h1>
            <p>Basic plan</p>
          </div>
          <div class="car__item">
            <h1>49</h1>
            <p>Medium plan</p>
          </div>
          <div class="car__item">
            <h1>99</h1>
            <p>Business plan</p>
          </div>
          <div class="car__item">
            <h1>149</h1>
            <p>Master plan</p>
          </div>
        </div>
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</section>

</main>

<footer>
<section class="container">
      <div class="row justify-content-end">
        © ERRO - 2019
      </div>
</section>
</footer>

<!-- code Robin -->
<!--
<div ></div>
        <div id="demo"></div>

        <p id="demo"></p> -->


  <!-- script jquery Robin -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

  <script src="//cdnjs.cloudflare.com/ajax/libs/handlebars.js/2.0.0-alpha.1/handlebars.min.js"></script>

<!-- script -->

    <script>
      <?php
      /*Ici on fait une variable JavaScript qui contient le token et qui existera dans script.js puisqu'on le fait
      juste avant d'inclure script.js. 
      Si on a le cookie, on utilise le cookie, sinon on utilise la réponse du serveur
      (à la première demande, les cookies n'existent pas, ils existeront seulement au prochain refresh de la page)*/
      
      if(isset($_COOKIE['token']) && !empty($_COOKIE['token'])) : ?>
        var access_token = "<?=$_COOKIE['token']?>";
      <?php elseif(isset($server_output['access_token']) && !empty($server_output['access_token'])) : ?>
        var access_token = "<?=$server_output['access_token']?>";
      <?php endif; ?>
    </script>

  <script src="script.js"></script>
</body>

</html>
