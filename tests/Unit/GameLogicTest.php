<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Logic\GameLogic;
use App\Logic\GameException;

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
        $this->assertTrue($gameLogic->isPlayerPiece(24));
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

    // test GameLogic::canMove
    public function testCanMove()
    {
        /*
         * test for whites
         */
        // same
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));

        // one forward
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $state[24] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two forward
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $state[24] = 'w';
        $state[26] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // two forward kings
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $state[24] = 'W';
        $state[26] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // one oponent piece
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $state[24] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // one oponent piece
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $state[26] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two oponent pieces
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $state[24] = 'b';
        $state[26] = 'B';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // one oponent piece and own piece
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $state[24] = 'b';
        $state[26] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));

        // one oponent piece and own piece
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $state[24] = 'w';
        $state[26] = 'B';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));

        // two forward and oponent piece back
        $state = array_fill(0, 100, ' ');
        $state[35] = 'w';
        $state[44] = 'w';
        $state[46] = 'w';
        $state[26] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(35, True));
        // two forward and oponent piece back (not for beat)
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $state[24] = 'w';
        $state[26] = 'w';
        $state[6] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // two forward and oponent piece back
        $state = array_fill(0, 100, ' ');
        $state[35] = 'w';
        $state[44] = 'w';
        $state[46] = 'w';
        $state[24] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(35, True));
        // two forward and two oponent pieces back
        $state = array_fill(0, 100, ' ');
        $state[35] = 'w';
        $state[44] = 'w';
        $state[46] = 'w';
        $state[24] = 'B';
        $state[26] = 'B';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(35, True));

        // same at end
        $state = array_fill(0, 100, ' ');
        $state[93] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(93, True));

        // same at left side
        $state = array_fill(0, 100, ' ');
        $state[40] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(40, True));
        // left side but own piece
        $state = array_fill(0, 100, ' ');
        $state[40] = 'w';
        $state[51] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(40, True));
        // left side but oponent piece
        $state = array_fill(0, 100, ' ');
        $state[40] = 'w';
        $state[51] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(40, True));
        // left side but oponent piece and own
        $state = array_fill(0, 100, ' ');
        $state[40] = 'w';
        $state[51] = 'w';
        $state[31] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(40, True));

        // same at right side
        $state = array_fill(0, 100, ' ');
        $state[49] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(49, True));
        // right side but own piece
        $state = array_fill(0, 100, ' ');
        $state[49] = 'w';
        $state[58] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(49, True));
        // left side but oponent piece
        $state = array_fill(0, 100, ' ');
        $state[49] = 'w';
        $state[58] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(49, True));
        // left side but oponent piece and own
        $state = array_fill(0, 100, ' ');
        $state[49] = 'w';
        $state[58] = 'w';
        $state[38] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(49, True));

        /* test for white king */
        // same
        $state = array_fill(0, 100, ' ');
        $state[15] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // one forward
        $state = array_fill(0, 100, ' ');
        $state[15] = 'W';
        $state[24] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two forward
        $state = array_fill(0, 100, ' ');
        $state[15] = 'W';
        $state[24] = 'w';
        $state[26] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two forward
        $state = array_fill(0, 100, ' ');
        $state[5] = 'W';
        $state[14] = 'w';
        $state[16] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(5, True));
        // two forward, one backward
        $state = array_fill(0, 100, ' ');
        $state[15] = 'W';
        $state[24] = 'w';
        $state[26] = 'w';
        $state[6] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two forward, two backward
        $state = array_fill(0, 100, ' ');
        $state[15] = 'W';
        $state[24] = 'w';
        $state[26] = 'w';
        $state[4] = 'w';
        $state[6] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // one oponent rest is own
        $state = array_fill(0, 100, ' ');
        $state[15] = 'W';
        $state[24] = 'w';
        $state[26] = 'b';
        $state[4] = 'w';
        $state[6] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // one oponent (but not to beat) rest is own
        $state = array_fill(0, 100, ' ');
        $state[15] = 'W';
        $state[24] = 'w';
        $state[26] = 'w';
        $state[4] = 'w';
        $state[6] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // one oponent (but not to beat) rest is own
        $state = array_fill(0, 100, ' ');
        $state[15] = 'W';
        $state[24] = 'w';
        $state[26] = 'w';
        $state[4] = 'b';
        $state[6] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));

        // one oponent rest is own
        $state = array_fill(0, 100, ' ');
        $state[25] = 'W';
        $state[34] = 'w';
        $state[36] = 'w';
        $state[14] = 'w';
        $state[16] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(25, True));
        // one oponent rest is own
        $state = array_fill(0, 100, ' ');
        $state[25] = 'W';
        $state[34] = 'w';
        $state[36] = 'w';
        $state[14] = 'b';
        $state[16] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(25, True));

        // same at end
        $state = array_fill(0, 100, ' ');
        $state[93] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(93, True));

        /*
         * test for blacks
         */
        // same
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));

        // one forward
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $state[74] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // two forward
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $state[74] = 'b';
        $state[76] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // two forward kings
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $state[74] = 'B';
        $state[76] = 'B';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // one oponent piece
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $state[74] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));
        // one oponent piece
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $state[76] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));

        // two oponent pieces
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $state[74] = 'w';
        $state[76] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // one oponent piece and own piece
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $state[74] = 'w';
        $state[76] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));

        // one oponent piece and own piece
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $state[74] = 'b';
        $state[76] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));

        // two forward and oponent piece back
        $state = array_fill(0, 100, ' ');
        $state[75] = 'b';
        $state[64] = 'b';
        $state[66] = 'b';
        $state[86] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));
        // two forward and oponent piece back
        $state = array_fill(0, 100, ' ');
        $state[75] = 'b';
        $state[64] = 'b';
        $state[66] = 'b';
        $state[84] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));
        // two forward and oponent piece back (bot not for beat)
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $state[74] = 'b';
        $state[76] = 'b';
        $state[94] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // two forward and two oponent pieces back
        $state = array_fill(0, 100, ' ');
        $state[75] = 'b';
        $state[64] = 'b';
        $state[66] = 'b';
        $state[84] = 'W';
        $state[86] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));

        // same at end
        $state = array_fill(0, 100, ' ');
        $state[3] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(3, False));

        // same at left side
        $state = array_fill(0, 100, ' ');
        $state[60] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(60, False));
        // left side but own piece
        $state = array_fill(0, 100, ' ');
        $state[60] = 'b';
        $state[51] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(60, False));
        // left side but oponent piece
        $state = array_fill(0, 100, ' ');
        $state[60] = 'b';
        $state[51] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(60, False));
        // left side but oponent piece and own
        $state = array_fill(0, 100, ' ');
        $state[60] = 'b';
        $state[51] = 'b';
        $state[71] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(60, False));

        // same at right side
        $state = array_fill(0, 100, ' ');
        $state[69] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(69, False));
        // right side but own piece
        $state = array_fill(0, 100, ' ');
        $state[69] = 'b';
        $state[58] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(69, False));
        // left side but oponent piece
        $state = array_fill(0, 100, ' ');
        $state[69] = 'b';
        $state[58] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(69, False));
        // left side but oponent piece and own
        $state = array_fill(0, 100, ' ');
        $state[69] = 'b';
        $state[58] = 'b';
        $state[78] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(69, False));

        /* test for black king */
        // same
        $state = array_fill(0, 100, ' ');
        $state[85] = 'B';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // one forward
        $state = array_fill(0, 100, ' ');
        $state[85] = 'B';
        $state[74] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // two forward
        $state = array_fill(0, 100, ' ');
        $state[85] = 'B';
        $state[74] = 'b';
        $state[76] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, True));
        // two forward
        $state = array_fill(0, 100, ' ');
        $state[95] = 'B';
        $state[84] = 'b';
        $state[86] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(95, False));
        // two forward, one backward
        $state = array_fill(0, 100, ' ');
        $state[85] = 'W';
        $state[74] = 'w';
        $state[76] = 'w';
        $state[96] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // two forward, two backward
        $state = array_fill(0, 100, ' ');
        $state[85] = 'B';
        $state[74] = 'b';
        $state[76] = 'b';
        $state[94] = 'b';
        $state[96] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // one oponent rest is own
        $state = array_fill(0, 100, ' ');
        $state[85] = 'B';
        $state[74] = 'b';
        $state[76] = 'w';
        $state[94] = 'b';
        $state[96] = 'b';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // one oponent (but not to beat) rest is own
        $state = array_fill(0, 100, ' ');
        $state[85] = 'B';
        $state[74] = 'b';
        $state[76] = 'b';
        $state[94] = 'b';
        $state[96] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // one oponent (but not to beat) rest is own
        $state = array_fill(0, 100, ' ');
        $state[85] = 'B';
        $state[74] = 'b';
        $state[76] = 'b';
        $state[94] = 'w';
        $state[96] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));

        // one oponent rest is own
        $state = array_fill(0, 100, ' ');
        $state[75] = 'B';
        $state[64] = 'b';
        $state[66] = 'b';
        $state[84] = 'b';
        $state[86] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));
        // one oponent rest is own
        $state = array_fill(0, 100, ' ');
        $state[75] = 'B';
        $state[64] = 'b';
        $state[66] = 'b';
        $state[84] = 'w';
        $state[86] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));

        // same at end
        $state = array_fill(0, 100, ' ');
        $state[3] = 'B';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertTrue($gameLogic->canMove(3, False));
    }

    // test GameLogic::checkGameEnd
    public function testCheckGameEnd()
    {
        $gameLogic = new GameLogic();
        $this->assertEquals(GameLogic::NOTEND, $gameLogic->checkGameEnd());
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', 'w', ' ', ' ', ' ', 'w', ' ', ' ', ' ',
           ' ', ' ', ' ', 'w', ' ', 'w', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $this->assertEquals(GameLogic::PLAYER1WIN, $gameLogic->checkGameEnd());
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', 'w', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'b', ' ', 'b', ' ', ' ', ' ', ' ',
           ' ', ' ', 'b', ' ', ' ', ' ', 'b', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $this->assertEquals(GameLogic::PLAYER2WIN, $gameLogic->checkGameEnd());
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', 'w', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'w', ' ', 'w', ' ', ' ', ' ', ' ',
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', ' ', ' ',
           ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w',
           'b', ' ', 'b', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'w',
           ' ', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ',
           ' ', ' ', ' ', 'b', ' ', 'b', ' ', 'b', ' ', ' ',
           ' ', ' ', ' ', ' ', 'b', ' ', 'b', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ', ' '
        ], True, NULL);
        $this->assertEquals(GameLogic::DRAW, $gameLogic->checkGameEnd());
        // same
        $state = array_fill(0, 100, ' ');
        $state[15] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertEquals(GameLogic::PLAYER1WIN, $gameLogic->checkGameEnd());
        // same
        $state = array_fill(0, 100, ' ');
        $state[85] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertEquals(GameLogic::PLAYER2WIN, $gameLogic->checkGameEnd());

        // same
        $state = array_fill(0, 100, ' ');
        $state[95] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertEquals(GameLogic::PLAYER1WIN, $gameLogic->checkGameEnd());
        // same
        $state = array_fill(0, 100, ' ');
        $state[5] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertEquals(GameLogic::PLAYER2WIN, $gameLogic->checkGameEnd());
    }

    // test GameLogic::findFirstBeatPos
    public function testFindFirstBeatPos()
    {
        $state = array_fill(0, 100, ' ');
        $state[45] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'w';
        $state[56] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        // oponent piece in other direction
        $state = array_fill(0, 100, ' ');
        $state[45] = 'w';
        $state[54] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        // oponent piece in other direction
        $state = array_fill(0, 100, ' ');
        $state[45] = 'w';
        $state[34] = 'B';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'w';
        $state[56] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertEquals([56, 67],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'w';
        $state[56] = 'B';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertEquals([56, 67],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'w';
        $state[67] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'w';
        $state[78] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'W';
        $state[56] = 'w';
        $state[78] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $state = array_fill(0, 100, ' ');
        $state[49] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));
        // if oponent piece in edge
        $state = array_fill(0, 100, ' ');
        $state[48] = 'W';
        $state[59] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        //////////////////////////////////////
        // if king
        $state = array_fill(0, 100, ' ');
        $state[45] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'W';
        $state[56] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'W';
        $state[56] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertEquals([56, 67],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'W';
        $state[67] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertEquals([67, 78],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'W';
        $state[78] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertEquals([78, 89],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        // if your piece precedes oponent piece
        $state = array_fill(0, 100, ' ');
        $state[45] = 'W';
        $state[56] = 'w';
        $state[78] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));
        // if your piece precedes oponent piece
        $state = array_fill(0, 100, ' ');
        $state[45] = 'W';
        $state[67] = 'w';
        $state[78] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));
        // if oponent piece in edge
        $state = array_fill(0, 100, ' ');
        $state[45] = 'W';
        $state[89] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $state = array_fill(0, 100, ' ');
        $state[49] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        /////////////////////////////////////////
        /* blacks */

        $state = array_fill(0, 100, ' ');
        $state[45] = 'b';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESE, False));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'b';
        $state[36] = 'b';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESE, False));

        // oponent piece in other direction
        $state = array_fill(0, 100, ' ');
        $state[45] = 'b';
        $state[34] = 'w';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESE, False));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'b';
        $state[36] = 'w';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertEquals([36, 27],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVESE, False));

        //////////////////////////////////////
        // if king
        $state = array_fill(0, 100, ' ');
        $state[45] = 'B';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESW, True));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'B';
        $state[34] = 'b';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESW, True));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'B';
        $state[34] = 'w';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertEquals([34, 23],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVESW, True));

        $state = array_fill(0, 100, ' ');
        $state[45] = 'B';
        $state[23] = 'w';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->assertEquals([23, 12],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVESW, True));
    }

    // test GameLogic::findBestBeatSeqs
    public function testFindBestBeatSeqs()
    {
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', 'w', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'b', ' ', 'b', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $outStartArray = [];
        $outBeatArray = [];
        $gameLogic->findBestBeatsSeqs(24, $outStartArray, $outBeatArray);
        $this->assertEquals([[24, 42], [24, 2]], $outStartArray);
        $this->assertEquals([[33, 51], [13, 11]], $outBeatArray);

        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', 'b',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'w', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', 'b',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $outStartArray = [];
        $outBeatArray = [];
        $gameLogic->findBestBeatsSeqs(18, $outStartArray, $outBeatArray);
        $this->assertEquals([[18]], $outStartArray);
        $this->assertEquals([[27]], $outBeatArray);

        // no beats
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'w', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $outStartArray = [];
        $outBeatArray = [];
        $gameLogic->findBestBeatsSeqs(18, $outStartArray, $outBeatArray);
        $this->assertEquals([], $outStartArray);
        $this->assertEquals([], $outBeatArray);

        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'b', ' ', 'b', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', 'w', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'b', ' ', 'b', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', ' ', ' ', 'b', ' ', 'b', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $outStartArray = [];
        $outBeatArray = [];
        $gameLogic->findBestBeatsSeqs(44, $outStartArray, $outBeatArray);
        $this->assertEquals([[44, 26, 4, 22, 44, 66],
                             [44, 26, 4, 22, 44, 66],
                             [44, 26, 4, 22, 44, 62],
                             [44, 22, 4, 26, 44, 66],
                             [44, 22, 4, 26, 44, 66],
                             [44, 22, 4, 26, 44, 62]], $outStartArray);
        $this->assertEquals([[35, 15, 13, 33, 55, 77],
                             [35, 15, 13, 33, 55, 75],
                             [35, 15, 13, 33, 53, 71],
                             [33, 13, 15, 35, 55, 77],
                             [33, 13, 15, 35, 55, 75],
                             [33, 13, 15, 35, 53, 71]], $outBeatArray);

        // blacks
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'w', ' ', 'w', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'w', ' ', 'w', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'w', ' ', ' ', ' ', 'w', ' ', 'w', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], False, NULL);
        $outStartArray = [];
        $outBeatArray = [];
        $gameLogic->findBestBeatsSeqs(44, $outStartArray, $outBeatArray);
        $this->assertEquals([[44, 26, 4, 22, 44, 66],
                             [44, 26, 4, 22, 44, 66],
                             [44, 26, 4, 22, 44, 62],
                             [44, 22, 4, 26, 44, 66],
                             [44, 22, 4, 26, 44, 66],
                             [44, 22, 4, 26, 44, 62]], $outStartArray);
        $this->assertEquals([[35, 15, 13, 33, 55, 77],
                             [35, 15, 13, 33, 55, 75],
                             [35, 15, 13, 33, 53, 71],
                             [33, 13, 15, 35, 55, 77],
                             [33, 13, 15, 35, 55, 75],
                             [33, 13, 15, 35, 53, 71]], $outBeatArray);

        // king
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', 'W', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ',
           ' ', ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'b',
           ' ', ' ', 'b', ' ', 'b', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $outStartArray = [];
        $outBeatArray = [];
        $gameLogic->findBestBeatsSeqs(35, $outStartArray, $outBeatArray);
        $this->assertEquals([[35, 71, 93, 75], [35, 71, 93, 66]], $outStartArray);
        $this->assertEquals([[62, 82, 84, 57], [62, 82, 84, 57]], $outBeatArray);


        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', 'W', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ',
           ' ', ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ', 'b',
           ' ', ' ', 'b', ' ', 'b', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $outStartArray = [];
        $outBeatArray = [];
        $gameLogic->findBestBeatsSeqs(35, $outStartArray, $outBeatArray);
        $this->assertEquals([[35, 71]], $outStartArray);
        $this->assertEquals([[62, 82]], $outBeatArray);

        /* black king */
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'w', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', 'B', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'w', ' ', ' ',
           ' ', ' ', 'w', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', 'w', ' ', ' ', ' ', 'w',
           ' ', ' ', 'w', ' ', 'w', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], False, NULL);
        $outStartArray = [];
        $outBeatArray = [];
        $gameLogic->findBestBeatsSeqs(35, $outStartArray, $outBeatArray);
        $this->assertEquals([[35, 71]], $outStartArray);
        $this->assertEquals([[62, 82]], $outBeatArray);
    }

    // test GameLogic::makeMove
    public function testMakeMove()
    {
        $gameLogic = new GameLogic();
        $gameLogic->makeMove(24, 35);
        $this->assertEquals([
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w',
           'w', ' ', 'w', ' ', ' ', ' ', 'w', ' ', 'w', ' ',
           ' ', ' ', ' ', ' ', ' ', 'w', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b',
           'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b'
        ], $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());

        //
        $gameLogic = new GameLogic();
        $gameLogic->makeMove(24, 33);
        $this->assertEquals([
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w',
           'w', ' ', 'w', ' ', ' ', ' ', 'w', ' ', 'w', ' ',
           ' ', ' ', ' ', 'w', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b',
           'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b'
        ], $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());

        // for king
        $state = array_fill(0, 100, ' ');
        $state[75] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(75, 42);
        $expState = array_fill(0, 100, ' ');
        $expState[42] = 'W';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());

        // for king 2
        $state = array_fill(0, 100, ' ');
        $state[75] = 'W';
        $state[31] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(75, 42);
        $expState = array_fill(0, 100, ' ');
        $expState[42] = 'W';
        $expState[31] = 'W';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());

        // for king 3
        $state = array_fill(0, 100, ' ');
        $state[41] = 'W';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(41, 85);
        $expState = array_fill(0, 100, ' ');
        $expState[85] = 'W';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());

        /*
         * Make beats
         */
        $state = array_fill(0, 100, ' ');
        $state[75] = 'w';
        $state[64] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(75, 53);
        $expState = array_fill(0, 100, ' ');
        $expState[53] = 'w';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $state = array_fill(0, 100, ' ');
        $state[75] = 'w';
        $state[86] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(75, 97);
        $expState = array_fill(0, 100, ' ');
        $expState[97] = 'W'; // handle promotion
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $state = array_fill(0, 100, ' ');
        $state[75] = 'w';
        $state[84] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(75, 93);
        $expState = array_fill(0, 100, ' ');
        $expState[93] = 'W'; // handle promotion
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $state = array_fill(0, 100, ' ');
        $state[75] = 'w';
        $state[66] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(75, 57);
        $expState = array_fill(0, 100, ' ');
        $expState[57] = 'w';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $state = array_fill(0, 100, ' ');
        $state[65] = 'w';
        $state[76] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(65, 87);
        $expState = array_fill(0, 100, ' ');
        $expState[87] = 'w';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $state = array_fill(0, 100, ' ');
        $state[75] = 'w';
        $state[86] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(75, 97);
        $expState = array_fill(0, 100, ' ');
        $expState[97] = 'W';    // promotion
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        // double beat (do not change player)
        $state = array_fill(0, 100, ' ');
        $state[65] = 'w';
        $state[54] = 'b';
        $state[32] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(65, 43);
        $expState = array_fill(0, 100, ' ');
        $expState[43] = 'w';
        $expState[32] = 'b';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([54, 43], $gameLogic->getLastBeat());
        // next beat
        $gameLogic->makeMove(43, 21);
        $expState = array_fill(0, 100, ' ');
        $expState[21] = 'w';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        // double beat (do not change player)
        $state = array_fill(0, 100, ' ');
        $state[75] = 'w';
        $state[86] = 'b';
        $state[88] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(75, 97);
        $expState = array_fill(0, 100, ' ');
        $expState[97] = 'w';    // no promotion (not last beat)
        $expState[88] = 'b';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([86, 97], $gameLogic->getLastBeat());
        // next beat
        $gameLogic->makeMove(97, 79);
        $expState = array_fill(0, 100, ' ');
        $expState[79] = 'w';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        // king
        // double beat (do not change player)
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'W', ' ',
           ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ',
           ' ', ' ', ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $gameLogic->makeMove(18, 54);
        $expState = [
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', 'W', ' ', 'b', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ];
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([36, 54], $gameLogic->getLastBeat());
        // next beat
        $gameLogic->makeMove(54, 32);
        $expState = [
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', 'W', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ];
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([43, 32], $gameLogic->getLastBeat());
        // last beat
        $gameLogic->makeMove(32, 10);
        $expState = [
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           'W', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ];
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        /*
         * for blacks
         */
        $state = array_fill(0, 100, ' ');
        $state[75] = 'b';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $gameLogic->makeMove(75, 66);
        $expState = array_fill(0, 100, ' ');
        $expState[66] = 'b';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());

        $state = array_fill(0, 100, ' ');
        $state[75] = 'b';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $gameLogic->makeMove(75, 64);
        $expState = array_fill(0, 100, ' ');
        $expState[64] = 'b';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());

        // from my test
        $gameLogic = GameLogic::fromData([
            'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
            ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w',
            'w', ' ', 'w', ' ', ' ', ' ', 'w', ' ', ' ', ' ',
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'w', ' ', ' ',
            ' ', ' ', ' ', ' ', ' ', ' ', 'w', ' ', ' ', ' ',
            ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ', ' ',
            ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ',
            ' ', 'b', ' ', 'b', ' ', ' ', ' ', ' ', ' ', 'b',
            'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ',
            ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b'
        ], True, NULL);
        $gameLogic->makeMove(46, 64);
    }

    public function testMakeMoveWrongEndPos()
    {
        $gameLogic = new GameLogic();
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Wrong end position');
        $gameLogic->makeMove(24, 38);
    }

    public function testMakeMoveWrongEndPosW2()
    {
        $gameLogic = new GameLogic();
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Wrong end position');
        $gameLogic->makeMove(24, 34);
    }

    public function testMakeMoveNoPlayerPiece()
    {
        $gameLogic = new GameLogic();
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('No player piece in start position');
        $gameLogic->makeMove(25, 35);
    }

    public function testMakeMoveWrongEndPosX()
    {
        $state = array_fill(0, 100, ' ');
        $state[55] = 'b';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Wrong end position');
        $gameLogic->makeMove(55, 45);
    }

    public function testMakeMoveWrongEndPosX2()
    {
        $state = array_fill(0, 100, ' ');
        $state[55] = 'b';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Wrong end position');
        $gameLogic->makeMove(55, 66);
    }

    public function testMakeMoveWrongEndPosX3()
    {
        $state = array_fill(0, 100, ' ');
        $state[55] = 'b';
        $gameLogic = GameLogic::fromData($state, False, NULL);
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Wrong end position');
        $gameLogic->makeMove(55, 33);
    }

    public function testMakeMoveStartOutOfRange1()
    {
        $gameLogic = new GameLogic();
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Move positions out of range');
        $gameLogic->makeMove(-7, 35);
    }

    public function testMakeMoveStartOutOfRange2()
    {
        $gameLogic = new GameLogic();
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Move positions out of range');
        $gameLogic->makeMove(20, 120);
    }

    public function testMakeMoveWrongEndPos2()
    {
        // for king
        $state = array_fill(0, 100, ' ');
        $state[75] = 'W';
        $state[42] = 'w';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Wrong end position');
        $gameLogic->makeMove(75, 42);
    }

    public function testMakeMoveNoFreeField()
    {
        $gameLogic = new GameLogic();
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('No free field in end position');
        $gameLogic->makeMove(13, 24);
    }

    public function testMakeMoveNoNextBeat()
    {
        // double beat (do not change player)
        $state = array_fill(0, 100, ' ');
        $state[65] = 'w';
        $state[54] = 'b';
        $state[32] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(65, 43);
        $expState = array_fill(0, 100, ' ');
        $expState[43] = 'w';
        $expState[32] = 'b';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([54, 43], $gameLogic->getLastBeat());
        // next beat
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Move is not a mandatory beat');
        $gameLogic->makeMove(43, 52);
    }

    public function testMakeMoveNoNextBeat2()
    {
        // double beat (do not change player)
        $state = array_fill(0, 100, ' ');
        $state[65] = 'w';
        $state[11] = 'w';  // not this same
        $state[54] = 'b';
        $state[32] = 'b';
        $gameLogic = GameLogic::fromData($state, True, NULL);
        $gameLogic->makeMove(65, 43);
        $expState = array_fill(0, 100, ' ');
        $expState[11] = 'w';
        $expState[43] = 'w';
        $expState[32] = 'b';
        $this->assertEquals($expState, $gameLogic->getState());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([54, 43], $gameLogic->getLastBeat());
        // next beat
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Move is not a mandatory beat');
        $gameLogic->makeMove(11, 20);
    }

    public function testMakeMoveNoMandatory()
    {
        // king
        // double beat (do not change player)
        $gameLogic = GameLogic::fromData([
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', 'W', ' ',
           ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ',
           ' ', ' ', ' ', 'b', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', 'b', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '
        ], True, NULL);
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Move is not a mandatory beat');
        $gameLogic->makeMove(18, 45);
    }
}
