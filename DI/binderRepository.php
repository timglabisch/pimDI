<?php

namespace de\any\di;

class binderRepository {

    private $bindings = array();
    private $unknownBindings = array();

    public function addBinding(binder $binding) {
        $this->unknownBindings[] = $binding;
    }

    private function knowBindings() {
        if(!count($this->unknownBindings))
            return;

        foreach($this->unknownBindings as $key => $unknownBinding) {
            $this->bindings[$unknownBinding->getHashKey()] = $unknownBinding;
            unset($this->unknownBindings[$key]);
        }
    }

    /**
     * @throws Exception
     * @param  $interface
     * @param  $concern
     * @return binder
     */
    public function getBinding($interface, $concern) {

        $this->knowBindings();

        if(!isset($this->bindings[$interface.'|'.$concern]))
            throw new Exception('Binding for interface "'.$interface.'" with concern "'.$concern.'" doesn\'t exists');

        return $this->bindings[$interface.'|'.$concern];
    }

}
 
