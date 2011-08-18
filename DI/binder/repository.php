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

    /**
     * @throws Exception
     * @param  $interface
     * @param  $concern
     * @return repository
     */
    public function getBinding($interface, $concern='') {

        $this->knowBindings();

        if(!isset($this->bindings[$interface.'|'.$concern]))
            throw new \Exception('Binding for interface "'.$interface.'" with concern "'.$concern.'" doesn\'t exists');

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

        if(!isset($this->bindings[$interface.'|'.$concern]))
            throw new Exception('Binding for interface "'.$interface.'" with concern "'.$concern.'" doesn\'t exists');

        return $this->bindings[$interface.'|'.$concern]['decorator'];
    }

}