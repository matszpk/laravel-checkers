/**
 * 
 */

require('./utils');

setArraySingle = function(arr, v)
{
    arr.splice(0, arr.length, v);
};

uniqueArray = function(arr)
{
    var out = [];
    var prev = null;
    for (var i = 0; i < arr.length; i++)
        if (prev !== arr[i])
        {
            out.push(arr[i]);
            prev = arr[i];
        }
    return out;
}

GameLogic = {
    MOVENE: 0,
    MOVESE: 1,
    MOVENW: 2,
    MOVESW: 3,
    BOARDDIM: 10,

    PLAYER1WIN: 1,
    PLAYER2WIN: 2,
    DRAW: 3,
    NOTEND: 0,

    board: null,
    player1Move: false,
    lastBeat: null,
    player1Plays: false,
    lastBeatenPiece: null,

    fromData: function(newBoard, newPlayer1Move, newLastBeat, newPlayer1Plays)
    {
        this.board = newBoard;
        this.player1Move = newPlayer1Move;
        this.newLastBeat = newLastBeat;
        this.player1Plays = newPlayer1Plays!=null ? newPlayer1Plays : true; 
    },

    startState: function()
    {
        this.player1Move = true;
        this.lastBeat = null;
        this.board = [
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
    },
    
    isPlayerMove: function() {
        return this.player1Plays == this.player1Move;
    },

    toConsoleLog: function()
    {
        var s = "";
        for (var y = 0; y < this.BOARDDIM; y++)
        {
            for (var x = 0; x < this.BOARDDIM; x++)
                s += this.board[x+y*this.BOARDDIM]+'|';
            s += '\n--------------------\n';
        };
        console.log(s + "Player1:" + this.player1Move+"\n");
    },

    getChoosable: function()
    {
        var choosablePieces = { };
        var mandatoryBeatStarts = [];
        var mandatoryBeats = [];
        if (this.lastBeat === null)
            for (var pos = 0; pos < this.BOARDDIM*this.BOARDDIM; pos++)
            {
                if (this.isPlayerPiece(pos))
                    this.findBestBeatsSeqs(pos, mandatoryBeatStarts, mandatoryBeats);
            }
        else
            // only for after last beat move
            this.findBestBeatsSeqs(this.lastBeat[1], mandatoryBeatStarts, mandatoryBeats);

        // if we have mandatary beats
        if (mandatoryBeats.length != 0)
        {
            for (var i = 0; i < mandatoryBeatStarts.length; i++)
            {
                // get possible moves while beating
                var mandBeatStart = mandatoryBeatStarts[i];
                var mandBeat = mandatoryBeats[i]; // end positions
                // get possible move ends for mandatory beat sequence
                var moveEnds = this.getPossibleMovesAtBeat(mandBeatStart, mandBeat);
                if (choosablePieces[mandBeatStart[0]] != null)
                    // just add to list
                    choosablePieces[mandBeatStart[0]] =
                        choosablePieces[mandBeatStart[0]].concat(moveEnds);
                else
                    choosablePieces[mandBeatStart[0]] = moveEnds;
            }
        }
        else
        {
            // no mandatory pieces
            for (var pos = 0; pos < this.BOARDDIM*this.BOARDDIM; pos++)
                if (this.isPlayerPiece(pos) && this.canMove(pos, this.player1Move))
                    choosablePieces[pos] = this.getPossibleSameMoves(pos);
        }

        // remove duplicates
        for (var i = 0; i < choosablePieces.length; i++)
        {
            choosablePieces[i].sort();
            choosablePieces[i] = uniqueArray(choosablePieces[i]);
        }
        return choosablePieces;
    },

    // input:
    // mandBeatStart - start position in beat sequence
    // mandBeatStart - beaten piece position in beat sequence
    getPossibleMovesAtBeat: function(mandBeatStart, mandBeat)
    {
        var startPos = mandBeatStart[0];
        var beatPos = mandBeat[0];
        var moveEnds = [];
        if (this.isKing(startPos) && mandBeat.length >= 2)
        {
            // if king and next beat is present then
            // check endPos with start pos from next beating
            moveEnds = [mandBeatStart[1]];
        }
        else
        {
            // next position
            var dir = this.getMoveDir(startPos, beatPos);
            // calculate position after beat
            afterPiece = this.goNext(beatPos, dir);
            moveEnds.push(afterPiece);
            if (this.isKing(startPos))
            {
                // check all position in cross line in this direction
                var nextp = this.goNext(afterPiece);
                while (nextp >= 0 && this.board[nextp] == ' ')
                {
                    moveEnds.push(nextp);
                    nextp = this.goNext(nextp, dir);
                }
            }
        }
        return moveEnds;
    },

    // but not beats
    getPossibleSameMoves: function(pos)
    {
        var moveEnds = [];
        // for directions king
        var dirs = [this.MOVENE, this.MOVENW, this.MOVESE, this.MOVESW];
        if (!this.isKing(pos))
            dirs = this.player1Move ? [this.MOVENE, this.MOVENW] : [this.MOVESE, this.MOVESW];
        // check we can any move in these directions
        for(var i = 0; i < dirs.length; i++)
        {
            var dir = dirs[i];
            var nextp = this.goNext(pos, dir);
            if (nextp >= 0 && this.board[nextp] == ' ')
            {
                moveEnds.push(nextp);
                if (this.isKing(pos))
                {
                    // for king all possible moves
                    nextp = this.goNext(nextp, dir);
                    while (nextp >= 0 && this.board[nextp] == ' ')
                    {
                        moveEnds.push(nextp);
                        nextp = this.goNext(nextp, dir);
                    }
                }
            }
        }
        return moveEnds;
    },
    
    getMoveDir: function(startPos, endPos)
    {
        var sxi = startPos % this.BOARDDIM;
        var syi = Math.floor(startPos/this.BOARDDIM);
        var exi = endPos % this.BOARDDIM;
        var eyi = Math.floor(endPos/this.BOARDDIM);
        if (syi < eyi)
            return sxi < exi ? this.MOVENE : this.MOVENW;
        else
            return sxi < exi ? this.MOVESE : this.MOVESW;
    },

    makeMove: function(startPos, endPos)
    {
        // check startPos and endPos
        if (startPos < 0 || startPos >= this.BOARDDIM*this.BOARDDIM ||
            endPos < 0 || endPos >= this.BOARDDIM*this.BOARDDIM)
            throw 'Move positions out of range';

        // check start position
        if (!this.isPlayerPiece(startPos))
            throw 'No player piece in start position';

        // now we check whether no mandatory beats
        var mandatoryBeatStarts = [];
        var mandatoryBeats = [];
        if (this.lastBeat === null)
            for (var pos = 0; pos < this.BOARDDIM*this.BOARDDIM; pos++)
            {
                if (this.isPlayerPiece(pos))
                    this.findBestBeatsSeqs(pos, mandatoryBeatStarts, mandatoryBeats);
            }
        else
        {
            if (this.lastBeat[1] != startPos)
                throw 'Move is not a mandatory beat';
            // only for after last beat move
            this.findBestBeatsSeqs(this.lastBeat[1], mandatoryBeatStarts, mandatoryBeats);
        }

        // if we have mandatary beats
        if (mandatoryBeats.length != 0)
        {
            var beatFound = false;
            var beatMove = null;
            var beatPos = null;
            var afterPiece = null;
            var c = mandatoryBeats.length;
            for (var i = 0; i < c; i++)
                if (mandatoryBeatStarts[i][0] == startPos)
                {
                    beatPos = mandatoryBeats[i][0];
                    if (this.isKing(startPos) && mandatoryBeats[i].length >= 2)
                    {
                        // if king and next beat is present then
                        // check endPos with start pos from next beating
                        if (mandatoryBeatStarts[i][1] == endPos)
                            beatFound = true;
                    }
                    else
                    {
                        // next position
                        var dir = this.getMoveDir(startPos, beatPos);
                        // calculate position after beat
                        afterPiece = this.goNext(beatPos, dir);
                        if (endPos == afterPiece)
                            beatFound = true;
                        else if (this.isKing(startPos))
                        {
                            // check all position in cross line in this direction
                            var nextp = this.goNext(afterPiece, dir);
                            while (nextp >= 0 && this.board[nextp] == ' ')
                            {
                                if (endPos == nextp)
                                {
                                    beatFound = true;
                                    break;
                                }
                                nextp = this.goNext(nextp, dir);
                            }
                        }
                    }
                    if (beatFound)
                        break;
                }

            // after finding, we check whether mandatory beat is found
            if (!beatFound)
                throw 'Move is not a mandatory beat';
            // make move
            var piece = this.board[startPos];
            this.board[startPos] = ' ';
            this.board[beatPos] = ' ';
            this.board[endPos] = piece; // your piece

            if (mandatoryBeats[0].length == 1)
            {
                // if it last beat, we can promote if in suitable place
                this.handlePromotion(endPos);
                // and reverse player
                this.player1Move = !this.player1Move;
                this.lastBeat = null; // clear lastBeat if last beat in sequence
            }
            else
                // this is not end of beating
                this.lastBeat = [beatPos, endPos];
            // set last beaten piece
            this.lastBeatenPiece = beatPos;
        }
        else
        {
            if (!this.isKing(startPos))
            {
                // otherwise this a normal move (not beat) for men
                // check end position
                if (this.player1Move)
                {
                    if (startPos + this.BOARDDIM-1 != endPos &&
                        startPos + this.BOARDDIM+1 != endPos)
                        throw 'Wrong end position';
                }
                else
                {
                    if (startPos - this.BOARDDIM-1 != endPos &&
                        startPos - this.BOARDDIM+1 != endPos)
                        throw 'Wrong end position';
                }
                if (this.board[endPos] != ' ')
                    throw 'No free field in end position';
            }
            else // for King
            {
                // check end position for the king
                var endPosFound = false;
                for(var dir = 0; dir < 4; dir++)
                {
                    var nextp = startPos;
                    while ((nextp = this.goNext(nextp, dir)) >= 0)
                    {
                        if (this.board[nextp] == ' ')
                        {
                            if (nextp == endPos)
                            {
                                endPosFound = true;
                                break;
                            }
                        }
                        else
                            break; // end of free fields in cross line in this position
                    }
                }
                if (!endPosFound)
                    throw 'Wrong end position';
            }

            // make
            piece = this.board[startPos];
            this.board[startPos] = ' ';
            this.board[endPos] = piece;
            this.handlePromotion(endPos);
            this.lastBeatenPiece = null; // unset lastBeatenPiece
            // reverse player
            this.player1Move = !this.player1Move;
        }
    },

    handlePromotion: function(pos)
    {
        var y = Math.floor(pos/this.BOARDDIM);
        if ((this.player1Move && y == this.BOARDDIM-1) ||
            (!this.player1Move && y == 0))
            // make men to king
            this.board[pos] = this.board[pos].toUpperCase();
    },

    // check whether player can any move by piece in this position
    canMove: function(pos, player1)
    {
        var playerCanMove = false;
        // for directions king
        var dirs = [this.MOVENE, this.MOVENW, this.MOVESE, this.MOVESW];
        if (!this.isGivenKing(pos, player1))
            dirs = player1 ? [this.MOVENE, this.MOVENW] : [this.MOVESE, this.MOVESW];
        // check we can any move in these directions
        for(var i = 0; i < dirs.length; i++)
        {
            var dir = dirs[i];
            var nextp = this.goNext(pos, dir);
            if (nextp >= 0 && this.board[nextp] == ' ')
            {
                playerCanMove = true;
                break;
            }
        }
        if (!playerCanMove)
            // check we can beats
            for (var dir = 0; dir < 4; dir++)
            {
                var nextp = this.goNext(pos, dir);
                if (nextp < 0)
                    continue; // no move
                // if oponent piece in next place
                if (this.isGivenPlayerPiece(nextp, !player1))
                {
                    nextp = this.goNext(nextp, dir);
                    if (nextp >= 0 && this.board[nextp] == ' ')
                    {
                        playerCanMove = true;
                        break;
                    }
                }
            }
        return playerCanMove;
    },

    // check game end
    checkGameEnd: function()
    {
        var playersCanMove = [false, false];
        var playerHavePieces = [false, false];
        for (var p = 0; p < 2; p++)
        {
            // p is player number (0 or 1)
            for (var pos = 0; pos < this.BOARDDIM*this.BOARDDIM; pos++)
            {
                if (!this.isGivenPlayerPiece(pos, p==0))
                    continue;
                playerHavePieces[p] = true;
                // if player piece
                if (this.canMove(pos, p==0))
                {
                    playersCanMove[p] = true;
                    break;
                }
            }
        }
        if (playerHavePieces[0] && playerHavePieces[1])
        {
            // if all players have pieces
            if (!playersCanMove[0] && playersCanMove[1])
                return this.PLAYER2WIN; //
            else if (playersCanMove[0] && !playersCanMove[1])
                return this.PLAYER1WIN; //
            else if (!playersCanMove[0] && !playersCanMove[1])
                return this.DRAW;
        }
        // otherwise
        else if (playerHavePieces[0])
            return this.PLAYER1WIN;
        else if (playerHavePieces[1])
            return this.PLAYER2WIN;
        else
            return this.DRAW;
        return this.NOTEND;
    },

    // return next position in direction or -1 if position out of board
    goNext: function(pos, dir)
    {
        var xi = pos % this.BOARDDIM;
        var yi = Math.floor(pos/this.BOARDDIM);
        switch (dir)
        {
            case this.MOVENE:
                if (xi+1 >= this.BOARDDIM || yi+1 >= this.BOARDDIM)
                    return -1;
                xi++; yi++;
                break;
            case this.MOVENW:
                if (xi-1 < 0 || yi+1 >= this.BOARDDIM)
                    return -1;
                xi--; yi++;
                break;
            case this.MOVESE:
                if (xi+1  >= this.BOARDDIM || yi-1 < 0)
                    return -1;
                xi++; yi--;
                break;
            case this.MOVESW:
                if (xi-1 < 0 || yi-1 < 0)
                    return -1;
                xi--; yi--;
                break;
        }
        // return new position
        return xi + yi*this.BOARDDIM;
    },

    isOponentPiece: function(pos)
    {
        var opMen = this.player1Move ? 'b' : 'w';
        var opKing = this.player1Move ? 'B' : 'W';
        return this.board[pos] == opMen || this.board[pos] == opKing;
    },

    isPlayerPiece: function(pos)
    {
        var opMen = this.player1Move ? 'w' : 'b';
        var opKing = this.player1Move ? 'W' : 'B';
        return this.board[pos] == opMen || this.board[pos] == opKing;
    },

    isGivenPlayerPiece: function (pos, player1)
    {
        var opMen = player1 ? 'w' : 'b';
        var opKing = player1 ? 'W' : 'B';
        return this.board[pos] == opMen || this.board[pos] == opKing;
    },

    isKing: function(pos)
    {
        return this.board[pos] == (this.player1Move ? 'W' : 'B');
    },

    isGivenKing: function (pos, player1)
    {
        return this.board[pos] == (player1 ? 'W' : 'B');
    },

    findFirstBeatPos: function(pos, dir, king)
    {
        var nextp = pos;
        var foundOpPiece = false;
        if ((nextp = this.goNext(nextp, dir)) >= 0)
             if (this.isOponentPiece(nextp))
                foundOpPiece = true;

        if (!foundOpPiece)
        {
            if (king && nextp >= 0 && this.board[nextp] == ' ')
                // for king, check for all position cross line
                // and if second position is empty
                while ((nextp = this.goNext(nextp, dir)) >= 0)
                {
                    if (this.isOponentPiece(nextp))
                    {
                        foundOpPiece = true;
                        break;
                    }
                    else if (this.board[nextp] != ' ')
                        break; // if your piece
                }
        }
        if (!foundOpPiece)
            return null;  // not found beat hit

        // check what is after oponent piece
        afterPiece = this.goNext(nextp, dir);
        if (afterPiece < 0 || this.board[afterPiece] != ' ')
            return null;  // no free place after piece
        // if free
        return [ nextp, afterPiece ];
    },

    // internal
    // find best beat sequence from specified position in  specified direction
    // beatArray - oponent piece position which have benn beaten
    // outBeatArray - array of best sequences contains positions of the beaten pieces
    // outStartArray - array of best sequences contains position from piece was beaten
    findBestBeatSeqsInt: function (pos, dir, startArray, beatArray,
            outStartArray, outBeatArray, king)
    {
        var beat = null;
        if (dir >= 0)
            beat = this.findFirstBeatPos(pos, dir, king);

        // check whether new beat can be done
        if (beat !== null && $.inArray(beat[0], beatArray) == -1)
        {
            // if we have beat and is not duplicate
            // if we have beat, then push into arrays
            beatArray.push(beat[0]);
            startArray.push(pos);
            var haveNextBeats = false;
            if (!king)
                for (var xdir = 0; xdir < 4; xdir++)
                    haveNextBeats |= this.findBestBeatSeqsInt(beat[1], xdir,
                        startArray, beatArray, outStartArray, outBeatArray, king);
            else
            {
                var nextp = beat[1];
                while (nextp >= 0 && this.board[nextp] == ' ')
                {
                    // if king check all position in cross line in this direction
                    for (var xdir = 0; xdir < 4; xdir++)
                        haveNextBeats |= this.findBestBeatSeqsInt(nextp, xdir,
                                startArray, beatArray,
                                outStartArray, outBeatArray, king);
                    nextp = this.goNext(nextp, dir);
                }
            }

            if (!haveNextBeats)
            {
                // no more next beats, put this result
                if (beatArray.length != 0)
                {
                    // put only if we have some beat
                    if (outBeatArray.length != 0)
                    {
                        if (outBeatArray[0].length < beatArray.length)
                        {
                            // if better, clear and put
                            setArraySingle(outStartArray, startArray.slice());
                            setArraySingle(outBeatArray, beatArray.slice());
                        }
                        else if (outBeatArray[0].length == beatArray.length)
                        {
                            // just put
                            outStartArray.push(startArray.slice());
                            outBeatArray.push(beatArray.slice());
                        }
                    }
                    else // just put
                    {
                        setArraySingle(outStartArray, startArray.slice());
                        setArraySingle(outBeatArray, beatArray.slice());
                    }
                }
            }

            startArray.pop();
            beatArray.pop();
            return true;
        }
        return false;
    },

    // find best beat sequence from specified position in  specified direction
    // outArray - array of best sequences
    findBestBeatsSeqs: function (pos, outStartArray, outBeatArray)
    {
        var king = this.isKing(pos);
        // before test, we remove piece from board
        var piece = this.board[pos];
        this.board[pos] = ' ';
        for (var dir = 0; dir < 4; dir++)
        {
            //echo "Next find for ", dir, "\n";
            var startArray = [];
            var beatArray = [];
            this.findBestBeatSeqsInt(pos, dir, startArray, beatArray,
                        outStartArray, outBeatArray, king);
        }
        // put back after test
        this.board[pos] = piece;
    }
};
