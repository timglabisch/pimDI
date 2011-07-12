<?php

namespace de\any\di\binder;
use de\any\di\binder;

class repository {

    private $bindings = array();
    private $unknownBindings = array();

    public function addBinding(binder $binding) {
        $this->unknownBindings[] = $binding;
    }

    private function knowBindings() {
        if(!count($this->unknownBindings))
            return;

        foreach($this->unknownBindings as $key => $unknownBinding) {

            if(!isset($this->bindings[$unknownBinding->getHashKey()]))
                $this->bindings[$unknownBinding->getHashKey()] = array('decorator', 'impl');

            if(!$unknownBinding->isDecorated())
                $this->bindings[$unknownBinding->getHashKey()]['impl'] = $unknownBinding;
            else
                $this->bindings[$unknownBinding->getHashKey()]['decorator'][] = $unknownBinding;

            unset($this->unknownBindings[$key]);

        }
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
            throw new Exception('Binding for interface "'.$interface.'" with concern "'.$concern.'" doesn\'t exists');

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


 
