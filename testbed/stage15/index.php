<?php
/**
 * Created by PhpStorm.
 * User: andreas.martin
 * Date: 12.09.2017
 * Time: 21:30
 */
require_once("config/Autoloader.php");

use router\Router;
use service\ServiceEndpoint;
use http\HTTPException;
use http\HTTPHeader;
use http\HTTPStatusCode;

Router::route("GET", "/api/customer", function () {
    ServiceEndpoint::findAllCustomer();
});

Router::route("GET", "/api/customer/{id}", function ($id) {
    ServiceEndpoint::readCustomer($id);
});

Router::route("PUT", "/api/customer/{id}", function ($id) {
    ServiceEndpoint::updateCustomer($id);
});

Router::route("POST", "/api/customer", function () {
    ServiceEndpoint::createCustomer();
});

Router::route("DELETE", "/api/customer/{id}", function ($id) {
    ServiceEndpoint::deleteCustomer($id);
});

try {
    HTTPHeader::setHeader("Access-Control-Allow-Origin: *");
    HTTPHeader::setHeader("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD");
    HTTPHeader::setHeader("Access-Control-Allow-Headers: Authorization, Location, Origin, Content-Type, X-Requested-With");
    if($_SERVER['REQUEST_METHOD']=="OPTIONS") {
        HTTPHeader::setStatusHeader(HTTPStatusCode::HTTP_204_NO_CONTENT);
    } else {
        Router::call_route($_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
    }
} catch (HTTPException $exception) {
    $exception->getHeader();
}