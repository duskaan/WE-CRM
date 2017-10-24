<?php
/**
 * Created by PhpStorm.
 * User: andreas.martin
 * Date: 12.09.2017
 * Time: 21:30
 */
require_once("router/router.php");
require_once("view/layout.php");
require_once("config/config.php");

session_start();

$authFunction = function () {
    if (isset($_SESSION["agentLogin"])) {
        return true;
    }
    redirect("/login");
    return false;
};

$errorFunction = function () {
    errorHeader();
    require_once("view/404.php");
};

route("GET", "/login", function () {
    require_once("view/agentLogin.php");
});

route("GET", "/register", function () {
    require_once("view/agentEdit.php");
});

route("POST", "/register", function () {
    $name = $_POST["name"];
    $email = $_POST["email"];
    require("database/database.php");
    $pdoInstance = connect();
    $stmt = $pdoInstance->prepare('
        INSERT INTO agent (name, email, password)
          SELECT :name,:email,:password
          WHERE NOT EXISTS (
            SELECT email FROM agent WHERE email = :emailExist
        );');
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':emailExist', $email);
    $stmt->bindValue(':password', password_hash($_POST["password"], PASSWORD_DEFAULT));
    $stmt->execute();
    redirect("/logout");
});

route("POST", "/login", function () {
    $email = $_POST["email"];
    require("database/database.php");
    $pdoInstance = connect();
    $stmt = $pdoInstance->prepare('
            SELECT * FROM agent WHERE email = :email;');
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $agent = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        if (password_verify($_POST["password"], $agent["password"])) {
            $_SESSION["agentLogin"]["name"] = $agent["name"];
            $_SESSION["agentLogin"]["email"] = $email;
            $_SESSION["agentLogin"]["id"] = $agent["id"];
            if (password_needs_rehash($agent["password"], PASSWORD_DEFAULT)) {
                $stmt = $pdoInstance->prepare('
                UPDATE agent SET password=:password WHERE id = :id;');
                $stmt->bindValue(':id', $agent["id"]);
                $stmt->bindValue(':password', password_hash($_POST["password"], PASSWORD_DEFAULT));
                $stmt->execute();
            }
        }
    }
    redirect("/");
});

route("GET", "/logout", function () {
    session_destroy();
    redirect("/login");
});

route_auth("GET", "/", $authFunction, function() {
    layoutSetContent("customers.php");
});

route_auth("GET", "/agent/edit", $authFunction, function() {
    require_once("view/agentEdit.php");
});

route_auth("GET", "/customer/create", $authFunction, function() {
    layoutSetContent("customerEdit.php");
});

route_auth("GET", "/customer/edit", $authFunction, function() {
    layoutSetContent("customerEdit.php");
});

route_auth("GET", "/customer/delete", $authFunction, function() {
    $data = $_GET["id"];
    redirect("/");
});

route_auth("POST", "/customer/update", $authFunction, function() {
    $data = $_POST["name"];
    redirect("/");
});

call_route($_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);