<?php
/**
 * Created by PhpStorm.
 * User: andreas.martin
 * Date: 16.10.2017
 * Time: 14:26
 */

namespace controller;

use service\WECRMServiceImpl;
use view\View;
use validator\AgentValidator;
use service\EmailServiceClient;

class AgentPasswordResetController
{

    public static function resetView(){
        $resetView = new View("agentPasswordReset.php");
        $resetView->token = $_GET["token"];
        echo $resetView->render();
    }
    
    public static function requestView(){
        echo (new View("agentPasswordResetRequest.php"))->render();
    }
    
    public static function reset(){
        if(WECRMServiceImpl::getInstance()->validateToken($_POST["token"])){
            $agent = WECRMServiceImpl::getInstance()->readAgent();
            $agent->setPassword($_POST["password"]);
            $agentValidator = new AgentValidator($agent);
            if($agentValidator->isValid()){
                if(WECRMServiceImpl::getInstance()->editAgent($agent->getName(),$agent->getEmail(), $agent->getPassword())){
                    return true;
                }
            }
            $agent->setPassword("");
            $resetView = new View("agentPasswordReset.php");
            $resetView->token = $_POST["token"];
            echo $resetView->render();
            return false;
        }
        return false;
    }

    public static function resetEmail(){
        $token = WECRMServiceImpl::getInstance()->issueToken(WECRMServiceImpl::RESET_TOKEN, $_POST["email"]);
        $emailView = new View("agentPasswordResetEmail.php");
        $emailView->resetLink = $GLOBALS["ROOT_URL"] . "/password/reset?token=" . $token;
        return EmailServiceClient::sendEmail($_POST["email"], "Password Reset Email", $emailView->render());
    }

}