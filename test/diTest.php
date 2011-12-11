<?php

namespace de\any\di\test;
use de\any\di;
use de\any\di\binder;

class DITest extends \PHPUnit_Framework_TestCase {

    public function testDiSet() {
        $di = new di();
        $di->bind('\diTest\istd')->to('\diTest\std1');

        $this->assertInstanceOf('\diTest\std1', $di->get('\diTest\istd'));
        return $di;
    }

    /**
     * @depends testDiSet
     */
    public function testDiOverwrite($di) {
        $di->bind('\diTest\istd')->to('\diTest\std2');

        $this->assertInstanceOf('\diTest\std2', $di->get('\diTest\istd'));
    }

    public function testDIConcern() {
        $di = new di();
        $di->bind('\diTest\istd')->to('\diTest\std1');
        $di->bind('\diTest\istd')->to('\diTest\std2')->concern('abc');

        $this->assertInstanceOf('\diTest\std2', $di->get('\diTest\istd', 'abc'));#
        $this->assertInstanceOf('\diTest\std1', $di->get('\diTest\istd'));
    }
    

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInterfaceDoesNotExists() {
        $di = new di();
        $di->bind('UNKNOWN')->to('\diTest\std1');

        $this->assertInstanceOf('\diTest\std1', $di->get('UNKNOWN'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBindingDoesNotExists() {
        $di = new di();
        $di->get('UNKNOWN');
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testImplementationDoesNotExists() {
        $di = new di();
        $di->bind('\diTest\istd')->to('UNKNOWN');
        $di->get('\diTest\istd');
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
       $di->bind('\diTest\istd')->to('\diTest\std1');

       $this->assertTrue($di->get('\diTest\istd') !== $di->get('\diTest\istd'));
    }

    public function testIsShared() {
       $di = new di();
       $di->bind('\diTest\istd')->to('\diTest\std1')->shared(true);

       $this->assertTrue($di->get('\diTest\istd') === $di->get('\diTest\istd'));
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
        $di->bind('\diTest\istd')->to('diDecorateStd1');
        $di->bind('\diTest\istd')->to('diDecorateDecorator1')->decorated(true);

        $this->assertEquals($di->get('\diTest\istd')->foo(), 'foo, decorated1!');
    }

    public function testDecorateMultiple() {
        $di = new di();
        $di->bind('\diTest\istd')->to('diDecorateStd1');
        $di->bind('\diTest\istd')->to('diDecorateDecorator1')->decorated(true);
        $di->bind('\diTest\istd')->to('diDecorateDecorator2')->decorated(true);

        $this->assertEquals($di->get('\diTest\istd')->foo(), 'foo, decorated1!, decorated2!');
    }

    public function testDecoratedNested() {
        $di = new di();
        $di->bind('\diTest\istd')->to('diDecorateStd1');
        $di->bind('\diTest\istd')->to('diDecorateDecorator1')->decorated(true);
        $di->bind('\diTest\istd')->to('diDecorateDecoratorNested1')->decorated(true);
        $di->bind('nested_inestedservice1')->to('nested_nestedservice1');

        $this->assertInstanceOf('nested_inestedservice1', $di->get('\diTest\istd')->getService());
        $this->assertEquals($di->get('\diTest\istd')->getService()->identify(), 'nested_nestedservice1');
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
        $di->bind('\sharedDecorators\iBase1')->to('\sharedDecorators\base1');
        $di->bind('\sharedDecorators\iBase1')->decoratedWith('\sharedDecorators\base1_decorator');
        $decorator = $di->get('\sharedDecorators\iBase1')->getService();

        $this->assertInstanceOf('\sharedDecorators\base1_decorator', $decorator);
        $this->assertTrue($di->get('\sharedDecorators\iBase1')->getService() !== $decorator);
    }

    function testSharedDecorator() {
        $di = new di();
        $di->bind('\sharedDecorators\iBase1')->to('\sharedDecorators\base1');
        $di->bind('\sharedDecorators\iBase1')->decoratedWith('\sharedDecorators\base1_decorator')->shared(true);
        $decorator = $di->get('\sharedDecorators\iBase1')->getService();

        $this->assertInstanceOf('\sharedDecorators\base1_decorator', $decorator);
        $this->assertTrue($di->get('\sharedDecorators\iBase1')->getService() === $decorator);
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
        $std = new \diTest\std1();
        $di->bind('\diTest\istd')->to($std);

        $this->assertTrue($di->get('\diTest\istd') === $std);
        return $di;
    }

    /**
     * @depends testBindInstances
     */
    public function testBindSharedInstances($di) {
        $std = new \diTest\std1();
        $di->bind('\diTest\istd')->to($std)->shared(false);

        $this->assertTrue($di->get('\diTest\istd') !== $std);
        $this->assertInstanceOf('\\diTest\std1', $di->get('\diTest\istd'));
    }

    public function testInjectDiItself() {
        $di = new di();
        $di->bind('\de\any\iDi')->to($di);

        $this->assertTrue($di === $di->get('\de\any\iDi'));
    }

    public function testParam() {
        $di = new di();
        $di->bind('\diTest\istd')->to('\diParam\standard');
        $di->bind('\diTest\iostd')->to('\diParam\standard_injected');

        $this->assertInstanceOf('\diParam\standard', $di->get('\diTest\istd'));
        $this->assertInstanceOf('\diParam\standard_injected', $di->get('\diTest\istd')->service);
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
        $di->bind('\diTest\istd')->to('\diParam\concern');
        $di->bind('\diTest\iostd')->to('\diParam\standard_injected');
        $di->bind('\diTest\iostd')->to('\diTest\ostd1')->concern('abc');

        $this->assertInstanceOf('\diParam\concern', $di->get('\diTest\istd'));
        $this->assertInstanceOf('\diParam\standard_injected', $di->get('\diTest\istd')->service);
        $this->assertInstanceOf('\diTest\ostd1', $di->get('\diTest\istd')->service_concern);
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
        $di->bind('\diTest\istd')->to('\diPropertyParseException\std1');
        $di->get('\diTest\istd');
    }

    public function testIgnoreAnnotationProperty() {
        $di = new di();
        $di->bind('\diTest\istd')->to('diTestIgnoreAnnotation\property');
        $this->assertNull($di->get('\diTest\istd')->basic);
        $this->assertNull($di->get('\diTest\istd')->author);
        $this->assertNull($di->get('\diTest\istd')->doctrine);
    }

    public function testIgnoreAnnotationMethod() {
        $di = new di();
        $di->bind('\diTest\istd')->to('diTestIgnoreAnnotation\method');
        $this->assertTrue($di->get('\diTest\istd')->basic());
        $this->assertTrue($di->get('\diTest\istd')->author());
        $this->assertTrue($di->get('\diTest\istd')->doctrine());
    }

    public function testRunable() {
        ob_start();
        $di = new di();
        $di->run(new \diRunable\Basic());

        $this->assertEquals(ob_get_contents(), 'ok!');
        ob_end_clean();
    }

    public function testRunableInjection() {
        $di = new di();
        $di->bind('\diTest\istd')->to('\diTest\std1');
        $di->bind('\diTest\istd')->to('\diTest\std2')->concern('std2');
        $di->bind('\diTest\iostd')->to('\diTest\ostd1');
        $di->bind('\diTest\iostd')->to('\diTest\ostd2')->concern('std2');

        $runable = new \diRunable\Inject();

        $di->run($runable);

        $this->assertInstanceOf('\diTest\std1', $runable->std);
        $this->assertInstanceOf('\diTest\std2', $runable->std2);
        $this->assertInstanceOf('\diTest\ostd1', $runable->getIostd());
        $this->assertInstanceOf('\diTest\ostd2', $runable->getIostd2());
    }

    public function testBasicRepository() {
        $di = new di();
        $this->assertTrue($di->get('\diTest\istd[]') instanceof \de\any\di\repository\standard);
     }

     public function testBasicRepository2() {
        $di = new di();
        $di->bind('\diTest\istd')->to('\diTest\std1');
        $this->assertEquals(0, count($di->get('\diTest\istd[]')));
     }

    public function testBasicRepository3() {
        $di = new di();
        $di->bind('\diTest\istd')->to('\diTest\std1');

        $repository = $di->get('\diTest\istd[]');
        $repository->append($di->get('\diTest\istd'));

        $this->assertEquals(1, count($repository));
        $this->assertEquals(0, count($di->get('\diTest\istd[]')));
     }

    public function testSharedRepository() {
        $di = new di();
        $di->bind('\diTest\istd[]')->to('\ArrayObject')->shared(true);
        $di->bind('\diTest\istd')->to('\diTest\std1');

        $di->get('\diTest\istd[]')->append($di->get('\diTest\istd'));

        $this->assertEquals(1, count($di->get('\diTest\istd[]')));

        $arr = $di->get('\diTest\istd[]');
        $this->assertInstanceOf('\diTest\std1', $arr[0]);
     }

     public function testRepositoryConcerns() {
        $di = new di();
        $di->bind('\diTest\istd[]')->to('\ArrayObject');
        $di->bind('\diTest\istd[]')->to('\diRepositoryConcern\customArrayObject')->concern('abc');

        $this->assertInstanceOf('\ArrayObject', $di->get('\diTest\istd[]'));
        $this->assertInstanceOf('\diRepositoryConcern\customArrayObject', $di->get('\diTest\istd[]', 'abc'));
     }

     public function testRepositoryInjectBasic() {
        $di = new di();
        $instance = new \diRepositoryInject\basic();
        $di->justInject($instance);

        $this->assertInstanceOf('\de\any\di\repository\standard', $instance->repository);
     }

    public function testRepositoryBindBasic() {
        $di = new di();
        $di->bind('\diTest\istd[]')->to('\ArrayObject');
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

    public function testImplementationBindingGetInstance() {
        $di = new di();
        $this->assertInstanceOf('\diImmplementationBinding\impl', $di->get('\diImmplementationBinding\impl'));
    }

    public function testImplementationBindingOverwritePropertyBinding() {
        $di = new di();
        $di->bind('\diImmplementationBinding\impl')->to('\diImmplementationBinding\extends_impl')->setIsClass(true);
        $this->assertInstanceOf('\diImmplementationBinding\impl', $di->get('\diImmplementationBinding\extends_impl'));
    }

    public function testImplementationBindingPropertyInjection() {
        $di = new di();
        $this->assertInstanceOf('\diImmplementationBinding\impl', $di->get('\diImmplementationBinding\property')->service);
    }

    public function testImplementationBindingConstructorInjection() {
        $di = new di();
        $this->assertInstanceOf('\diImmplementationBinding\impl', $di->get('\diImmplementationBinding\constructor')->service);
    }

    public function testImplementationBindingOverwriteConstructorBinding() {
        $di = new di();
        $di->bind('\diImmplementationBinding\impl')->to('\diImmplementationBinding\extends_impl')->setIsClass(true);
        $this->assertInstanceOf('\diImmplementationBinding\extends_impl', $di->get('\diImmplementationBinding\constructor')->service);
    }

    public function testImplementationBindingSetterBinding() {
        $di = new di();
        $this->assertInstanceOf('\diImmplementationBinding\impl', $di->get('\diImmplementationBinding\setter')->service);
    }

    public function testImplementationBindingImplicitShared() {
        $c = new \diImmplementationBinding\extends_stdClass();

        $di = new di();
        $di->bind('\stdClass')->to($c)->isClass(true);
        $this->assertTrue($c === $di->get('\stdClass'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testImplementationBindingPropertyBadExtend() {
        $di = new di();
        $di->bind('\diImmplementationBinding\impl')->to('\diImmplementationBinding\impl2')->setIsClass(true);
        $this->assertInstanceOf('\diImmplementationBinding\impl2', $di->get('\diImmplementationBinding\property')->service);
    }

}
