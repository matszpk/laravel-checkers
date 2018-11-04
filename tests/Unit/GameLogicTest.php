<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Logic\GameLogic;

class GameLogicTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGoNext()
    {
        $this->assertEquals(11, GameLogic::goNext(0, GameLogic::MOVENE));
    }
}
