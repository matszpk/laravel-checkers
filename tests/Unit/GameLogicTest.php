<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Logic\GameLogic;

class GameLogicTest extends TestCase
{
    // test GameLogic::goNext()
    public function testGoNext()
    {
        // north-east
        $this->assertEquals(11, GameLogic::goNext(0, GameLogic::MOVENE));
        $this->assertEquals(39, GameLogic::goNext(28, GameLogic::MOVENE));
        $this->assertEquals(86, GameLogic::goNext(75, GameLogic::MOVENE));
        $this->assertEquals(74, GameLogic::goNext(63, GameLogic::MOVENE));
        $this->assertEquals(-1, GameLogic::goNext(49, GameLogic::MOVENE));
        $this->assertEquals(-1, GameLogic::goNext(93, GameLogic::MOVENE));
        $this->assertEquals(-1, GameLogic::goNext(99, GameLogic::MOVENE));

        // north-west
        $this->assertEquals(10, GameLogic::goNext(1, GameLogic::MOVENW));
        $this->assertEquals(47, GameLogic::goNext(38, GameLogic::MOVENW));
        $this->assertEquals(60, GameLogic::goNext(51, GameLogic::MOVENW));
        $this->assertEquals(96, GameLogic::goNext(87, GameLogic::MOVENW));
        $this->assertEquals(-1, GameLogic::goNext(40, GameLogic::MOVENW));
        $this->assertEquals(-1, GameLogic::goNext(97, GameLogic::MOVENW));
        $this->assertEquals(-1, GameLogic::goNext(90, GameLogic::MOVENW));

        // south-east
        $this->assertEquals(1, GameLogic::goNext(10, GameLogic::MOVESE));
        $this->assertEquals(64, GameLogic::goNext(73, GameLogic::MOVESE));
        $this->assertEquals(49, GameLogic::goNext(58, GameLogic::MOVESE));
        $this->assertEquals(9, GameLogic::goNext(18, GameLogic::MOVESE));
        $this->assertEquals(-1, GameLogic::goNext(6, GameLogic::MOVESE));
        $this->assertEquals(-1, GameLogic::goNext(59, GameLogic::MOVESE));
        $this->assertEquals(-1, GameLogic::goNext(9, GameLogic::MOVESE));

        // south-west
        $this->assertEquals(0, GameLogic::goNext(11, GameLogic::MOVESW));
        $this->assertEquals(62, GameLogic::goNext(73, GameLogic::MOVESW));
        $this->assertEquals(47, GameLogic::goNext(58, GameLogic::MOVESW));
        $this->assertEquals(35, GameLogic::goNext(46, GameLogic::MOVESW));
        $this->assertEquals(-1, GameLogic::goNext(40, GameLogic::MOVESW));
        $this->assertEquals(-1, GameLogic::goNext(7, GameLogic::MOVESW));
        $this->assertEquals(-1, GameLogic::goNext(0, GameLogic::MOVESW));
    }

    // test GameLogic::isPlayerPiece
    public function testIsPlayerPiece()
    {
        $gameLogic = new GameLogic();
        $this->assertFalse($gameLogic->isPlayerPiece(71));
        $this->assertFalse($gameLogic->isPlayerPiece(72));
        $this->assertTrue($gameLogic->isPlayerPiece(11));
        $state = [
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', 'w', ' ', 'W', ' ', 'w', ' ', 'w', ' ', 'w',
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b',
           'b', ' ', 'b', ' ', 'B', ' ', 'b', ' ', 'b', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b'
        ];
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->isPlayerPiece(84));
        $this->assertFalse($gameLogic->isPlayerPiece(85));
        $this->assertTrue($gameLogic->isPlayerPiece(13));

        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertFalse($gameLogic->isPlayerPiece(13));
        $this->assertFalse($gameLogic->isPlayerPiece(15));
        $this->assertFalse($gameLogic->isPlayerPiece(44));
        $this->assertTrue($gameLogic->isPlayerPiece(73));
    }

    // test GameLogic::isOponentPiece
    public function testIsOponentPiece()
    {
        $gameLogic = new GameLogic();
        $this->assertTrue($gameLogic->isOponentPiece(71));
        $this->assertFalse($gameLogic->isOponentPiece(72));
        $this->assertFalse($gameLogic->isOponentPiece(11));
        $state = [
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', 'w', ' ', 'W', ' ', 'w', ' ', 'w', ' ', 'w',
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b',
           'b', ' ', 'b', ' ', 'B', ' ', 'b', ' ', 'b', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b'
        ];
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->isOponentPiece(84));
        $this->assertFalse($gameLogic->isOponentPiece(85));
        $this->assertFalse($gameLogic->isOponentPiece(13));

        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertTrue($gameLogic->isOponentPiece(13));
        $this->assertTrue($gameLogic->isOponentPiece(15));
        $this->assertFalse($gameLogic->isOponentPiece(44));
        $this->assertFalse($gameLogic->isOponentPiece(73));
    }

    // test GameLogic::isGivenPlayerPiece
    public function testIsGivenPlayerPiece()
    {
        $gameLogic = new GameLogic();
        $this->assertTrue($gameLogic->isGivenPlayerPiece(71, False));
        $this->assertFalse($gameLogic->isGivenPlayerPiece(72, False));
        $this->assertFalse($gameLogic->isGivenPlayerPiece(11, False));
        $state = [
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', 'w', ' ', 'W', ' ', 'w', ' ', 'w', ' ', 'w',
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b',
           'b', ' ', 'b', ' ', 'B', ' ', 'b', ' ', 'b', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b'
        ];
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->isGivenPlayerPiece(84, False));
        $this->assertFalse($gameLogic->isGivenPlayerPiece(85, False));
        $this->assertFalse($gameLogic->isGivenPlayerPiece(13, False));

        $this->assertTrue($gameLogic->isGivenPlayerPiece(13, True));
        $this->assertTrue($gameLogic->isGivenPlayerPiece(15, True));
        $this->assertFalse($gameLogic->isGivenPlayerPiece(44, True));
        $this->assertFalse($gameLogic->isGivenPlayerPiece(73, True));
    }

    // test GameLogic::isKing
    public function testIsKing()
    {
        $state = [
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', 'w', ' ', 'W', ' ', 'w', ' ', 'w', ' ', 'w',
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b',
           'b', ' ', 'b', ' ', 'B', ' ', 'b', ' ', 'b', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b'
        ];
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->isKing(84));
        $this->assertFalse($gameLogic->isKing(85));
        $this->assertTrue($gameLogic->isKing(13));
        $this->assertFalse($gameLogic->isKing(15));

        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertTrue($gameLogic->isKing(84));
        $this->assertFalse($gameLogic->isKing(85));
        $this->assertFalse($gameLogic->isKing(13));
        $this->assertFalse($gameLogic->isKing(15));
    }

    // test GameLogic::isGivenKing
    public function testIsGivenKing()
    {
        $state = [
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', 'w', ' ', 'W', ' ', 'w', ' ', 'w', ' ', 'w',
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b',
           'b', ' ', 'b', ' ', 'B', ' ', 'b', ' ', 'b', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b'
        ];
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->isGivenKing(84, True));
        $this->assertFalse($gameLogic->isGivenKing(85, True));
        $this->assertTrue($gameLogic->isGivenKing(13, True));
        $this->assertFalse($gameLogic->isGivenKing(15, True));

        $this->assertTrue($gameLogic->isGivenKing(84, False));
        $this->assertFalse($gameLogic->isGivenKing(85, False));
        $this->assertFalse($gameLogic->isGivenKing(13, False));
        $this->assertFalse($gameLogic->isGivenKing(15, False));
    }

    public function handlePromotionEqual($pos, $player1, $initial, $expected)
    {
        $state = array_fill(0, 100, ' ');
        $state[$pos] = $initial;
        $gameLogic = GameLogic::fromData($state, $player1, NULL);
        $gameLogic->handlePromotion($pos);
        $this->assertEquals($expected, $gameLogic->getState()[$pos]);
    }

    // test GameLogic::handlePromotion (to king)
    public function testHandlePromotion()
    {
        $this->handlePromotionEqual(85, True, 'w', 'w');
        $this->handlePromotionEqual(5, True, 'w', 'w');
        $this->handlePromotionEqual(95, True, 'w', 'W');

        $this->handlePromotionEqual(15, False, 'b', 'b');
        $this->handlePromotionEqual(95, False, 'b', 'b');
        $this->handlePromotionEqual(5, False, 'b', 'B');
    }
}
