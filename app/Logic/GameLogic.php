<?php

namespace App\Logic;

class GameLogic
{
    private $state;
    private $player1Move;
    private $lastBeat;

    // IMPORTANT NOTICE:
    // we treat moves as single move or single beat (not a whole sequence of beats)

    public function __construct()
    {
        // construct
        $this->startState();
    }

    // north - y+, south - y-, east - x+, west - x-
    public const MOVENE = 0;
    public const MOVESE = 1;
    public const MOVENW = 2;
    public const MOVESW = 3;
    public const BOARDDIM = 10;

    public const PLAYER1WIN = 1;
    public const PLAYER2WIN = 2;
    public const DRAW = 3;
    public const NOTEND = 0;

    public static function fromData(array $newState,
                bool $newPlayer1Move, $newLastBeat)
    {
        $gameLogic = new Self();
        $gameLogic->state = $newState;
        $gameLogic->player1Move = $newPlayer1Move;
        $gameLogic->lastBeat = $newLastBeat;
        return $gameLogic;
    }

    // player1 plays whites, player2 playes blacks
    public function startState()
    {
        $this->player1Move = True;
        $this->lastBeat = NULL;
        $this->state = [
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w',
           'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ', 'w', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b',
           'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ',
           ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b', ' ', 'b'
        ];
    }

    public function getState()
    {
        return $this->state;
    }

    public function isPlayer1MakeMove()
    {
        return $this->player1Move;
    }

    public function getLastBeat()
    {
        return $this->lastBeat;
    }

    public function makeMove(int $startPos, int $endPos)
    {
        // check startPos and endPos
        if ($startPos < 0 || $startPos >= Self::BOARDDIM*Self::BOARDDIM ||
            $endPos < 0 || $endPos >= Self::BOARDDIM*Self::BOARDDIM)
            throw new GameException('Move positions out of range');

        // check start position
        if (!$this->isPlayerPiece($startPos))
            throw new GameException('No player piece in start position');

        // now we check whether no mandatory beats
        $mandatoryBeatStarts = [];
        $mandatoryBeats = [];
        if ($this->lastBeat === NULL)
            for ($pos = 0; $pos < Self::BOARDDIM*Self::BOARDDIM; $pos++)
            {
                if ($this->isPlayerPiece($pos))
                    $this->findBestBeatsSeqs($pos, $mandatoryBeatStarts, $mandatoryBeats);
            }
        else
        {
            if ($this->lastBeat[1] != $startPos)
                throw new GameException('Move is not a mandatory beat');
            // only for after last beat move
            $this->findBestBeatsSeqs($this->lastBeat[1],
                    $mandatoryBeatStarts, $mandatoryBeats);
        }

        // if we have mandatary beats
        if (count($mandatoryBeats) != 0)
        {
            $beatFound = False;
            $beatMove = NULL;
            $beatPos = NULL;
            $afterPiece = NULL;
            $c = count($mandatoryBeats);
            for ($i = 0; $i < $c; $i++)
                if ($mandatoryBeatStarts[$i][0] == $startPos)
                {
                    $beatPos = $mandatoryBeats[$i][0];
                    // next position
                    $dir = 0;
                    // determine direction
                    if ($startPos < $beatPos)
                        $dir = ($startPos + Self::BOARDDIM-1 == $beatPos) ?
                            Self::MOVENW : Self::MOVENE;
                    else
                        $dir = ($startPos - (Self::BOARDDIM-1) == $beatPos) ?
                            Self::MOVESE : Self::MOVESW;
                    // calculate position after beat
                    $afterPiece = Self::goNext($beatPos, $dir);
                    if ($endPos == $afterPiece)
                        $beatFound = True;
                    else if ($this->isKing($startPos))
                    {
                        // check all position in cross line in this direction
                        $nextp = Self::goNext($afterPiece);
                        while ($nextp >= 0 && $this->state[$nextp] == ' ')
                        {
                            if ($endPos == $nextp)
                            {
                                $beatFound = True;
                                break;
                            }
                            $nextp = Self::goNext($nextp, $dir);
                        }
                    }
                }
            if (!$beatFound)
                throw new GameException('Move is not a mandatory beat');
            // make move
            $piece = $this->state[$startPos];
            $this->state[$startPos] = ' ';
            $this->state[$beatPos] = ' ';
            $this->state[$endPos] = $piece; // your piece

            if (count($mandatoryBeats[0]) == 1)
            {
                // if it last beat, we can promote if in suitable place
                $this->handlePromotion($endPos);
                // and reverse player
                $this->player1Move = !$this->player1Move;
                $this->lastBeat = NULL; // clear lastBeat if last beat in sequence
            }
            else
                // this is not end of beating
                $this->lastBeat = [$beatPos, $endPos];
        }
        else
        {
            if (!$this->isKing($startPos))
            {
                // otherwise this a normal move (not beat) for men
                // check end position
                if ($this->player1Move)
                {
                    if ($startPos + Self::BOARDDIM-1 != $endPos &&
                        $startPos + Self::BOARDDIM+1 != $endPos)
                        throw new GameException('Wrong end position');
                }
                else
                {
                    if ($startPos - Self::BOARDDIM-1 != $endPos &&
                        $startPos - Self::BOARDDIM+1 != $endPos)
                        throw new GameException('Wrong end position');
                }
                if ($this->state[$endPos] != ' ')
                    throw new GameException('No free field in end position');
            }
            else // for King
            {
                // check end position for the king
                $endPosFound = False;
                for($dir = 0; $dir < 4; $dir++)
                {
                    $nextp = $startPos;
                    while (($nextp = Self::goNext($nextp, $dir)) >= 0)
                    {
                        if ($this->state[$nextp] == ' ')
                        {
                            if ($nextp == $endPos)
                            {
                                $endPosFound = True;
                                break;
                            }
                        }
                        else
                            break; // end of free fields in cross line in this position
                    }
                }
                if (!$endPosFound)
                    throw new GameException('Wrong end position');
            }

            // make
            $piece = $this->state[$startPos];
            $this->state[$startPos] = ' ';
            $this->state[$endPos] = $piece;
            $this->handlePromotion($endPos);
            // reverse player
            $this->player1Move = !$this->player1Move;
        }
    }

    public function handlePromotion($pos)
    {
        $y = intdiv($pos, Self::BOARDDIM);
        if (($this->player1Move && $y == Self::BOARDDIM-1) ||
            (!$this->player1Move && $y == 0))
            // make men to king
            $this->state[$pos] = strtoupper($this->state[$pos]);
    }

    // check whether player can any move by piece in this position
    public function canMove(int $pos, bool $player1): bool
    {
        $playerCanMove = False;
        // for directions king
        $dirs = [Self::MOVENE, Self::MOVENW, Self::MOVESE, Self::MOVESW];
        if (!$this->isGivenKing($pos, $player1))
            $dirs = $player1 ? [Self::MOVENE, Self::MOVENW] :
                [Self::MOVESE, Self::MOVESW];
        // check we can any move in these directions
        foreach ($dirs as $dir)
        {
            $nextp = Self::goNext($pos, $dir);
            if ($nextp >= 0 && $this->state[$nextp] == ' ')
            {
                $playerCanMove = True;
                break;
            }
        }
        if (!$playerCanMove)
            // check we can beats
            for ($dir = 0; $dir < 4; $dir++)
            {
                $nextp = Self::goNext($pos, $dir);
                if ($nextp < 0)
                    continue; // no move
                // if oponent piece in next place
                if ($this->isGivenPlayerPiece($nextp, !$player1))
                {
                    $nextp = Self::goNext($nextp, $dir);
                    if ($nextp >= 0 && $this->state[$nextp] == ' ')
                    {
                        $playerCanMove = True;
                        break;
                    }
                }
            }
        return $playerCanMove;
    }

    // check game end
    public function checkGameEnd(): int
    {
        $playersCanMove = [False, False];
        $playerHavePieces = [False, False];
        for ($p = 0; $p < 2; $p++)
        {
            // p is player number (0 or 1)
            for ($pos = 0; $pos < Self::BOARDDIM*Self::BOARDDIM; $pos++)
            {
                if (!$this->isGivenPlayerPiece($pos, $p==0))
                    continue;
                $playerHavePieces[$p] = True;
                // if player piece
                if ($this->canMove($pos, $p==0))
                {
                    $playersCanMove[$p] = True;
                    break;
                }
            }
        }
        if ($playerHavePieces[0] && $playerHavePieces[1])
        {
            // if all players have pieces
            if (!$playersCanMove[0] && $playersCanMove[1])
                return Self::PLAYER2WIN; //
            else if ($playersCanMove[0] && !$playersCanMove[1])
                return Self::PLAYER1WIN; //
            else if (!$playersCanMove[0] && !$playersCanMove[1])
                return Self::DRAW;
        }
        // otherwise
        else if ($playerHavePieces[0])
            return Self::PLAYER1WIN;
        else if ($playerHavePieces[1])
            return Self::PLAYER2WIN;
        else
            return Self::DRAW;
        return Self::NOTEND;
    }

    // return next position in direction or -1 if position out of board
    public static function goNext(int $pos, int $dir): int
    {
        $xi = $pos % Self::BOARDDIM;
        $yi = intdiv($pos,Self::BOARDDIM);
        switch ($dir)
        {
            case Self::MOVENE:
                if ($xi+1 >= Self::BOARDDIM || $yi+1 >= Self::BOARDDIM)
                    return -1;
                $xi++; $yi++;
                break;
            case Self::MOVENW:
                if ($xi-1 < 0 || $yi+1 >= Self::BOARDDIM)
                    return -1;
                $xi--; $yi++;
                break;
            case Self::MOVESE:
                if ($xi+1  >= Self::BOARDDIM || $yi-1 < 0)
                    return -1;
                $xi++; $yi--;
                break;
            case Self::MOVESW:
                if ($xi-1 < 0 || $yi-1 < 0)
                    return -1;
                $xi--; $yi--;
                break;
        }
        // return new position
        return $xi + $yi*Self::BOARDDIM;
    }

    public function isOponentPiece(int $pos): bool
    {
        $opMen = $this->player1Move ? 'b' : 'w';
        $opKing = $this->player1Move ? 'B' : 'W';
        return $this->state[$pos] == $opMen || $this->state[$pos] == $opKing;
    }

    public function isPlayerPiece(int $pos): bool
    {
        $opMen = $this->player1Move ? 'w' : 'b';
        $opKing = $this->player1Move ? 'W' : 'B';
        return $this->state[$pos] == $opMen || $this->state[$pos] == $opKing;
    }

    public function isGivenPlayerPiece(int $pos, bool $player1): bool
    {
        $opMen = $player1 ? 'w' : 'b';
        $opKing = $player1 ? 'W' : 'B';
        return $this->state[$pos] == $opMen || $this->state[$pos] == $opKing;
    }

    public function isKing(int $pos): bool
    {
        return $this->state[$pos] == ($this->player1Move ? 'W' : 'B');
    }

    public function isGivenKing(int $pos, bool $player1): bool
    {
        return $this->state[$pos] == ($player1 ? 'W' : 'B');
    }

    public function findFirstBeatPos(int $pos, int $dir, bool $king = False)
    {
        $nextp = $pos;
        $foundOpPiece = False;
        if (($nextp = Self::goNext($nextp, $dir)) >= 0)
             if ($this->isOponentPiece($nextp))
                $foundOpPiece = True;

        if (!$foundOpPiece)
        {
            if ($king && $nextp >= 0 && $this->state[$nextp] == ' ')
                // for king, check for all position cross line
                // and if second position is empty
                while (($nextp = Self::goNext($nextp, $dir)) >= 0)
                {
                    if ($this->isOponentPiece($nextp))
                    {
                        $foundOpPiece = True;
                        break;
                    }
                    else if ($this->state[$nextp] != ' ')
                        break; // if your piece
                }
        }
        if (!$foundOpPiece)
            return NULL;  // not found beat hit

        // check what is after oponent piece
        $afterPiece = Self::goNext($nextp, $dir);
        if ($afterPiece < 0 || $this->state[$afterPiece] != ' ')
            return NULL;  // no free place after piece
        // if free
        return [ $nextp, $afterPiece ];
    }

    // internal
    // find best beat sequence from specified position in  specified direction
    // beatArray - oponent piece position which have benn beaten
    // outBeatArray - array of best sequences contains positions of the beaten pieces
    // outStartArray - array of best sequences contains position from piece was beaten
    private function findBestBeatSeqsInt(int $pos, int $dir,
            array& $startArray, array& $beatArray,
            array& $outStartArray, array& $outBeatArray, $king)
    {
        $revDirs = [ Self::MOVESW, Self::MOVENW, Self::MOVESE, Self::MOVENE ];
        $revDir = $revDirs[$dir];
        $beat = NULL;
        if ($dir >= 0)
            $beat = $this->findFirstBeatPos($pos, $dir, $king);

        // check whether new beat can be done
        if ($beat !== NULL && !in_array($beat[0], $beatArray))
        {
            // if we have beat and is not duplicate
            // if we have beat, then push into arrays
            array_push($beatArray, $beat[0]);
            array_push($startArray, $pos);
            $haveNextBeats = False;
            if (!$king)
                for ($dir = 0; $dir < 4; $dir++)
                {
                    if ($dir == $revDir)
                        continue;
                    $haveNextBeats |= $this->findBestBeatSeqsInt($beat[1], $dir,
                            $startArray, $beatArray, $outStartArray, $outBeatArray, $king);
                }
            else
            {
                // if king check all position in cross line in this direction
                for ($dir = 0; $dir < 4; $dir++)
                {
                    if ($dir == $revDir)
                        continue;
                    $nextp = $beat[1];
                    while ($nextp >= 0 && $this->state[$nextp] == ' ')
                    {
                        $haveNextBeats |= $this->findBestBeatSeqsInt($nextp, $dir,
                            $startArray, $beatArray, $outStartArray, $outBeatArray, $king);
                        $nextp = Self::goNext($nextp, $dir);
                    }
                }
            }

            if (!$haveNextBeats)
            {
                // no more next beats, put this result
                if (count($beatArray) != 0)
                {
                    // put only if we have some beat
                    if (count($outBeatArray) != 0)
                    {
                        if (count($outBeatArray[0]) < count($beatArray))
                        {
                            // if better
                            $outStartArray = [$startArray];
                            $outBeatArray = [$beatArray]; // clear and put
                        }
                        else if (count($outBeatArray[0]) == count($beatArray))
                        {
                            // just put
                            array_push($outStartArray, $startArray);
                            array_push($outBeatArray, $beatArray);
                        }
                    }
                    else // just put
                    {
                        $outStartArray = [$startArray];
                        $outBeatArray = [$beatArray];
                    }
                }
            }

            array_pop($startArray);
            array_pop($beatArray);
            return True;
        }
        return False;
    }

    // find best beat sequence from specified position in  specified direction
    // $outArray - array of best sequences
    public function findBestBeatsSeqs(int $pos,
                array& $outStartArray, array& $outBeatArray)
    {
        $king = $this->isKing($pos);
        // before test, we remove piece from board
        $piece = $this->state[$pos];
        $this->state[$pos] = ' ';
        for ($dir = 0; $dir < 4; $dir++)
        {
            //echo "Next find for ", $dir, "\n";
            $startArray = [];
            $beatArray = [];
            $this->findBestBeatSeqsInt($pos, $dir, $startArray, $beatArray,
                        $outStartArray, $outBeatArray, $king);
        }
        // put back after test
        $this->state[$pos] = $piece;
    }
};
