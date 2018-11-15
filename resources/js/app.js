
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

GameBoard = {
    cellSize: 50,
    boardDim: GameLogic.BOARDDIM,
    boardElem: null,
    init: function() {
        this.boardElem = $("#checkers_board_main");
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
                        .css({ left: (this.cellSize*xi)+'px',
                               top: ((this.boardDim-1-yi)*this.cellSize)+'px' });
            if (pos in this.choosable)
                pieceElem.addClass('checkers_board_choosable');
            this.boardElem.append(pieceElem);
            this.pieceElems[pos] = pieceElem;
        }
    }
};
