<?php

namespace de\any\di\binder;
use de\any\di\binder;

class repository {

    private $bindings = array();
    private $unknownBindings = array();
    private $unknownBindingsCount = 0;

    public function addBinding(binder $binding) {
        $this->unknownBindingsCount++;
        $this->unknownBindings[] = $binding;
    }

    public function addBindings(array $bindings) {
        foreach($bindings as $binding)
            $this->addBinding($binding);
    }

    private function knowBindings() {
        if($this->unknownBindingsCount === 0)
            return;

        foreach($this->unknownBindings as &$unknownBinding) {
            $hashKey = $unknownBinding->getHashKey();

            if(!isset($this->bindings[$hashKey]))
                $this->bindings[$hashKey] = array('decorator'=>array(), 'impl'=>null);

            if(!$unknownBinding->isDecorated())
                $this->bindings[$hashKey]['impl'] = $unknownBinding;
            else
                $this->bindings[$hashKey]['decorator'][] = $unknownBinding;
        }

        $this->unknownBindings = array();
        $this->unknownBindingsCount = 0;
    }

    private function interfaceIsARepository($interface) {
        return strlen($interface) > 1 && $interface[strlen($interface) -2] == "[" && $interface[strlen($interface) -1] == "]";
    }

    private function interfaceIsAClassBinding($interface) {
        return class_exists($interface);
    }

    /**
     * @throws Exception
     * @param  $interface
     * @param  $concern
     * @return repository
     */
    public function getBinding($interface, $concern='') {
        $this->knowBindings();

        if(substr($interface, 0, 1) != '\\')
            $interface = '\\'.$interface;

        if(!isset($this->bindings[$interface.'|'.$concern])) {

            if($this->interfaceIsARepository($interface)) {
                $binding = new binder($interface);
                $binding->concern($concern);
                $this->addBinding($binding);
                $this->knowBindings();
            }
            else if($this->interfaceIsAClassBinding($interface)) {
                $binding = new binder($interface);
                $binding->concern($concern);
                $binding->setInterfaceImpl($interface);
                $binding->setIsClass(true);
                $this->addBinding($binding);
                $this->knowBindings();
            }
            else
                throw new \InvalidArgumentException('Binding for interface "'.$interface.'" with concern "'.$concern.'" doesn\'t exists');

        }

        return $this->bindings[$interface.'|'.$concern]['impl'];
    }

    /**
     * @throws Exception
     * @param $interface
     * @param $concern
     * @return array
     */
    public function getBindingDecorators($interface, $concern='') {
        $this->knowBindings();

        if(substr($interface, 0, 1) != '\\')
            $interface = '\\'.$interface;

        if(!isset($this->bindings[$interface.'|'.$concern]))
            throw new \Exception('Binding for interface "'.$interface.'" with concern "'.$concern.'" doesn\'t exists');

        return $this->bindings[$interface.'|'.$concern]['decorator'];
    }

}