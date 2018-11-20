/**
 * First we will load all of this project's JavaScript dependencies
 */

require('./bootstrap');
require('./utils');
require('./GameLogic');

// main class of Game

Game = {
    cellSize : 50,
    boardDim : GameLogic.BOARDDIM,
    boardElem : null,
    movesElem : null,
    titleElem : null,
    focusedPos : null,
    choosenPos : null,
    choosenMove : null,
    choosenByKeyboard : false,
    doingMove : false,
    timerHandle : null,
    moves : [], // array of moves -> [ start, end, done_by_player1 ]
    doneMoves : 0, // number of done moves
    gameEnd : GameLogic.NOTEND,
    // prevent condition races between calls
    lock : false,

    init : function(newBoard, newPlayer1Move, newLastBeat, newPlayer1Plays) {
        this.boardElem = $("#checkers_board_main");
        this.movesElem = $("#checkers_movelist");
        this.titleElem = $("#checkers_game_title");
        this.statusElem = $("#checkers_gamestatus");
        GameLogic.fromData(newBoard, newPlayer1Move, newLastBeat,
                newPlayer1Plays);
        this.initEvents();
        this.initTimer();
    },
    initMoves : function(moves) {
        this.moves = moves;
        this.doneMoves = this.moves.length;
    },

    // initialize events
    initEvents : function() {
        $(document).keypress(function(e) {
            return Game.handleKey(e);
        });
        var cells = $(".checkers_board_cell", this.boardElem);
        cells.mouseenter(function(e) {
            return Game.handleCellEnter(e);
        }).mouseleave(function(e) {
            return Game.handleCellLeave(e);
        }).click(function(e) {
            return Game.handleCellClick(e);
        });
    },

    initTimer : function() {
        this.timerHandle = setInterval(function() {
            Game.handleTimer();
        }, 1000);
    },

    pieceElems : [],
    choosableMoveSet : null, // initial choosable start position with further
                                // moves
    choosable : null, // current choosable

    // for update state
    handleTimer : function() {
        if (this.lock)
            return;
        checkersAxiosGet(GameStateURL, function(response) {
            if (Game.lock)
                return;
            Game.lock = true;
            var data = response.data;
            // update game title
            Game.titleElem.text(data.gameName);

            if (Game.moves.length == data.moves.length) {
                Game.lock = false;
                return; // no change
            }
            Game.moves = data.moves;
            console.log("change state");
            Game.resetSelection();
            Game.doingMoves();
        });
    },
    
    doingMoves: function() {
        if (this.doneMoves < this.moves.length)
            this.movePiece(this.moves[this.doneMoves][0],
                this.moves[this.doneMoves][1], function() {
                Game.doneMoves++;
                Game.doingMoves(); 
            });
        else
            // if finish, then handle state of game
            this.handleState();
    },

    resetSelection : function() {
        this.choosable = this.choosableMoveSet = null;
        this.focusedPos = this.choosenPos = this.choosenMove = null;
        $(".checkers_board_cell", this.boardElem).removeClass(
                "checkers_board_choosen");
        $.each(this.pieceElems, function(i, elem) {
            elem.removeClass("checkers_board_choosen");
        });
    },

    cellClasses : {
        'w' : 'checkers_board_men_white',
        'W' : 'checkers_board_king_white',
        'b' : 'checkers_board_men_black',
        'B' : 'checkers_board_king_black'
    },

    clearBoard : function() {
        $(".checkers_board_piece", this.boardElem).remove();
        this.pieceElems = {};
    },

    displayBoard : function() {
        var board = GameLogic.board;
        this.choosable = this.choosableMoveSet = null;
        if (GameLogic.isPlayerMove())
            this.choosable = this.choosableMoveSet = GameLogic.getChoosable();
        this.clearBoard();
        for (var pos = 0; pos < board.length; pos++) {
            if (board[pos] == ' ')
                continue;
            var xi = pos % this.boardDim;
            var yi = Math.floor(pos / this.boardDim);
            var pieceElem = $("<div></div>").addClass(
                    [ 'checkers_board_piece', this.cellClasses[board[pos]] ])
            // set piece id for fast getting position
            .attr('id', 'checkers_board_piece' + yi + xi).css({
                left : (this.cellSize * xi) + 'px',
                top : ((this.boardDim - 1 - yi) * this.cellSize) + 'px'
            });
            pieceElem.mouseenter(function(e) {
                return Game.handleCellEnter(e);
            }).mouseleave(function(e) {
                return Game.handleCellLeave(e);
            }).click(function(e) {
                return Game.handlePieceClick(e);
            });
            this.boardElem.append(pieceElem);
            this.pieceElems[pos] = pieceElem;
        }
    },

    displayMoves : function() {
        this.movesElem.empty();
        for (var i = 0; i < this.moves.length; i++) {
            var move = this.moves[i];
            var sxi = move[0] % this.boardDim;
            var syi = Math.floor(move[0] / this.boardDim);
            var exi = move[1] % this.boardDim;
            var eyi = Math.floor(move[1] / this.boardDim);
            var moveElem = $("<div></div>").addClass(
                    "checkers_move_" + (move[2] ? 'white' : 'black'));
            moveElem.text((i + 1) + ". " + String.fromCharCode(97 + syi) + sxi
                    + " " + String.fromCharCode(97 + eyi) + exi);
            this.movesElem.append(moveElem);
        }
        // scroll to down
        this.movesElem.scrollTop(this.movesElem[0].scrollHeight);
    },

    movePiece : function(startPos, endPos, callback) {
        this.doingMove = true;
        GameLogic.makeMove(startPos, endPos);
        var xi = endPos % this.boardDim;
        var yi = Math.floor(endPos / this.boardDim);
        var piece = this.pieceElems[startPos];
        this.pieceElems[endPos] = this.pieceElems[startPos];
        piece.attr('id', 'checkers_board_piece' + yi + xi);
        var beatPos = GameLogic.lastBeatenPiece;
        var beatenPiece = null;
        if (beatPos != null) {
            beatenPiece = this.pieceElems[beatPos];
            delete this.pieceElems[beatPos];
        }
        delete this.pieceElems[startPos];
        // handle animations
        piece.animate({
            left : (this.cellSize * xi),
            top : ((this.boardDim - 1 - yi) * this.cellSize)
        }, 500, 'swing', function() {
            piece.removeClass();
            var board = GameLogic.board;
            piece.addClass([ 'checkers_board_piece', Game.cellClasses[board[endPos]] ])
            callback();
        });
        if (beatPos != null)
            beatenPiece.fadeOut(450);
    },

    isChoosablePos : function(pos) {
        return this.choosable != null && (pos in this.choosable);
    },

    canHandleEvent : function() {
        if (this.lock || this.doingMove || !GameLogic.isPlayerMove()
                || this.gameEnd != GameLogic.NOTEND)
            return false;
        return true;
    },

    // handle key
    handleKey : function(event) {
        if (!this.canHandleEvent())
            return;

        this.lock = true;
        switch (event.keyCode) {
        case 37: // left
            this.chooseLeftFocusedPos();
            break;
        case 38: // up
            this.chooseUpFocusedPos();
            break;
        case 39: // right
            this.chooseRightFocusedPos();
            break;
        case 40: // down
            this.chooseDownFocusedPos();
            break;
        case 13: // enter
            this.selectPieceOrMove();
            return;
        }
        if (event.which == 32) // key space
            this.selectPieceOrMove();
        else
            this.lock = false;
    },

    chooseLeftFocusedPos : function() {
        var old = this.focusedPos;
        if (this.focusedPos == null)
            this.focusedPos = this.boardDim * this.boardDim;
        var bestPos = null;
        var bestDist = 1000000;
        for (xxpos in this.choosable) {
            var xpos = parseInt(xxpos);
            if (this.focusedPos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos / this.boardDim);
            var fxi = this.focusedPos % this.boardDim;
            if (this.boardDim * this.boardDim == this.focusedPos)
                fxi = this.boardDim;
            var fyi = Math.floor(this.focusedPos / this.boardDim);
            if (xxi >= fxi)
                continue;
            var dist = fxi - xxi + Math.abs(fyi - xyi);
            if (dist < bestDist) {
                bestPos = xpos;
                bestDist = dist;
            }
        }
        if (bestPos != null)
            this.updateFocusedPos(bestPos, true);
        else
            this.focusedPos = old;
    },

    chooseRightFocusedPos : function() {
        var old = this.focusedPos;
        if (this.focusedPos == null)
            this.focusedPos = -1;
        var bestPos = null;
        var bestDist = 1000000;
        for ( var xxpos in this.choosable) {
            var xpos = parseInt(xxpos);
            if (this.focusedPos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos / this.boardDim);
            var fxi = this.focusedPos % this.boardDim;
            var fyi = Math.floor(this.focusedPos / this.boardDim);
            if (xxi <= fxi)
                continue;
            var dist = xxi - fxi + Math.abs(fyi - xyi);
            if (dist < bestDist) {
                bestPos = xpos;
                bestDist = dist;
            }
        }
        if (bestPos != null)
            this.updateFocusedPos(bestPos, true);
        else
            this.focusedPos = old;
    },

    chooseDownFocusedPos : function() {
        var old = this.focusedPos;
        if (this.focusedPos == null)
            this.focusedPos = this.boardDim * this.boardDim;
        var bestPos = null;
        var bestDist = 1000000;
        for (xxpos in this.choosable) {
            var xpos = parseInt(xxpos);
            if (this.focusedPos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos / this.boardDim);
            var fxi = this.focusedPos % this.boardDim;
            var fyi = Math.floor(this.focusedPos / this.boardDim);
            if (xyi >= fyi)
                continue;
            var dist = fyi - xyi + Math.abs(fxi - xxi);
            if (dist < bestDist) {
                bestPos = xpos;
                bestDist = dist;
            }
        }
        if (bestPos != null)
            this.updateFocusedPos(bestPos, true);
        else
            this.focusedPos = old;
    },

    chooseUpFocusedPos : function() {
        var old = this.focusedPos;
        if (this.focusedPos == null)
            this.focusedPos = -1;
        var bestPos = null;
        var bestDist = 1000000;
        for (xxpos in this.choosable) {
            var xpos = parseInt(xxpos);
            if (this.focusedPos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos / this.boardDim);
            var fxi = this.focusedPos % this.boardDim;
            var fyi = Math.floor(this.focusedPos / this.boardDim);
            if (xyi <= fyi)
                continue;
            var dist = xyi - fyi + Math.abs(fxi - xxi);
            if (dist < bestDist) {
                bestPos = xpos;
                bestDist = dist;
            }
        }
        if (bestPos != null)
            this.updateFocusedPos(bestPos, true);
        else
            this.focusedPos = old;
    },

    updateFocusedPos : function(piecePos, byKeyboard) {
        if (this.choosenByKeyboard && !byKeyboard && piecePos == null)
            // prevent obsolete unselect cell when choosen by keyboard
            return;
        if (piecePos != null && piecePos != this.focusedPos)
            this.getBoardCell(piecePos).addClass("checkers_board_choosen");
        if (this.focusedPos != null)
            this.getBoardCell(this.focusedPos).removeClass(
                    "checkers_board_choosen");
        this.focusedPos = piecePos;
        this.choosenByKeyboard = byKeyboard;
    },

    getBoardCell : function(pos) {
        var xi = pos % this.boardDim;
        var yi = Math.floor(pos / this.boardDim);
        var id = "checkers_board_cell" + yi + xi;
        return $("#" + id, this.boardCell);
    },

    getPosFromDOMElem : function(elem) {
        var id = $(elem).attr('id');
        return parseInt(id.substring(id.length - 2));
    },

    handleCellEnter : function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        if (this.isChoosablePos(pos))
            this.updateFocusedPos(pos, false);
        this.lock = false;
    },

    handleCellLeave : function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        this.updateFocusedPos(null, false);
        this.lock = false;
    },

    handleCellClick : function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        if (this.choosenPos != null && this.isChoosablePos(pos))
            this.selectPieceOrMove();
        else
            this.lock = false;
    },

    handlePieceClick : function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        if (this.isChoosablePos(pos)) {
            this.updateFocusedPos(pos, false);
            this.selectPieceOrMove();
        } else
            this.lock = false;
    },

    // select piece or move
    selectPieceOrMove : function() {
        if (this.focusedPos == null)
            return false;
        var fpos = this.focusedPos;
        this.updateFocusedPos(null, true);
        if (GameLogic.board[fpos] != ' ') {
            if (this.choosenPos!=null && fpos != this.choosenPos)
                this.pieceElems[this.choosenPos].removeClass("checkers_board_choosen");
            // if piece choosen first
            this.choosenPos = fpos;
            this.pieceElems[this.choosenPos].addClass("checkers_board_choosen");
            this.focusedPos = null;
            //this.choosable = arrayToSetObject(this.choosableMoveSet[fpos]);
            this.choosable = $.extend(arrayToSetObject(this.choosableMoveSet[fpos]),
                    this.choosableMoveSet);
            delete this.choosable[fpos];
            this.lock = false;
        } else {
            // if move choosen (empty cell)
            this.focusedPos = null;
            this.choosenMove = [ this.choosenPos, fpos ];
            this.choosenPos = null;
            // reset choosable sets
            this.choosable = this.choosableMoveSet = null;
            console.log("Move from " + this.choosenMove[0] + " to "
                    + this.choosenMove[1] + " choosen");
            // unfocus piece
            this.pieceElems[this.choosenMove[0]]
                    .removeClass("checkers_board_choosen");

            this.movePiece(this.choosenMove[0], this.choosenMove[1],
                    function() {
                        // if end of move
                        Game.doingMove = false;
                        Game.doneMoves++;
                        // put to server
                        Game.postMakeMove();
                    });
        }
    },

    postMakeMove : function() {
        checkersAxiosPost(GameMakeMoveURL, {
            startPos : this.choosenMove[0],
            endPos : this.choosenMove[1],
            countMoves : this.moves.length
        }, function(response) {
            // handle state again
            Game.handleState();
        },
        // error
        function(error) {
            if (!error.response && error.request)
                // handle timeout
                Game.installNoConnTimeout();
        });
    },

    installNoConnTimeout : function() {
        // try again after second
        setTimeout(function() {
            Game.postMakeMove();
        }, 1000);
    },
    
    resultNames: [ '', 'winner1', 'winner2', 'draw' ],

    handleState : function() {
        console.log('handle state');
        this.lock = true;
        this.choosenMove = null;
        this.gameEnd = GameLogic.checkGameEnd();
        if (this.gameEnd == GameLogic.NOTEND) {
            if (GameLogic.isPlayerMove()) {
                // if current player plays
                this.statusElem.text(Lang.get('game.youDoMove'));
                this.choosable = this.choosableMoveSet = null;
                if (GameLogic.isPlayerMove())
                    this.choosable = this.choosableMoveSet = GameLogic.getChoosable();
            }
            else
                // otherwise player doing move
                this.statusElem.text(Lang.get('game.oponentDoMove'));
        } else {
            // if end
            var msgText = Lang.get('game.result_'+this.resultNames[this.gameEnd]);
            this.statusElem.text(msgText);
            displayMessage(msgText);
            if (this.timerHandle != null)
                clearInterval(this.timerHandle);
            this.timerHandle = null;
        }
        this.doingMove = false;
        this.lock = false;
    }
}
