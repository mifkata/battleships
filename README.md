Battleships game
==============================

# Introduction
A simple implementation of the battleships game with 2 interfaces:

 * Console - executed with PHP-Cli, uses passthru('clear|cls') 
 to clear the screen
 * Web based

_IMPORTANT_ The application has not been tested only under Linux
environment, except for the browser part (tested on Chrome/Windows 7)

_IMPORTANT_ There're many ways to abuse the code in terms of a rather
complete lack of validations for the web part, however this shouldn't
affect the game experience

# Starting the console application

```
php console.php
```

# Starting the web appliction

For the web application to work, you need to have writable data
folder located in the root directory of the application. Its being
used to store game data information in serialized format on disk,
as to avoid database usage

To start the game simply load index.php from Apache