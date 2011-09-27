<?php

namespace de\any\di\test;
use de\any\di;
use de\any\di\binder;

class DITest extends \PHPUnit_Framework_TestCase {

    public function testDiSet() {
        $di = new di();
        $di->bind('istd')->to('std1');

        $this->assertInstanceOf('std1', $di->get('istd'));
        return $di;
    }

    /**
     * @depends testDiSet
     */
    public function testDiOverwrite($di) {
        $di->bind('istd')->to('std2');

        $this->assertInstanceOf('std2', $di->get('istd'));
    }

    public function testDIConcern() {
        $di = new di();
        $di->bind('istd')->to('std1');
        $di->bind('istd')->to('std2')->concern('abc');

        $this->assertInstanceOf('std2', $di->get('istd', 'abc'));#
        $this->assertInstanceOf('std1', $di->get('istd'));
    }
    

    /**
     * @expectedException Exception
     */
    public function testInterfaceDoesNotExists() {
        $di = new di();
        $di->bind('UNKNOWN')->to('std1');

        $this->assertInstanceOf('std1', $di->get('UNKNOWN'));
    }


    /**
     * @expectedException Exception
     */
    public function testBindingDoesNotExists() {
        $di = new di();
        $di->get('UNKNOWN');
    }

    /**
     * @expectedException Exception
     */
    public function testImplementationDoesNotExists() {
        $di = new di();
        $di->bind('istd')->to('UNKNOWN');
        $di->get('istd');
    }

    public function testBasicInjection() {
       $di = new di();
       $di->bind('nested_iobject')->to('nested_object');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice1');

       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1());
    }

    public function testDoubleInjection() {
       $di = new di();
       $di->bind('nested_iobject')->to('nested_objectDouble');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice1');

       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1());
       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1_2());

       $this->assertNull($di->get('nested_iobject')->service3);
    }

    public function testDouble2Injection() {
       $di = new di();
       $di->bind('nested_iobject')->to('nested_objectDouble2');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice1');

       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1());
       $this->assertInstanceOf('nested_inestedservice1', $di->get('nested_iobject')->getNestedService1_2());
    }

   public function testConcern() {
       $di = new di();
       $di->bind('nested_iobject')->to('nested_objectConcern');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice1');
       $di->bind('nested_inestedservice1')->to('nested_nestedservice2')->concern('abc');

       $this->assertInstanceOf('nested_nestedservice1', $di->get('nested_iobject')->getNestedService1());
       $this->assertInstanceOf('nested_nestedservice2', $di->get('nested_iobject')->getNestedService1_2());
    }

    public function testSharedDefault() {
       $di = new di();
       $di->bind('istd')->to('std1');

       $this->assertTrue($di->get('istd') !== $di->get('istd'));
    }

    public function testIsShared() {
       $di = new di();
       $di->bind('istd')->to('std1')->shared(true);

       $this->assertTrue($di->get('istd') === $di->get('istd'));
    }

    function testConstructorInjection() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_std1');
        $di->bind('constructor_istd')->to('constructor_std2')->concern('std2');
        $instance = $di->get('constructor_istd');

        $this->assertInstanceOf('constructor_std2', $instance->getService());
    }

    function testCreateInstanceFromClassname() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_std1');
        $di->bind('constructor_istd')->to('constructor_std2')->concern('std2');
        $instance = $di->createInstanceFromClassname('constructor_std1');

        $this->assertInstanceOf('constructor_std2', $instance->getService());
    }

    public function testDecorate() {
        $di = new di();
        $di->bind('istd')->to('diDecorateStd1');
        $di->bind('istd')->to('diDecorateDecorator1')->decorated(true);

        $this->assertEquals($di->get('istd')->foo(), 'foo, decorated1!');
    }

    public function testDecorateMultiple() {
        $di = new di();
        $di->bind('istd')->to('diDecorateStd1');
        $di->bind('istd')->to('diDecorateDecorator1')->decorated(true);
        $di->bind('istd')->to('diDecorateDecorator2')->decorated(true);

        $this->assertEquals($di->get('istd')->foo(), 'foo, decorated1!, decorated2!');
    }

    public function testDecoratedNested() {
        $di = new di();
        $di->bind('istd')->to('diDecorateStd1');
        $di->bind('istd')->to('diDecorateDecorator1')->decorated(true);
        $di->bind('istd')->to('diDecorateDecoratorNested1')->decorated(true);
        $di->bind('nested_inestedservice1')->to('nested_nestedservice1');

        $this->assertInstanceOf('nested_inestedservice1', $di->get('istd')->getService());
        $this->assertEquals($di->get('istd')->getService()->identify(), 'nested_nestedservice1');
    }

    public function testDecoratedDecorator() {
        $di = new di();
        $di->bind('decoratorDecorated_iBase1')->to('decoratorDecorated_base1');
        $di->bind('decoratorDecorated_iBase1')->to('decoratorDecorated_base1_decorator')->decorated(true);
        $di->bind('decoratorDecorated_iBase2')->to('decoratorDecorated_base2');
        $di->bind('decoratorDecorated_iBase2')->decoratedWith('decoratorDecorated_base2_decorator');

        $this->assertEquals($di->get('decoratorDecorated_iBase1')->getClassname(), 'decoratorDecorated_base1|decoratorDecorated_base1_decorator|decoratorDecorated_base2|decoratorDecorated_base2_decorator');
    }

    function testSharedNotDecorator() {
        $di = new di();
        $di->bind('sharedDecorators_iBase1')->to('sharedDecorators_base1');
        $di->bind('sharedDecorators_iBase1')->decoratedWith('sharedDecorators_base1_decorator');
        $decorator = $di->get('sharedDecorators_iBase1')->getService();

        $this->assertInstanceOf('sharedDecorators_base1_decorator', $decorator);
        $this->assertTrue($di->get('sharedDecorators_iBase1')->getService() !== $decorator);
    }

    function testSharedDecorator() {
        $di = new di();
        $di->bind('sharedDecorators_iBase1')->to('sharedDecorators_base1');
        $di->bind('sharedDecorators_iBase1')->decoratedWith('sharedDecorators_base1_decorator')->shared(true);
        $decorator = $di->get('sharedDecorators_iBase1')->getService();

        $this->assertInstanceOf('sharedDecorators_base1_decorator', $decorator);
        $this->assertTrue($di->get('sharedDecorators_iBase1')->getService() === $decorator);
    }

    public function testSetBinderRepositoy() {
        $di = new di();
        $class = new \stdClass();
        $di->setBinderRepository($class);

        $this->assertTrue($class === $di->getBinderRepository());
    }

    public function testDefaultBinderRepository() {
        $di = new di();
        
        $this->assertInstanceOf('\de\any\di\binder\repository', $di->getBinderRepository());
    }

    public function testBindInstances() {
        $di = new di();
        $std = new \std1();
        $di->bind('istd')->to($std);

        $this->assertTrue($di->get('istd') === $std);
        return $di;
    }

    /**
     * @depends testBindInstances
     */
    public function testBindSharedInstances($di) {
        $std = new \std1();
        $di->bind('istd')->to($std)->shared(false);

        $this->assertTrue($di->get('istd') !== $std);
        $this->assertInstanceOf('\std1', $di->get('istd'));
    }

    public function testInjectDiItself() {
        $di = new di();
        $di->bind('\de\any\iDi')->to($di);

        $this->assertTrue($di === $di->get('\de\any\iDi'));
    }

    public function testParam() {
        $di = new di();
        $di->bind('istd')->to('diParam_standard');
        $di->bind('iostd')->to('diParam_standard_injected');

        $this->assertInstanceOf('diParam_standard', $di->get('istd'));
        $this->assertInstanceOf('diParam_standard_injected', $di->get('istd')->service);
        return $di;
    }


    public function testClosure() {
        $di = new di();
        $di->foo = function() { return 'abc'; };

#        var_dump(($di->foo)());
##        $this->assertEquals('abc', $di->foo());
    }

    public function testParamConcern() {
        $di = new di();
        $di->bind('istd')->to('diParam_concern');
        $di->bind('iostd')->to('diParam_standard_injected');
        $di->bind('iostd')->to('ostd1')->concern('abc');

        $this->assertInstanceOf('diParam_concern', $di->get('istd'));
        $this->assertInstanceOf('diParam_standard_injected', $di->get('istd')->service);
        $this->assertInstanceOf('ostd1', $di->get('istd')->service_concern);
        return $di;
    }

    /**
       * A Depends on B and B depends on A,
       * @expectedException \de\any\di\exception\circular
       */
    public function testCicular() {
        $di = new di();
        $di->bind('iCircular')->to('circular_a')->concern('a');
        $di->bind('iCircular')->to('circular_b')->concern('b');

        $this->assertInstanceOf('circular_a', $di->get('iCircular', 'a'));
    }

   /**
     * B Depends on C and C depends on A, get instance A
     * @expectedException \de\any\di\exception\circular
     */
    public function testCicularNested() {
        $di = new di();
        $di->bind('iCircular')->to('circularNested_a')->concern('a');
        $di->bind('iCircular')->to('circularNested_b')->concern('b');
        $di->bind('iCircular')->to('circularNested_c')->concern('c');

        $this->assertInstanceOf('circular_a', $di->get('iCircular', 'a'));
    }

   /**
     * @expectedException  \de\any\di\exception\parse
     */
    public function testPropertyParseException() {
        $di= new di();
        $di->bind('istd')->to('diPropertyParseException_std1');
        $di->get('istd');
    }

    public function testIgnoreAnnotationProperty() {
        $di = new di();
        $di->bind('istd')->to('diTestIgnoreAnnotation_property');
        $this->assertNull($di->get('istd')->basic);
        $this->assertNull($di->get('istd')->author);
        $this->assertNull($di->get('istd')->doctrine);
    }

    public function testIgnoreAnnotationMethod() {
        $di = new di();
        $di->bind('istd')->to('diTestIgnoreAnnotation_method');
        $this->assertTrue($di->get('istd')->basic());
        $this->assertTrue($di->get('istd')->author());
        $this->assertTrue($di->get('istd')->doctrine());
    }

    public function testRunable() {
        ob_start();
        $di = new di();
        $di->run(new \diRunable_Basic());

        $this->assertEquals(ob_get_contents(), 'ok!');
        ob_end_clean();
    }

    public function testRunableInjection() {
        $di = new di();
        $di->bind('istd')->to('std1');
        $di->bind('istd')->to('std2')->concern('std2');
        $di->bind('iostd')->to('ostd1');
        $di->bind('iostd')->to('ostd2')->concern('std2');

        $runable = new \diRunable_Inject();

        $di->run($runable);

        $this->assertInstanceOf('std1', $runable->std);
        $this->assertInstanceOf('std2', $runable->std2);
        $this->assertInstanceOf('ostd1', $runable->getIostd());
        $this->assertInstanceOf('ostd2', $runable->getIostd2());
    }

    public function testBasicRepository() {
        $di = new di();
        $this->assertTrue($di->get('istd[]') instanceof \de\any\di\repository\standard);
     }

     public function testBasicRepository2() {
        $di = new di();
        $di->bind('istd')->to('std1');
        $this->assertEquals(0, count($di->get('istd[]')));
     }

    public function testBasicRepository3() {
        $di = new di();
        $di->bind('istd')->to('std1');

        $repository = $di->get('istd[]');
        $repository->append($di->get('istd'));

        $this->assertEquals(1, count($repository));
        $this->assertEquals(0, count($di->get('istd[]')));
     }

    public function testSharedRepository() {
        $di = new di();
        $di->bind('istd[]')->to('\ArrayObject')->shared(true);
        $di->bind('istd')->to('std1');

        $di->get('istd[]')->append($di->get('istd'));

        $this->assertEquals(1, count($di->get('istd[]')));

        $arr = $di->get('istd[]');
        $this->assertInstanceOf('std1', $arr[0]);
     }

     public function testRepositoryConcerns() {
        $di = new di();
        $di->bind('istd[]')->to('\ArrayObject');
        $di->bind('istd[]')->to('\diRepositoryConcern_customArrayObject')->concern('abc');

        $this->assertInstanceOf('\ArrayObject', $di->get('istd[]'));
        $this->assertInstanceOf('\diRepositoryConcern_customArrayObject', $di->get('istd[]', 'abc'));
     }

     public function testRepositoryInjectBasic() {
        $di = new di();
        $instance = new \diRepositoryInject_basic();
        $di->justInject($instance);

        $this->assertInstanceOf('\de\any\di\repository\standard', $instance->repository);
     }

    public function testRepositoryBindBasic() {
        $di = new di();
        $di->bind('istd[]')->to('\ArrayObject');
    }

    public function testDefaultSelfBinding() {
        $di = new di();
        $this->assertTrue($di->get('\de\any\iDi') === $di);
    }

    public function testNamespaceInjectionAnnotationStylesProperties() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_namespace');
        $di->bind('\test\diCodingstyle\iInjected_in_namespace')->to('test\diCodingstyle\injected_in_namespace');

        $this->assertTrue($di->get('constructor_istd')->i1 instanceof \test\diCodingstyle\iInjected_in_namespace);
        $this->assertTrue($di->get('constructor_istd')->i2 instanceof \test\diCodingstyle\iInjected_in_namespace);
    }

    public function testNamespaceInjectionAnnotationStyles2() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_namespace');
        $di->bind('test\diCodingstyle\iInjected_in_namespace')->to('test\diCodingstyle\injected_in_namespace');

        $this->assertTrue($di->get('constructor_istd')->i1 instanceof \test\diCodingstyle\iInjected_in_namespace);
        $this->assertTrue($di->get('constructor_istd')->i2 instanceof \test\diCodingstyle\iInjected_in_namespace);
    }

    public function testNamespaceInjectionAnnotationStylesConstructor() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_namespace');
        $di->bind('test\diCodingstyle\iInjected_in_namespace')->to('test\diCodingstyle\injected_in_namespace');

        $this->assertTrue($di->get('constructor_istd')->i4 instanceof \test\diCodingstyle\iInjected_in_namespace);
    }

    public function testNamespaceInjectionAnnotationStylesConstructor2() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_namespace');
        $di->bind('\test\diCodingstyle\iInjected_in_namespace')->to('test\diCodingstyle\injected_in_namespace');

        $this->assertTrue($di->get('constructor_istd')->i4 instanceof \test\diCodingstyle\iInjected_in_namespace);
    }

    public function testNamespaceInjectionAnnotationStylesMethod() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_namespace');
        $di->bind('test\diCodingstyle\iInjected_in_namespace')->to('test\diCodingstyle\injected_in_namespace');

        $this->assertTrue($di->get('constructor_istd')->i3 instanceof \test\diCodingstyle\iInjected_in_namespace);
    }

    public function testNamespaceInjectionAnnotationStylesMethod2() {
        $di = new di();
        $di->bind('constructor_istd')->to('constructor_namespace');
        $di->bind('\test\diCodingstyle\iInjected_in_namespace')->to('test\diCodingstyle\injected_in_namespace');

        $this->assertTrue($di->get('constructor_istd')->i3 instanceof \test\diCodingstyle\iInjected_in_namespace);
    }
    
    public function testCodingstyleAnnotationParams() {
        $di = new di();
        $di->bind('constructor_istd')->to('test\codingstyle\annotations');
        $di->bind('\test\diCodingstyle\iInjected_in_namespace')->to('test\diCodingstyle\injected_in_namespace');

        $this->assertTrue($di->get('constructor_istd')->i1 instanceof \test\diCodingstyle\iInjected_in_namespace);
        $this->assertEquals($di->get('constructor_istd')->i2, null);
        $this->assertTrue($di->get('constructor_istd')->i3 instanceof \test\diCodingstyle\iInjected_in_namespace);
        $this->assertEquals($di->get('constructor_istd')->i4, null);
    }
}
