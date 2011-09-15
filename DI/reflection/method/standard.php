<?php
namespace de\any\di\reflection\method;

class standard implements \de\any\di\reflection\iMethod  {

    private $parameters;
    private $methodName;
    private $inject;

    public function __construct($methodname) {
        $this->setMethodName($methodname);
    }
    
    public function getParameters() {
        return $this->parameters;
    }

    public function setParameters(array $params) {
        $this->parameters = $params;
        return $this;
    }

    public function invokeArgs($instance, $args) {
        $methodName = $this->getMethodName();

        $argsLength = count($args);

        switch($argsLength) {
            case 0:
                $instance->$methodName() ;
                break;
            case 1:
                $instance->$methodName($args[0]);
                break;
            case 2:
                $instance->$methodName($args[0], $args[1]);
                break;
            case 3:
                $instance->$methodName($args[0], $args[1], $args[2]);
                break;
            case 4:
                $instance->$methodName($args[0], $args[1], $args[2], $args[3]);
                break;
            case 5:
                $instance->$methodName($args[0], $args[1], $args[2], $args[3], $args[4]);
                break;
            case 6:
                $instance->$methodName($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                break;
            default:
                call_user_func_array(array($instance, $methodName) , $args);
                break;
        }

    }

    public function setMethodName($methodName) {
        $this->methodName = $methodName;
    }

    public function getMethodName() {
        return $this->methodName;
    }


    public function setParamsByReflectionMethod(\ReflectionMethod $method) {
        $params = array();

        $annotations = \de\any\di\ReflectionAnnotation::parseMethodAnnotations($method);
       
        $i = 0;

        foreach($method->getParameters() as $param) {

            if(!$param->getClass()) {
                $i++;
                continue;
            }

            $dicParam = new \de\any\di\reflection\param\standard();
            $dicParam->setInterface($param->getClass()->getName());

            if(isset($annotations['inject'][$i])) {
                $dicParam->setInject(true);
                $dicParam->setConcern(isset($annotations['inject'][$i])?$annotations['inject'][$i]:'');
            } else {
                $dicParam->setInject(false);
            }

            $params[$i] = $dicParam;

            $i++;
        }

        $this->setParameters($params);
    }

    public function setInject($inject) {
        $this->inject = $inject;
    }

    public function getInject() {
        return $this->inject;
    }

}