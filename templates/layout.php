<!DOCTYPE html>
<html ng-app>
<head>
	<title>Battleships - Web-based version running AngularJS</title>
	<script language="javascript" type="text/javascript" src="js/lib/angular.min.js"></script>
	<script language="javascript" type="text/javascript" src="js/GameHelper.js"></script>
	<script language="javascript" type="text/javascript" src="js/app.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div ng-controller="GameController" id="container">
		<table cellpadding="5" cellspacing="5" border="0" id="gameTable">
			<thead>
				<td>&nbsp;</td>
				<td>1</td>
				<td>2</td>
				<td>3</td>
				<td>4</td>
				<td>5</td>
				<td>6</td>
				<td>7</td>
				<td>8</td>
				<td>9</td>
				<td>0</td>
			</thead>
			<tbody>
				<tr ng-repeat="row in data.map">
					<td class="index">{{fromCharCode($index)}}</td>
					<td ng-repeat="state in row track by $index" class="gameCell state_{{state}}" ng-click="shootAt($parent.$index,$index)">
						{{renderState(state)}}
					</td>
				</tr>
			</tbody>
		</table>
		<div class="shots"><strong>Shots at the enemy:</strong> {{data.shots}}</div>
		<div class="message state_{{data.messageState}}">
			{{data.message}}
		</div>

		<div class="buttons">
			<button class="btn toggleHidden" ng-click="toggleHidden()">{{hiddenState ? 'Hide Targets' : 'Show Targets'}}</button>
			<button class="btn newGame" ng-click="newGame()">Start New Game</button>
		</div>
	</div>
</body>
</html>
