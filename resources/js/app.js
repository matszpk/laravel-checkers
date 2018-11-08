
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

setArraySingle = function(arr, v)
{
    arr.splice(0, arr.length, v);
};

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

    state: null,
    player1Move: false,
    lastBeat: null,

    fromData: function(newState, newPlayer1Move, newLastBeat)
    {
        this.state = newState;
        this.player1Move = newPlayer1Move;
        this.newLastBeat = newLastBeat;
    },

    startState: function()
    {
        this.player1Move = true;
        this.lastBeat = null;
        this.state = [
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

    toConsoleLog: function()
    {
        var s = "";
        for (var y = 0; y < this.BOARDDIM; y++)
        {
            for (var x = 0; x < this.BOARDDIM; x++)
                s += this.state[x+y*this.BOARDDIM]+'|';
            s += '\n--------------------\n';
        };
        console.log(s + "Player1:" + this.player1Move+"\n");
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
                    // next position
                    var dir = 0;
                    // determine direction
                    if (startPos < beatPos)
                        dir = (startPos + this.BOARDDIM-1 == beatPos) ?
                            this.MOVENW : this.MOVENE;
                    else
                        dir = (startPos - (this.BOARDDIM-1) == beatPos) ?
                            this.MOVESE : this.MOVESW;
                    // calculate position after beat
                    afterPiece = this.goNext(beatPos, dir);
                    if (endPos == afterPiece)
                        beatFound = true;
                }
            if (!beatFound)
                throw 'Move is not a mandatory beat';
            // make move
            var piece = this.state[startPos];
            this.state[startPos] = ' ';
            this.state[beatPos] = ' ';
            this.state[endPos] = piece; // your piece

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
                if (this.state[endPos] != ' ')
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
                        if (this.state[nextp] == ' ')
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
            piece = this.state[startPos];
            this.state[startPos] = ' ';
            this.state[endPos] = piece;
            this.handlePromotion(endPos);
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
            this.state[pos] = this.state[pos].toUpperCase();
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
            if (nextp >= 0 && this.state[nextp] == ' ')
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
                    if (nextp >= 0 && this.state[nextp] == ' ')
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
        return this.state[pos] == opMen || this.state[pos] == opKing;
    },

    isPlayerPiece: function(pos)
    {
        var opMen = this.player1Move ? 'w' : 'b';
        var opKing = this.player1Move ? 'W' : 'B';
        return this.state[pos] == opMen || this.state[pos] == opKing;
    },

    isGivenPlayerPiece: function (pos, player1)
    {
        var opMen = player1 ? 'w' : 'b';
        var opKing = player1 ? 'W' : 'B';
        return this.state[pos] == opMen || this.state[pos] == opKing;
    },

    isKing: function(pos)
    {
        return this.state[pos] == (this.player1Move ? 'W' : 'B');
    },

    isGivenKing: function (pos, player1)
    {
        return this.state[pos] == (player1 ? 'W' : 'B');
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
            if (king && nextp >= 0 && this.state[nextp] == ' ')
                // for king, check for all position cross line
                // and if second position is empty
                while ((nextp = this.goNext(nextp, dir)) >= 0)
                {
                    if (this.isOponentPiece(nextp))
                    {
                        foundOpPiece = true;
                        break;
                    }
                    else if (this.state[nextp] != ' ')
                        break; // if your piece
                }
        }
        if (!foundOpPiece)
            return null;  // not found beat hit

        // check what is after oponent piece
        afterPiece = this.goNext(nextp, dir);
        if (afterPiece < 0 || this.state[afterPiece] != ' ')
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
            for (var dir = 0; dir < 4; dir++)
                haveNextBeats |= this.findBestBeatSeqsInt(beat[1], dir, startArray,
                        beatArray, outStartArray, outBeatArray, king);

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
        var piece = this.state[pos];
        this.state[pos] = ' ';
        for (var dir = 0; dir < 4; dir++)
        {
            //echo "Next find for ", dir, "\n";
            var startArray = [];
            var beatArray = [];
            this.findBestBeatSeqsInt(pos, dir, startArray, beatArray,
                        outStartArray, outBeatArray, king);
        }
        // put back after test
        this.state[pos] = piece;
    }
};
