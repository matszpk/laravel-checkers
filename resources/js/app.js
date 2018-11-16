
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
    focusedPiecePos: null,
    choosenPiecePos: null,
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
    choosable: null,

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
        this.choosable = GameLogic.getChoosable();
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
                .mouseleave(function(e) { return Game.handleCellLeave(e); });
            this.boardElem.append(pieceElem);
            this.pieceElems[pos] = pieceElem;
        }
    },
    
    choosePiece: function(piecePos) {
    },
    
    chooseMove: function(endPos) {
    },
    
    movePiece: function(startPos, endPos, callback) {
    },
    
    beatPiece: function(startPos, beatenPos, endPos, callback) {
    },
    
    isChoosableStartPos: function(pos) {
        return this.choosable != null && (pos in this.choosable);
    },
    
    // initialize events
    initEvents: function() {
        $(document).keypress(function(e) { return Game.handleKey(e); });
        var cells = $(".checkers_board_cell", this.boardElem);
        cells.mouseenter(function(e) { return Game.handleCellEnter(e); })
            .mouseleave(function(e) { return Game.handleCellLeave(e); });
    },
    
    // handle key
    handleKey: function(event) {
        if (this.doingMove || !GameLogic.isPlayerMove())
            return;
        
        switch (event.keyCode) {
        case 37:    // left
            this.chooseLeftFocusedPiecePos(); 
            break;
        case 38:    // up
            this.chooseUpFocusedPiecePos();
            break;
        case 39:    // right
            this.chooseRightFocusedPiecePos();
            break;
        case 40:    // down
            this.chooseDownFocusedPiecePos();
            break;
        case 13:   // enter
            break;
        }
    },
    
    chooseLeftFocusedPiecePos: function() {
        //console.log('left');
        var old = this.focusedPiecePos;
        if (this.focusedPiecePos == null)
            this.focusedPiecePos = this.boardDim*this.boardDim;
        var bestPos = null;
        var bestDist = 1000000;
        for (xpos in this.choosable) {
            if (this.focusedPiecePos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos/this.boardDim);
            var fxi = this.focusedPiecePos % this.boardDim;
            if (this.boardDim*this.boardDim == this.focusedPiecePos)
                fxi = this.boardDim;
            var fyi = Math.floor(this.focusedPiecePos/this.boardDim);
            if (xxi >= fxi)
                continue;
            var dist = fxi - xxi + Math.abs(fyi-xyi);
            if (dist < bestDist) {
                bestPos = xpos;
                bestDist = dist;
            }
        }
        if (bestPos != null)
            this.updateFocusedPiecePos(bestPos, true);
        else
            this.focusedPiecePos = old;
    },
    
    chooseRightFocusedPiecePos: function() {
        //console.log('right');
        var old = this.focusedPiecePos;
        if (this.focusedPiecePos == null)
            this.focusedPiecePos = -1;
        var bestPos = null;
        var bestDist = 1000000;
        for (xpos in this.choosable) {
            if (this.focusedPiecePos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos/this.boardDim);
            var fxi = this.focusedPiecePos % this.boardDim;
            var fyi = Math.floor(this.focusedPiecePos/this.boardDim);
            if (xxi <= fxi)
                continue;
            var dist = xxi - fxi + Math.abs(fyi-xyi);
            if (dist < bestDist) {
                bestPos = xpos;
                bestDist = dist;
            }
        }
        if (bestPos != null)
            this.updateFocusedPiecePos(bestPos, true);
        else
            this.focusedPiecePos = old;
    },
    
    chooseUpFocusedPiecePos: function() {
        //console.log('up');
        var old = this.focusedPiecePos;
        if (this.focusedPiecePos == null)
            this.focusedPiecePos = this.boardDim*this.boardDim;
        var bestPos = null;
        var bestDist = 1000000;
        for (xpos in this.choosable) {
            if (this.focusedPiecePos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos/this.boardDim);
            var fxi = this.focusedPiecePos % this.boardDim;
            var fyi = Math.floor(this.focusedPiecePos/this.boardDim);
            if (xyi >= fyi)
                continue;
            var dist = fyi - xyi + Math.abs(fxi-xxi);
            if (dist < bestDist) {
                bestPos = xpos;
                bestDist = dist;
            }
        }
        if (bestPos != null)
            this.updateFocusedPiecePos(bestPos, true);
        else
            this.focusedPiecePos = old;
    },
    
    chooseDownFocusedPiecePos: function() {
        //console.log('down');
        var old = this.focusedPiecePos;
        if (this.focusedPiecePos == null)
            this.focusedPiecePos = -1;
        var bestPos = null;
        var bestDist = 1000000;
        for (xpos in this.choosable) {
            if (this.focusedPiecePos == xpos)
                continue;
            var xxi = xpos % this.boardDim;
            var xyi = Math.floor(xpos/this.boardDim);
            var fxi = this.focusedPiecePos % this.boardDim;
            var fyi = Math.floor(this.focusedPiecePos/this.boardDim);
            if (xyi <= fyi)
                continue;
            var dist = xyi - fyi + Math.abs(fxi-xxi);
            if (dist < bestDist) {
                bestPos = xpos;
                bestDist = dist;
            }
        }
        if (bestPos != null)
            this.updateFocusedPiecePos(bestPos, true);
        else
            this.focusedPiecePos = old;
    },
    
    updateFocusedPiecePos: function(piecePos, byKeyboard) {
        if (this.choosenByKeyboard && !byKeyboard && piecePos==null)
            // prevent obsolete unselect cell when choosen by keyboard
            return;
        if (piecePos != null && piecePos != this.focusedPiecePos)
            this.getBoardCell(piecePos).addClass("checkers_board_choosen");
        if (this.focusedPiecePos != null)
            this.getBoardCell(this.focusedPiecePos).removeClass("checkers_board_choosen");
        this.focusedPiecePos = piecePos;
        this.choosenByKeyboard = byKeyboard;
    },
    
    selectPiecePos: function() {
        if (this.focusedPiecePos == null)
            return false;
        console.log("selected: "+this.focusedPiecePos);
        this.choosenPiecePos = this.focusedPiecePos;
        this.focusedPiecePos = null;
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
        if (this.doingMove || !GameLogic.isPlayerMove())
            return;
        var pos = this.getPosFromDOMElem(event.target);
        if (this.isChoosableStartPos(pos))
            this.updateFocusedPiecePos(pos, false);
        console.log("enter to cell "+pos);
    },
    
    handleCellLeave: function(event) {
        if (this.doingMove || !GameLogic.isPlayerMove())
            return;
        var pos = this.getPosFromDOMElem(event.target);
        this.updateFocusedPiecePos(null, false);
        console.log("leave from cell "+pos);
    },

    __handleStateInt: function() {
        if (GameLogic.isPlayerMove()) {
            // if current player plays
            
        } else {
            // otherwise player doing move
            
        }
    },
    // handle state of the game
    handleState: function() {
        if (this.lock)
            return false;
        this.lock = true;
        this.__handleStateInt();
        this.lock = false;
        return true;
    }
}
