<?php

$app->mount( '/', new Controller\IndexController() );
$app->mount( '/register', new Controller\RegisterController() );
$app->mount( '/parking', new Controller\ParkingController() );
$app->mount( '/user', new Controller\UserController() );
$app->mount( '/car', new Controller\CarController() );
$app->mount( '/reservation', new Controller\ReservationController() );

?>