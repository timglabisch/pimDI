<?php

namespace diRunable;

class Inject implements \de\any\di\iRunable {

    /** @var \diTest\istd !inject */
    public $std;

    /** @var \diTest\istd !inject std2 */
    public $std2;

    public $iostd;
    public $iostd2;

   /**
     * @inject
     */
    public function injectIostd(\diTest\iostd $iostd) {
        $this->iostd = $iostd;
    }

    /**
      * @inject std2
      */
    public function injectIostd2(\diTest\iostd $iostd) {
        $this->iostd2 = $iostd;
    }

    public function getIostd() {
        return $this->iostd;
    }

    public function getIostd2() {
        return $this->iostd2;
    }

    public function run() {
      
    }

}