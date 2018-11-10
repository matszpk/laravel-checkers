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
        $board = [
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
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->isPlayerPiece(84));
        $this->assertFalse($gameLogic->isPlayerPiece(85));
        $this->assertTrue($gameLogic->isPlayerPiece(13));

        $gameLogic = GameLogic::fromData($board, False, NULL);
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
        $board = [
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
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->isOponentPiece(84));
        $this->assertFalse($gameLogic->isOponentPiece(85));
        $this->assertFalse($gameLogic->isOponentPiece(13));

        $gameLogic = GameLogic::fromData($board, False, NULL);
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
        $board = [
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
        $gameLogic = GameLogic::fromData($board, True, NULL);
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
        $board = [
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
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->isKing(84));
        $this->assertFalse($gameLogic->isKing(85));
        $this->assertTrue($gameLogic->isKing(13));
        $this->assertFalse($gameLogic->isKing(15));

        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->assertTrue($gameLogic->isKing(84));
        $this->assertFalse($gameLogic->isKing(85));
        $this->assertFalse($gameLogic->isKing(13));
        $this->assertFalse($gameLogic->isKing(15));
    }

    // test GameLogic::isGivenKing
    public function testIsGivenKing()
    {
        $board = [
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
        $gameLogic = GameLogic::fromData($board, True, NULL);
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
        $board = array_fill(0, 100, ' ');
        $board[$pos] = $initial;
        $gameLogic = GameLogic::fromData($board, $player1, NULL);
        $gameLogic->handlePromotion($pos);
        $this->assertEquals($expected, $gameLogic->getBoard()[$pos]);
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
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));

        // one forward
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $board[24] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two forward
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $board[24] = 'w';
        $board[26] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // two forward kings
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $board[24] = 'W';
        $board[26] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // one oponent piece
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $board[24] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // one oponent piece
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $board[26] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two oponent pieces
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $board[24] = 'b';
        $board[26] = 'B';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // one oponent piece and own piece
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $board[24] = 'b';
        $board[26] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));

        // one oponent piece and own piece
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $board[24] = 'w';
        $board[26] = 'B';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));

        // two forward and oponent piece back
        $board = array_fill(0, 100, ' ');
        $board[35] = 'w';
        $board[44] = 'w';
        $board[46] = 'w';
        $board[26] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(35, True));
        // two forward and oponent piece back (not for beat)
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $board[24] = 'w';
        $board[26] = 'w';
        $board[6] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // two forward and oponent piece back
        $board = array_fill(0, 100, ' ');
        $board[35] = 'w';
        $board[44] = 'w';
        $board[46] = 'w';
        $board[24] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(35, True));
        // two forward and two oponent pieces back
        $board = array_fill(0, 100, ' ');
        $board[35] = 'w';
        $board[44] = 'w';
        $board[46] = 'w';
        $board[24] = 'B';
        $board[26] = 'B';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(35, True));

        // same at end
        $board = array_fill(0, 100, ' ');
        $board[93] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(93, True));

        // same at left side
        $board = array_fill(0, 100, ' ');
        $board[40] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(40, True));
        // left side but own piece
        $board = array_fill(0, 100, ' ');
        $board[40] = 'w';
        $board[51] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(40, True));
        // left side but oponent piece
        $board = array_fill(0, 100, ' ');
        $board[40] = 'w';
        $board[51] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(40, True));
        // left side but oponent piece and own
        $board = array_fill(0, 100, ' ');
        $board[40] = 'w';
        $board[51] = 'w';
        $board[31] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(40, True));

        // same at right side
        $board = array_fill(0, 100, ' ');
        $board[49] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(49, True));
        // right side but own piece
        $board = array_fill(0, 100, ' ');
        $board[49] = 'w';
        $board[58] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(49, True));
        // left side but oponent piece
        $board = array_fill(0, 100, ' ');
        $board[49] = 'w';
        $board[58] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(49, True));
        // left side but oponent piece and own
        $board = array_fill(0, 100, ' ');
        $board[49] = 'w';
        $board[58] = 'w';
        $board[38] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(49, True));

        /* test for white king */
        // same
        $board = array_fill(0, 100, ' ');
        $board[15] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // one forward
        $board = array_fill(0, 100, ' ');
        $board[15] = 'W';
        $board[24] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two forward
        $board = array_fill(0, 100, ' ');
        $board[15] = 'W';
        $board[24] = 'w';
        $board[26] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two forward
        $board = array_fill(0, 100, ' ');
        $board[5] = 'W';
        $board[14] = 'w';
        $board[16] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(5, True));
        // two forward, one backward
        $board = array_fill(0, 100, ' ');
        $board[15] = 'W';
        $board[24] = 'w';
        $board[26] = 'w';
        $board[6] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // two forward, two backward
        $board = array_fill(0, 100, ' ');
        $board[15] = 'W';
        $board[24] = 'w';
        $board[26] = 'w';
        $board[4] = 'w';
        $board[6] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // one oponent rest is own
        $board = array_fill(0, 100, ' ');
        $board[15] = 'W';
        $board[24] = 'w';
        $board[26] = 'b';
        $board[4] = 'w';
        $board[6] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(15, True));
        // one oponent (but not to beat) rest is own
        $board = array_fill(0, 100, ' ');
        $board[15] = 'W';
        $board[24] = 'w';
        $board[26] = 'w';
        $board[4] = 'w';
        $board[6] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));
        // one oponent (but not to beat) rest is own
        $board = array_fill(0, 100, ' ');
        $board[15] = 'W';
        $board[24] = 'w';
        $board[26] = 'w';
        $board[4] = 'b';
        $board[6] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(15, True));

        // one oponent rest is own
        $board = array_fill(0, 100, ' ');
        $board[25] = 'W';
        $board[34] = 'w';
        $board[36] = 'w';
        $board[14] = 'w';
        $board[16] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(25, True));
        // one oponent rest is own
        $board = array_fill(0, 100, ' ');
        $board[25] = 'W';
        $board[34] = 'w';
        $board[36] = 'w';
        $board[14] = 'b';
        $board[16] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(25, True));

        // same at end
        $board = array_fill(0, 100, ' ');
        $board[93] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(93, True));

        /*
         * test for blacks
         */
        // same
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));

        // one forward
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $board[74] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // two forward
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $board[74] = 'b';
        $board[76] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // two forward kings
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $board[74] = 'B';
        $board[76] = 'B';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // one oponent piece
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $board[74] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));
        // one oponent piece
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $board[76] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));

        // two oponent pieces
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $board[74] = 'w';
        $board[76] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // one oponent piece and own piece
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $board[74] = 'w';
        $board[76] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));

        // one oponent piece and own piece
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $board[74] = 'b';
        $board[76] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));

        // two forward and oponent piece back
        $board = array_fill(0, 100, ' ');
        $board[75] = 'b';
        $board[64] = 'b';
        $board[66] = 'b';
        $board[86] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));
        // two forward and oponent piece back
        $board = array_fill(0, 100, ' ');
        $board[75] = 'b';
        $board[64] = 'b';
        $board[66] = 'b';
        $board[84] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));
        // two forward and oponent piece back (bot not for beat)
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $board[74] = 'b';
        $board[76] = 'b';
        $board[94] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // two forward and two oponent pieces back
        $board = array_fill(0, 100, ' ');
        $board[75] = 'b';
        $board[64] = 'b';
        $board[66] = 'b';
        $board[84] = 'W';
        $board[86] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));

        // same at end
        $board = array_fill(0, 100, ' ');
        $board[3] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(3, False));

        // same at left side
        $board = array_fill(0, 100, ' ');
        $board[60] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(60, False));
        // left side but own piece
        $board = array_fill(0, 100, ' ');
        $board[60] = 'b';
        $board[51] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(60, False));
        // left side but oponent piece
        $board = array_fill(0, 100, ' ');
        $board[60] = 'b';
        $board[51] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(60, False));
        // left side but oponent piece and own
        $board = array_fill(0, 100, ' ');
        $board[60] = 'b';
        $board[51] = 'b';
        $board[71] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(60, False));

        // same at right side
        $board = array_fill(0, 100, ' ');
        $board[69] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(69, False));
        // right side but own piece
        $board = array_fill(0, 100, ' ');
        $board[69] = 'b';
        $board[58] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(69, False));
        // left side but oponent piece
        $board = array_fill(0, 100, ' ');
        $board[69] = 'b';
        $board[58] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(69, False));
        // left side but oponent piece and own
        $board = array_fill(0, 100, ' ');
        $board[69] = 'b';
        $board[58] = 'b';
        $board[78] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(69, False));

        /* test for black king */
        // same
        $board = array_fill(0, 100, ' ');
        $board[85] = 'B';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // one forward
        $board = array_fill(0, 100, ' ');
        $board[85] = 'B';
        $board[74] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // two forward
        $board = array_fill(0, 100, ' ');
        $board[85] = 'B';
        $board[74] = 'b';
        $board[76] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, True));
        // two forward
        $board = array_fill(0, 100, ' ');
        $board[95] = 'B';
        $board[84] = 'b';
        $board[86] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(95, False));
        // two forward, one backward
        $board = array_fill(0, 100, ' ');
        $board[85] = 'W';
        $board[74] = 'w';
        $board[76] = 'w';
        $board[96] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // two forward, two backward
        $board = array_fill(0, 100, ' ');
        $board[85] = 'B';
        $board[74] = 'b';
        $board[76] = 'b';
        $board[94] = 'b';
        $board[96] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // one oponent rest is own
        $board = array_fill(0, 100, ' ');
        $board[85] = 'B';
        $board[74] = 'b';
        $board[76] = 'w';
        $board[94] = 'b';
        $board[96] = 'b';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->assertTrue($gameLogic->canMove(85, False));
        // one oponent (but not to beat) rest is own
        $board = array_fill(0, 100, ' ');
        $board[85] = 'B';
        $board[74] = 'b';
        $board[76] = 'b';
        $board[94] = 'b';
        $board[96] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));
        // one oponent (but not to beat) rest is own
        $board = array_fill(0, 100, ' ');
        $board[85] = 'B';
        $board[74] = 'b';
        $board[76] = 'b';
        $board[94] = 'w';
        $board[96] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertFalse($gameLogic->canMove(85, False));

        // one oponent rest is own
        $board = array_fill(0, 100, ' ');
        $board[75] = 'B';
        $board[64] = 'b';
        $board[66] = 'b';
        $board[84] = 'b';
        $board[86] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));
        // one oponent rest is own
        $board = array_fill(0, 100, ' ');
        $board[75] = 'B';
        $board[64] = 'b';
        $board[66] = 'b';
        $board[84] = 'w';
        $board[86] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertTrue($gameLogic->canMove(75, False));

        // same at end
        $board = array_fill(0, 100, ' ');
        $board[3] = 'B';
        $gameLogic = GameLogic::fromData($board, True, NULL);
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
        $board = array_fill(0, 100, ' ');
        $board[15] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertEquals(GameLogic::PLAYER1WIN, $gameLogic->checkGameEnd());
        // same
        $board = array_fill(0, 100, ' ');
        $board[85] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertEquals(GameLogic::PLAYER2WIN, $gameLogic->checkGameEnd());

        // same
        $board = array_fill(0, 100, ' ');
        $board[95] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertEquals(GameLogic::PLAYER1WIN, $gameLogic->checkGameEnd());
        // same
        $board = array_fill(0, 100, ' ');
        $board[5] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertEquals(GameLogic::PLAYER2WIN, $gameLogic->checkGameEnd());
    }

    // test GameLogic::findFirstBeatPos
    public function testFindFirstBeatPos()
    {
        $board = array_fill(0, 100, ' ');
        $board[45] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'w';
        $board[56] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        // oponent piece in other direction
        $board = array_fill(0, 100, ' ');
        $board[45] = 'w';
        $board[54] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        // oponent piece in other direction
        $board = array_fill(0, 100, ' ');
        $board[45] = 'w';
        $board[34] = 'B';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'w';
        $board[56] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertEquals([56, 67],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'w';
        $board[56] = 'B';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertEquals([56, 67],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'w';
        $board[67] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'w';
        $board[78] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'W';
        $board[56] = 'w';
        $board[78] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        $board = array_fill(0, 100, ' ');
        $board[49] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));
        // if oponent piece in edge
        $board = array_fill(0, 100, ' ');
        $board[48] = 'W';
        $board[59] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, False));

        //////////////////////////////////////
        // if king
        $board = array_fill(0, 100, ' ');
        $board[45] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'W';
        $board[56] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'W';
        $board[56] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertEquals([56, 67],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'W';
        $board[67] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertEquals([67, 78],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'W';
        $board[78] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertEquals([78, 89],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        // if your piece precedes oponent piece
        $board = array_fill(0, 100, ' ');
        $board[45] = 'W';
        $board[56] = 'w';
        $board[78] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));
        // if your piece precedes oponent piece
        $board = array_fill(0, 100, ' ');
        $board[45] = 'W';
        $board[67] = 'w';
        $board[78] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));
        // if oponent piece in edge
        $board = array_fill(0, 100, ' ');
        $board[45] = 'W';
        $board[89] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        $board = array_fill(0, 100, ' ');
        $board[49] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVENE, True));

        /////////////////////////////////////////
        /* blacks */

        $board = array_fill(0, 100, ' ');
        $board[45] = 'b';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESE, False));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'b';
        $board[36] = 'b';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESE, False));

        // oponent piece in other direction
        $board = array_fill(0, 100, ' ');
        $board[45] = 'b';
        $board[34] = 'w';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESE, False));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'b';
        $board[36] = 'w';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->assertEquals([36, 27],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVESE, False));

        //////////////////////////////////////
        // if king
        $board = array_fill(0, 100, ' ');
        $board[45] = 'B';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESW, True));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'B';
        $board[34] = 'b';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->assertNull($gameLogic->findFirstBeatPos(45, GameLogic::MOVESW, True));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'B';
        $board[34] = 'w';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->assertEquals([34, 23],
                $gameLogic->findFirstBeatPos(45, GameLogic::MOVESW, True));

        $board = array_fill(0, 100, ' ');
        $board[45] = 'B';
        $board[23] = 'w';
        $gameLogic = GameLogic::fromData($board, False, NULL);
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
        ], $gameLogic->getBoard());
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
        ], $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());

        // for king
        $board = array_fill(0, 100, ' ');
        $board[75] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(75, 42);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[42] = 'W';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());

        // for king 2
        $board = array_fill(0, 100, ' ');
        $board[75] = 'W';
        $board[31] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(75, 42);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[42] = 'W';
        $expBoard[31] = 'W';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());

        // for king 3
        $board = array_fill(0, 100, ' ');
        $board[41] = 'W';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(41, 85);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[85] = 'W';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());

        /*
         * Make beats
         */
        $board = array_fill(0, 100, ' ');
        $board[75] = 'w';
        $board[64] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(75, 53);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[53] = 'w';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $board = array_fill(0, 100, ' ');
        $board[75] = 'w';
        $board[86] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(75, 97);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[97] = 'W'; // handle promotion
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $board = array_fill(0, 100, ' ');
        $board[75] = 'w';
        $board[84] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(75, 93);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[93] = 'W'; // handle promotion
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $board = array_fill(0, 100, ' ');
        $board[75] = 'w';
        $board[66] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(75, 57);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[57] = 'w';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $board = array_fill(0, 100, ' ');
        $board[65] = 'w';
        $board[76] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(65, 87);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[87] = 'w';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        $board = array_fill(0, 100, ' ');
        $board[75] = 'w';
        $board[86] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(75, 97);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[97] = 'W';    // promotion
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        // double beat (do not change player)
        $board = array_fill(0, 100, ' ');
        $board[65] = 'w';
        $board[54] = 'b';
        $board[32] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(65, 43);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[43] = 'w';
        $expBoard[32] = 'b';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([54, 43], $gameLogic->getLastBeat());
        // next beat
        $gameLogic->makeMove(43, 21);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[21] = 'w';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        // double beat (do not change player)
        $board = array_fill(0, 100, ' ');
        $board[75] = 'w';
        $board[86] = 'b';
        $board[88] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(75, 97);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[97] = 'w';    // no promotion (not last beat)
        $expBoard[88] = 'b';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([86, 97], $gameLogic->getLastBeat());
        // next beat
        $gameLogic->makeMove(97, 79);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[79] = 'w';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
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
        $expBoard = [
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
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([36, 54], $gameLogic->getLastBeat());
        // next beat
        $gameLogic->makeMove(54, 32);
        $expBoard = [
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
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());
        $this->assertEquals([43, 32], $gameLogic->getLastBeat());
        // last beat
        $gameLogic->makeMove(32, 10);
        $expBoard = [
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
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertFalse($gameLogic->isPlayer1MakeMove());
        $this->assertNull($gameLogic->getLastBeat());

        /*
         * for blacks
         */
        $board = array_fill(0, 100, ' ');
        $board[75] = 'b';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $gameLogic->makeMove(75, 66);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[66] = 'b';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
        $this->assertTrue($gameLogic->isPlayer1MakeMove());

        $board = array_fill(0, 100, ' ');
        $board[75] = 'b';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $gameLogic->makeMove(75, 64);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[64] = 'b';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
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
        $board = array_fill(0, 100, ' ');
        $board[55] = 'b';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Wrong end position');
        $gameLogic->makeMove(55, 45);
    }

    public function testMakeMoveWrongEndPosX2()
    {
        $board = array_fill(0, 100, ' ');
        $board[55] = 'b';
        $gameLogic = GameLogic::fromData($board, False, NULL);
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Wrong end position');
        $gameLogic->makeMove(55, 66);
    }

    public function testMakeMoveWrongEndPosX3()
    {
        $board = array_fill(0, 100, ' ');
        $board[55] = 'b';
        $gameLogic = GameLogic::fromData($board, False, NULL);
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
        $board = array_fill(0, 100, ' ');
        $board[75] = 'W';
        $board[42] = 'w';
        $gameLogic = GameLogic::fromData($board, True, NULL);
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
        $board = array_fill(0, 100, ' ');
        $board[65] = 'w';
        $board[54] = 'b';
        $board[32] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(65, 43);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[43] = 'w';
        $expBoard[32] = 'b';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
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
        $board = array_fill(0, 100, ' ');
        $board[65] = 'w';
        $board[11] = 'w';  // not this same
        $board[54] = 'b';
        $board[32] = 'b';
        $gameLogic = GameLogic::fromData($board, True, NULL);
        $gameLogic->makeMove(65, 43);
        $expBoard = array_fill(0, 100, ' ');
        $expBoard[11] = 'w';
        $expBoard[43] = 'w';
        $expBoard[32] = 'b';
        $this->assertEquals($expBoard, $gameLogic->getBoard());
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
