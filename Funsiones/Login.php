<?php
    require_once "../Funsiones/Auth/Auth.php";

    $user = (isset($_POST['user'])) ? $_POST['user'] : '';
    $pass = (isset($_POST['pass'])) ? $_POST['pass'] : '';

    echo AuthLogin($user,$pass);
    Usuario($user);
