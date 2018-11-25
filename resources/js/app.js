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
    doingMovePiece: false,
    timerHandle : null,
    moves : [], // array of moves -> [ start, end, done_by_player1 ]
    doneMoves : 0, // number of done moves
    gameEnd : GameLogic.NOTEND,
    player1: null,
    player2: null,
    // prevent condition races between calls
    lock : false,
    replayMode: false,
    replay: false,
    
    // initialize Game object to play
    init : function(newBoard, newPlayer1Move, newLastBeat, newPlayer1Plays) {
        this.replayMode = false;
        this.replay = false;
        this.initElems();
        GameLogic.fromData(newBoard, newPlayer1Move, newLastBeat,
                newPlayer1Plays);
        this.initEvents();
        this.initTimer();
    },
    
    // initialize Game object to replay
    initReplay : function(newBoard, newPlayer1Move, newLastBeat) {
        this.initElems();
        this.replayMode = true;
        this.replay = false;
        GameLogic.fromData(newBoard, newPlayer1Move, newLastBeat, null);
        // button handlers
        $("#checkers_replay_replay").click(function() {
            Game.doReplay();
        });
        $("#checkers_replay_stop").click(function() {
            Game.doStop();
        });
        $("#checkers_replay_continue").click(function() {
            Game.doContinue();
        });
    },
    
    // initialize HTML elements of game
    initElems: function() {
        this.boardElem = $("#checkers_board_main");
        this.movesElem = $("#checkers_movelist");
        this.titleElem = $("#checkers_game_title");
        this.statusElem = $("#checkers_gamestatus");
    },
    
    // initialize moves
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

    // initialize timer while playing (for updating state)
    initTimer : function() {
        this.timerHandle = setInterval(function() {
            Game.handleTimer();
        }, 1000);
    },
    
    /*
     * REPLAY stuff
     */
    
    // handle Replay button (replay)
    doReplay: function() {
        if (this.replay || this.doingMovePiece) {
            this.replay = false;
            setTimeout(function() {
                Game.doingMovePiece = false;
                Game.doReplay();
            }, 1200);
            return;
        }
        this.replay = true;
        this.doneMoves = 0;
        GameLogic.startState();
        this.displayBoard();
        this.movesElem.empty();
        this.statusElem.text(Lang.get('game.replaying'));
        this.doingMoves();
    },
    
    // handle Continue button (continue replay)
    doContinue: function() {
        if (this.replay)
            return;
        if (this.doingMovePiece) {
            this.replay = false;
            setTimeout(function() {
                Game.doingMovePiece = false;
                Game.doContinue();
            }, 1200);
            return;
        }
        this.replay = true;
        this.statusElem.text(Lang.get('game.replaying'));
        this.doingMoves();
    },
    
    // handle Stop button (stop replay)
    doStop: function() {
        this.statusElem.text(Lang.get('game.replayStopped'));
        this.replay = false;
    },
    /*
     * end of REPLAY stuff
     */

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
            
            if (Game.player1=='-' && data.player1!='-')
                displayMessage(Lang.get('game.youOponentWillBe')+' '+data.player1);
            else if (Game.player2=='-' && data.player2!='-')
                displayMessage(Lang.get('game.youOponentWillBe')+' '+data.player2);
            Game.player1 = data.player1;
            Game.player2 = data.player2;

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
    
    // doing moves
    // used while updating state to making done moves by oponent moves
    doingMoves: function() {
        if (this.doneMoves < this.moves.length) {
            // update move list
            setTimeout(function() {
                Game.updateDisplayMoves(Game.doneMoves, Game.doneMoves+1);
                Game.movePiece(Game.moves[Game.doneMoves][0],
                    Game.moves[Game.doneMoves][1], function() {
                    Game.doneMoves++;
                    if (Game.replayMode && !Game.replay)
                        return; // do nothing, just stop
                    Game.doingMoves();
                });
            }, 500);
        } else
            // if finish, then handle state of game
            this.handleState();
    },

    // reset move selection (piece or same move end position)
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

    // clear board
    clearBoard : function() {
        $(".checkers_board_piece", this.boardElem).remove();
        this.pieceElems = {};
    },

    // display game board
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
            if (!this.replayMode) {
                pieceElem.mouseenter(function(e) {
                    return Game.handleCellEnter(e);
                }).mouseleave(function(e) {
                    return Game.handleCellLeave(e);
                }).click(function(e) {
                    return Game.handlePieceClick(e);
                });
            }
            this.boardElem.append(pieceElem);
            this.pieceElems[pos] = pieceElem;
        }
    },

    // display moves
    displayMoves : function() {
        this.movesElem.empty();
        this.updateDisplayMoves();
    },
    
    // update moves in list
    // moveCount - current move count, newMoveCount - new move count after update
    updateDisplayMoves: function(moveCount, newMoveCount) {
        if (moveCount == null)
            moveCount = 0;
        if (newMoveCount == null)
            newMoveCount = this.moves.length;
        for (var i = moveCount; i < newMoveCount; i++) {
            var move = this.moves[i];
            var sxi = move[0] % this.boardDim;
            var syi = Math.floor(move[0] / this.boardDim);
            var exi = move[1] % this.boardDim;
            var eyi = Math.floor(move[1] / this.boardDim);
            var moveElem = $("<div></div>").addClass(
                    "checkers_move_" + (move[2] ? 'white' : 'black'));
            moveElem.text((i+1) + ". " + String.fromCharCode(97 + sxi) + (syi+1)
                    + " " + String.fromCharCode(97 + exi) + (eyi+1));
            this.movesElem.append(moveElem);
        }
        // scroll to down
        this.movesElem.scrollTop(this.movesElem[0].scrollHeight);
    },
    
    // add new move
    addMove: function(move) {
        this.moves.push(move);
        this.updateDisplayMoves(this.moves.length-1);
    },

    // move piece in the game board
    movePiece : function(startPos, endPos, callback) {
        this.doingMove = true;
        this.doingMovePiece = true;
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
            Game.doingMovePiece = false;
            callback();
        });
        if (beatPos != null)
            beatenPiece.fadeOut(450);
    },

    // if choosable position
    isChoosablePos : function(pos) {
        return this.choosable != null && (pos in this.choosable);
    },

    // true if can handle event
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

    // move choose position to nearest left choosable position 
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

    // move choose position to nearest right choosable position
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

    // move choose position to nearest down choosable position
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

    // move choose position to nearest up choosable position
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

    // update focused position
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

    // get board cell from position
    getBoardCell : function(pos) {
        var xi = pos % this.boardDim;
        var yi = Math.floor(pos / this.boardDim);
        var id = "checkers_board_cell" + yi + xi;
        return $("#" + id, this.boardCell);
    },

    // get position from DOM element (piece of board cell HTML element)
    getPosFromDOMElem : function(elem) {
        var id = $(elem).attr('id');
        return parseInt(id.substring(id.length - 2));
    },

    // handle mouse entering on the cell
    handleCellEnter : function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        if (this.isChoosablePos(pos))
            this.updateFocusedPos(pos, false);
        this.lock = false;
    },

    // handle mouse leaving from the cell
    handleCellLeave : function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        this.updateFocusedPos(null, false);
        this.lock = false;
    },

    // handle clicking on the cell
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

    // handle clicking on the piece
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
            this.choosenMove = [ this.choosenPos, fpos, GameLogic.player1Plays ];
            this.choosenPos = null;
            // reset choosable sets
            this.choosable = this.choosableMoveSet = null;
            console.log("Move from " + this.choosenMove[0] + " to "
                    + this.choosenMove[1] + " choosen");
            // unfocus piece
            this.pieceElems[this.choosenMove[0]]
                    .removeClass("checkers_board_choosen");
            
            this.doMakeMove();
        }
    },
    
    // make move after choosing move by player
    doMakeMove: function() {
        this.movePiece(this.choosenMove[0], this.choosenMove[1],
                function() {
                    // if end of move
                    Game.doingMove = false;
                    Game.doneMoves++;
                    // put to server
                    Game.postMakeMove();
                });
    },

    // send move to server for verification
    postMakeMove : function() {
        checkersAxiosPost(GameMakeMoveURL, {
            startPos : this.choosenMove[0],
            endPos : this.choosenMove[1],
            countMoves : this.moves.length
        }, function(response) {
            // add move
            Game.addMove(Game.choosenMove);
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

    // install timeout to retry sending move to server
    installNoConnTimeout : function() {
        // try again after second
        setTimeout(function() {
            Game.postMakeMove();
        }, 1000);
    },
    
    resultNames: [ '', 'winner1', 'winner2', 'draw' ],

    // handle Game state
    handleState : function() {
        this.lock = true;
        this.choosenMove = null;
        this.gameEnd = GameLogic.checkGameEnd();
        if (this.gameEnd == GameLogic.NOTEND) {
            if (!this.replayMode) {
                if (GameLogic.isPlayerMove()) {
                    // if current player plays
                    this.statusElem.text(Lang.get('game.youDoMove'));
                    this.choosable = this.choosableMoveSet = GameLogic.getChoosable();
                    var chkeys = Object.keys(this.choosable);
                    if (chkeys.length == 1 && this.choosableMoveSet[chkeys[0]].length == 1) {
                        // automatically make move
                        this.choosenMove = [ parseInt(chkeys[0]),
                                this.choosableMoveSet[chkeys[0]][0], GameLogic.player1Plays ];
                        this.choosable = this.choosableMoveSet = null;
                        setTimeout(function() {
                            Game.doMakeMove();
                        }, 500);
                    }
                }
                else
                    // otherwise player doing move
                    this.statusElem.text(Lang.get('game.oponentDoMove'));
            } else {
                this.statusElem.text(Lang.get('game.replayFinished'));
                this.replay = false;
            }
        } else {
            // if end
            var msgText = Lang.get('game.result_'+this.resultNames[this.gameEnd]);
            var statusText = msgText;
            if (this.replayMode)
                statusText += '. ' + Lang.get('game.replayFinished');
            this.statusElem.text(statusText);
            displayMessage(msgText);
            if (this.timerHandle != null)
                clearInterval(this.timerHandle);
            this.timerHandle = null;
            if (this.replayMode)
                this.replay = false;
        }
        this.doingMove = false;
        this.lock = false;
    }
}
