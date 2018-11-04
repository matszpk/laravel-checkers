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
}
