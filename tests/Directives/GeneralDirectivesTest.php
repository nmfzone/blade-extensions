<?php namespace Radic\Tests\BladeExtensions\Directives;

use Illuminate\Html\HtmlServiceProvider;
use Mockery as m;
use Radic\BladeExtensions\BladeExtensionsServiceProvider;
use Radic\Testing\Traits\BladeViewTestingTrait;
use Radic\Tests\BladeExtensions\TestCase;

/**
 * Class ViewTest
 *
 * @author     Robin Radic
 *
 */
class GeneralDirectivesTest extends TestCase
{
    use BladeViewTestingTrait;

    public function setUp()
    {
        parent::setUp();
        $this->loadViewTesting();
        $this->registerHtml();
        $this->registerBlade();
    }

    public function testSet()
    {
        $this->view->make(
            'set',
            [
                'dataString'        => 'hello',
                'dataArray'         => $this->getData()->array,
                'dataClassInstance' => $this->getData(),
                'dataClassName'     => 'TestData'
            ]
        )->render();
    }


    public function testForeach()
    {
        $this->view->make(
            'foreach',
            [
                'dataClass' => $this->getData(),
                'array'     => $this->getData()->array,
                'getArray'  => $this->getData()->getArrayGetterFn()
            ]
        )->render();
    }


    public function testPartials()
    {
        $partials = $this->view->make('partials')->render();
        $this->assertEquals("okokok", str_replace("\n", '', $partials));
    }
}