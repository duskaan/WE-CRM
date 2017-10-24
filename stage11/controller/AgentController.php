<?php
/**
 * Created by PhpStorm.
 * User: andreas.martin
 * Date: 08.10.2017
 * Time: 22:20
 */

namespace controller;

use service\WECRMServiceImpl;
use validator\AgentValidator;
use view\View;
use domain\Agent;

class AgentController
{
    public static function editView(){
        $view = new View("agentEdit.php");
        $view->agent = WECRMServiceImpl::getInstance()->readAgent();
        $view->pageTitle = "WE-CRM";
        $view->pageHeading = "<strong>WE-CRM | Update</strong> your account.";
        $view->pageSubmitText = "Update";
        $view->pageFormAction = "/agent/edit";
        echo $view->render();
    }

    public static function registerView(){
        echo (new View("agentEdit.php"))->render();
    }

    public static function update(){
        $view = new View("agentEdit.php");
        $view->pageTitle = "WE-CRM";
        $view->pageHeading = "<strong>WE-CRM | Update</strong> your account.";
        $view->pageSubmitText = "Update";
        $view->pageFormAction = "/agent/edit";
        return self::register($view);
    }

    public static function register($view = null){
        $agent = new Agent();
        $agent->setName($_POST["name"]);
        $agent->setEmail($_POST["email"]);
        $agent->setPassword($_POST["password"]);
        $agentValidator = new AgentValidator($agent);
        if($agentValidator->isValid()){
            if(WECRMServiceImpl::getInstance()->editAgent($agent->getName(),$agent->getEmail(), $agent->getPassword())){
                return true;
            }else{
                $agentValidator->setEmailError("Email already exists");
            }
        }
        $agent->setPassword("");
        if (is_null($view))
            $view = new View("agentEdit.php");
        $view->agent = $agent;
        $view->agentValidator = $agentValidator;
        echo $view->render();
        return false;
    }

    public static function loginView(){
        echo (new View("agentLogin.php"))->render();
    }
}