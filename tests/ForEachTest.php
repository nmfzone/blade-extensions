<?php

use Radic\BladeExtensions\Extensions\ForEachManager;
use Radic\BladeExtensions\Core\LoopStackInterface;
use \Mockery as m;

class ForEachTest extends Orchestra\Testbench\TestCase
{
    protected $loopData;
    protected $loopData2;
    public function setUp()
    {
        parent::setUp();
        $this->loopData = array(
            array('name' => 'micheal', 'age' => 55, 'gender' => 'male'),
            array('name' => 'anton', 'age' => 12, 'gender' => 'male'),
            array('name' => 'claire', 'age' => 45, 'gender' => 'female'),
            array('name' => 'thomas', 'age' => 22, 'gender' => 'male'),
            array('name' => 'richard', 'age' => 34, 'gender' => 'male'),
            array('name' => 'donal', 'age' => 85, 'gender' => 'does-not-know')
        );
        $this->loopData2 = array(
            array('name' => 'travis', 'age' => 35, 'gender' => 'male'),
            array('name' => 'mike', 'age' => 22, 'gender' => 'male'),
            array('name' => 'robin', 'age' => 15, 'gender' => 'male'),
            array('name' => 'lisbeth', 'age' => 52, 'gender' => 'female'),
            array('name' => 'mikael', 'age' => 24, 'gender' => 'male'),
            array('name' => 'strump', 'age' => 35, 'gender' => 'does-not-know')
        );
        ForEachManager::reset();
    }



    public function testNewLoopCreatesStack()
    {
        ForEachManager::newLoop($this->loopData);
        $stack = ForEachManager::getStack();
        $this->assertTrue(!empty($stack) && count($stack) == 1);
    }

    public function testResetEmptiesStacks()
    {
        ForEachManager::newLoop($this->loopData);
        $stack = ForEachManager::getStack();
        $this->assertTrue(!empty($stack) && count($stack) == 1);
        ForEachManager::reset();
        $stack = ForEachManager::getStack();
        $this->assertTrue(empty($stack), 'stack should be empty array');
    }


    public function testLooping()
    {
        $total = count($this->loopData);
        ForEachManager::newLoop($this->loopData);
        foreach($this->loopData as $key => $val)
        {
            $loop = ForEachManager::loop();

            $this->assertTrue($loop instanceof LoopStackInterface);

            // check for valid loop data
            $this->assertTrue($loop->index == $key, 'index');
            $this->assertTrue($loop->index1 == $key + 1, '1 based index');
            $this->assertTrue($loop->revindex == ($total - 1) - $key, 'revindex');
            $this->assertTrue($loop->revindex1 == $total - $key, '1 based revindex');

            if($key == 0)
            {
                $this->assertTrue($loop->length == $total, 'total');

                $this->assertTrue($loop->first, 'first should be true');
                $this->assertNotTrue($loop->last, 'last should be false');

                $this->assertTrue($loop->even, 'even should be true');
                $this->assertNotTrue($loop->odd, 'odd should be false');
            }
            elseif($key == 1)
            {
                $this->assertTrue($loop->odd, 'odd should be true');
                $this->assertNotTrue($loop->even, 'even should be false');

                $this->assertNotTrue($loop->first, 'first should be false');
                $this->assertNotTrue($loop->last, 'last should be false');
            }
            elseif($key == $total - 1)
            {
                $this->assertTrue($loop->last, 'last should be true');
                $this->assertNotTrue($loop->first, 'last should be false');
            }
            else
            {
                $this->assertNotTrue($loop->first, 'first should be false');
                $this->assertNotTrue($loop->last, 'last should be false');
            }

            ForEachManager::looped();
        }
        ForEachManager::endLoop($loop);

        $this->assertNull($loop, 'End of loop stack should be null but is not null');
    }

    public function testLoopStack()
    {
        ForEachManager::newLoop($this->loopData);
        foreach($this->loopData as $key => $val)
        {
            $loop = ForEachManager::loop();

            $index = $loop->index;

            // Create child loop with loopdata2
            ForEachManager::newLoop($this->loopData2);
            foreach($this->loopData2 as $childKey => $childVal)
            {
                $loop = ForEachManager::loop();

                $this->assertTrue($loop->index == $childKey);

                ForEachManager::looped();
            }
            ForEachManager::endLoop($loop);
            // end child loop

            // Check if $loop is back to this loop and not child
            $this->assertTrue($loop->index == $index);


            $this->assertTrue($loop->index == $key);

            ForEachManager::looped();
        }
        ForEachManager::endLoop($loop);

        $this->assertNull($loop, 'End of loop stack should be null but is not null');
    }


}