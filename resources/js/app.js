
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./utils');
require('./GameLogic');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Game = {
    cellSize: 50,
    boardDim: GameLogic.BOARDDIM,
    boardElem: null,
    focusedPos: null,
    choosenPos: null,
    choosenMove: null,
    choosenByKeyboard: false,
    doingMove: false,
    // prevent condition races between calls
    lock: false,
    
    init: function(newBoard, newPlayer1Move, newLastBeat, newPlayer1Plays) {
        this.boardElem = $("#checkers_board_main");
        GameLogic.fromData(newBoard, newPlayer1Move, newLastBeat, newPlayer1Plays);
        this.initEvents();
    },
    pieceElems: [],
    choosableMoveSet: null, // initial choosable start position with further moves
    choosable: null,    // current choosable

    cellClasses: {
        'w': 'checkers_board_men_white',
        'W': 'checkers_board_king_white',
        'b': 'checkers_board_men_black',
        'B': 'checkers_board_king_black'
    },

    clearBoard: function() {
        $(".checkers_board_piece", this.boardElem).remove();
        this.pieceElems = {};
    },

    displayBoard: function() {
        var board = GameLogic.board;
        this.choosable  = this.choosableMoveSet = GameLogic.getChoosable();
        this.clearBoard();
        for (var pos = 0; pos < board.length; pos++)
        {
            if (board[pos] == ' ')
                continue;
            var xi = pos % this.boardDim;
            var yi = Math.floor(pos/this.boardDim);
            var pieceElem = $("<div></div>").addClass(['checkers_board_piece',
                        this.cellClasses[board[pos]]])
                        // set piece id for fast getting position
                        .attr('id', 'checkers_board_piece'+yi+xi)
                        .css({ left: (this.cellSize*xi)+'px',
                               top: ((this.boardDim-1-yi)*this.cellSize)+'px' });
            pieceElem.mouseenter(function(e) { return Game.handleCellEnter(e); })
                .mouseleave(function(e) { return Game.handleCellLeave(e); })
                .click(function(e) { return Game.handlePieceClick(e); });
            this.boardElem.append(pieceElem);
            this.pieceElems[pos] = pieceElem;
        }
    },
    
    movePiece: function(startPos, endPos, callback) {
        console.log('make move:'+startPos+","+endPos);
        GameLogic.makeMove(startPos, endPos);
        var xi = endPos % this.boardDim;
        var yi = Math.floor(endPos/this.boardDim);
        var piece = this.pieceElems[startPos];
        this.pieceElems[endPos] = this.pieceElems[startPos];
        var beatPos = GameLogic.lastBeatenPiece;
        var beatenPiece = null;
        if (beatPos != null) {
            beatenPiece = this.pieceElems[beatPos];
            delete this.pieceElems[beatPos];
        }
        delete this.pieceElems[startPos];
        // handle animations
        piece.animate({
            left: (this.cellSize*xi),
            top: ((this.boardDim-1-yi)*this.cellSize)
        }, 500, 'swing', callback);
        if (beatPos != null)
            beatenPiece.fadeOut(450);
    },
    
    isChoosableStartPos: function(pos) {
        return this.choosable != null && (pos in this.choosable);
    },
    
    // initialize events
    initEvents: function() {
        $(document).keypress(function(e) { return Game.handleKey(e); });
        var cells = $(".checkers_board_cell", this.boardElem);
        cells.mouseenter(function(e) { return Game.handleCellEnter(e); })
            .mouseleave(function(e) { return Game.handleCellLeave(e); })
            .click(function(e) { return Game.handleCellClick(e); });
    },
    
    canHandleEvent: function() {
        if (!this.lock && (this.doingMove || !GameLogic.isPlayerMove()))
            return false;
        return true;
    },
    
    // handle key
    handleKey: function(event) {
        if (!this.canHandleEvent())
            return;
        
        this.lock = true;
        switch (event.keyCode) {
        case 37:    // left
            this.chooseLeftFocusedPos(); 
            break;
        case 38:    // up
            this.chooseUpFocusedPos();
            break;
        case 39:    // right
            this.chooseRightFocusedPos();
            break;
        case 40:    // down
            this.chooseDownFocusedPos();
            break;
        case 13:   // enter
            this.selectPieceOrMove();
            break;
        }
        this.lock = false;
    },
    
    chooseLeftFocusedPos: function() {
        var old = this.focusedPos;
        if (this.focusedPos == null)
            this.focusedPos = this.boardDim*this.boardDim;
        var bestPos = null;
        var bestDist = 1000000;
        for (xxpos in this.choosable) {
            var xpos = parseInt(xxpos);
            if (this.focusedPos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos/this.boardDim);
            var fxi = this.focusedPos % this.boardDim;
            if (this.boardDim*this.boardDim == this.focusedPos)
                fxi = this.boardDim;
            var fyi = Math.floor(this.focusedPos/this.boardDim);
            if (xxi >= fxi)
                continue;
            var dist = fxi - xxi + Math.abs(fyi-xyi);
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
    
    chooseRightFocusedPos: function() {
        var old = this.focusedPos;
        if (this.focusedPos == null)
            this.focusedPos = -1;
        var bestPos = null;
        var bestDist = 1000000;
        for (var xxpos in this.choosable) {
            var xpos = parseInt(xxpos);
            if (this.focusedPos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos/this.boardDim);
            var fxi = this.focusedPos % this.boardDim;
            var fyi = Math.floor(this.focusedPos/this.boardDim);
            if (xxi <= fxi)
                continue;
            var dist = xxi - fxi + Math.abs(fyi-xyi);
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
    
    chooseUpFocusedPos: function() {
        var old = this.focusedPos;
        if (this.focusedPos == null)
            this.focusedPos = this.boardDim*this.boardDim;
        var bestPos = null;
        var bestDist = 1000000;
        for (xxpos in this.choosable) {
            var xpos = parseInt(xxpos);
            if (this.focusedPos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos/this.boardDim);
            var fxi = this.focusedPos % this.boardDim;
            var fyi = Math.floor(this.focusedPos/this.boardDim);
            if (xyi >= fyi)
                continue;
            var dist = fyi - xyi + Math.abs(fxi-xxi);
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
    
    chooseDownFocusedPos: function() {
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
            var xyi = Math.floor(xpos/this.boardDim);
            var fxi = this.focusedPos % this.boardDim;
            var fyi = Math.floor(this.focusedPos/this.boardDim);
            if (xyi <= fyi)
                continue;
            var dist = xyi - fyi + Math.abs(fxi-xxi);
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
    
    updateFocusedPos: function(piecePos, byKeyboard) {
        if (this.choosenByKeyboard && !byKeyboard && piecePos==null)
            // prevent obsolete unselect cell when choosen by keyboard
            return;
        if (piecePos != null && piecePos != this.focusedPos)
            this.getBoardCell(piecePos).addClass("checkers_board_choosen");
        if (this.focusedPos != null)
            this.getBoardCell(this.focusedPos).removeClass("checkers_board_choosen");
        this.focusedPos = piecePos;
        this.choosenByKeyboard = byKeyboard;
    },
    
    getBoardCell: function(pos) {
        var xi = pos % this.boardDim;
        var yi = Math.floor(pos/this.boardDim);
        var id = "checkers_board_cell" + yi + xi;
        return $("#"+id, this.boardCell);
    },
    
    getPosFromDOMElem: function(elem) {
        var id = $(elem).attr('id');
        return parseInt(id.substring(id.length-2));
    },
    
    handleCellEnter: function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        if (this.isChoosableStartPos(pos))
            this.updateFocusedPos(pos, false);
        this.lock = false;
    },
    
    handleCellLeave: function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        this.updateFocusedPos(null, false);
        this.lock = false;
    },
    
    handleCellClick: function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        if (this.choosenPos != null && pos in this.choosable)
            this.selectPieceOrMove();
        else
            this.lock = false;
    },
    
    handlePieceClick: function(event) {
        if (!this.canHandleEvent())
            return;
        this.lock = true;
        var pos = this.getPosFromDOMElem(event.target);
        if (this.choosenPos != null || this.isChoosableStartPos(pos)) {
            this.updateFocusedPos(pos, false);
            this.selectPieceOrMove();
        }
        else
            this.lock = false;
    },
    
    // select piece or move
    selectPieceOrMove: function() {
        if (this.focusedPos == null)
            return false;
        var fpos = this.focusedPos;
        this.updateFocusedPos(null, true);
        if (this.choosenPos == null) {
            // if no piece choosen
            this.choosenPos = fpos;
            this.pieceElems[this.choosenPos].addClass("checkers_board_choosen");
            this.focusedPos = null;
            this.choosable = arrayToSetObject(this.choosableMoveSet[fpos]);
        } else {
            // if piece choosen, then move will be choosen
            this.focusedPos = null;
            this.choosenMove = [this.choosenPos, fpos];
            this.choosenPos = null;
            // reset choosable sets
            this.choosable = this.choosableMoveSet = null;
            console.log("Move from "+this.choosenMove[0]+" to "+
                    this.choosenMove[1]+" choosen");
            // unfocus piece
            this.pieceElems[this.choosenMove[0]].removeClass("checkers_board_choosen");
            
            this.movePiece(this.choosenMove[0], this.choosenMove[1], function() {
                // handle state again
                Game.handleState();
            });
        }
    },

    handleState: function() {
        this.lock = true;
        console.log('handle state int');
        if (this.choosenMove != null) {
            this.choosenMove = null;
        }
        if (GameLogic.isPlayerMove()) {
            // if current player plays
            
        } else {
            // otherwise player doing move
            
        }
        this.lock = false;
    }
}
