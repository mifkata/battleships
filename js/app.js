function GameController($scope, $http) {
    var gameId = GameHelper.getGameIdFromQuery(),
        scriptLocation = document.location.pathname,
        ASCII_A = 65;

    $scope.hiddenState = false;
    $scope.data = {
        coords: [],
        shots: 0,
        message: null,
        messageState: null
    }

    $scope.formatResponse = function(response) {
        $scope.data = {
            map: response.coords,
            shots: response.shots,
            message: response.message,
            messageState: response.messageState,
            gameState: response.gameState
        };
    }

    $scope.fromCharCode = function(code) {
        return String.fromCharCode(ASCII_A + code);
    }

    $scope.isGameOver = function() {
        return 1 == $scope.data.gameState;
    }

    $scope.shootAt = function(y, x) {
        if ($scope.isGameOver())
            return false;

        $scope.hiddenState = false;
        $http.get(scriptLocation, {
            params: {
                gameId: gameId,
                action: 'shoot',
                y: y,
                x: x
            }
        }).success($scope.formatResponse);
    }

    $scope.renderState = function(state) {
        state = parseInt(state);
        switch (state) {
            case 0:
                return '\u00A0';
            case 2:
                return 'O';
            case 3:
            case 1:
                return 'X';
        }
    }

    $scope.toggleHidden = function() {
        if ($scope.isGameOver())
            return false;

        $scope.hiddenState = !$scope.hiddenState;
        $scope.getMapData(!$scope.hiddenState);
    }

    $scope.getMapData = function(visible) {
        action = false === visible ? 'getMapHidden' : 'getMapVisible'

        $http.get(scriptLocation, {
            params: {
                gameId: gameId,
                action: action,
            }
        }).success($scope.formatResponse);
    };

    $scope.newGame = function() {
        return window.location = scriptLocation;
    }

    // get data from server
    $scope.getMapData();
}