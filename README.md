# treasurebox

**Project installation**

To run this project, you should have "Composer" installed on your environment. If you don't have "Composer", please read the following documentation that explains how to install it:
https://getcomposer.org/download/

Then you should:
- Open a shell
- Clone the current repository on your machine
- Install all dependencies of this project automatically using the following command : composer install

And now you're done ! 

**Game configuration file format**

In order to play this game, a game configuration must be provided at "public/inputs/game_configuration.txt"
Configuration data are formatted as :
OPTION - ATTR1 - ATTR2

Available options are :
- C - Map dimensions
- M - Mountain
- T - Treasure
- A - Adventurer

Attributes could be (sorted as below) :
- Width (int) : Map width [C]
- Height (int) : Map height [C]
- Name (string) : Name of adventurer [A]
- X (int) : horizontal position [T, M, A]
- Y (int) : vertical position [T, M, A]
- Counter (int) : Number of treasures [T]
- Orientation (string) : Orientation of adventurer which could be : "N" North, "S" South, "E" East, "O" West [A]
- Actions (string): Sequence of actions like "AGGADA": "A" Move, "D" Turn right, "G" Turn left [A]

Please take a look at the example below : (explanation after the #)
- C - 4 - 3                           # Map width : 4, Map height : 3
- M - 3 - 1                           # Mountain X : 3, Mountain Y : 1
- M - 0 - 1                           # Mountain X : 0, Mountain Y : 1
- T - 2 - 2 - 3                       # Treasure X : 2, Treasure Y : 2, Counter : 3 (3 treasures on this cell)
- A - Nicolas - 1 - 1 - S - AADAGGA   # Adventurer Name : Nicolas, X : 1, Y : 1, Orientation : South (will go down on the next move [A]), (Move, Move, Turn Right, Move, Turn Left, Turn Left, Move)

**How to run Treasurebox**

Open a shell.
If you run the command "bin/console" on the root of this repository, you'll have a list of commands that you can use to execute some part of the program.
Please locate the commands that starts with "treasurebox".

You'll see 2 commands available which are :
- treasurebox:configurator : Run this command if you would like to create a new configuration file to run instead of writing it manually by respecting our format. It will generate one automatically
- treasurebox:play : Will play the game using the configuration file.

**Game configurator**

Run :
bin/console treasurebox:configurator

Follow the instructions and then finish using "F". It will generate the configuration file at "public/inputs/game_configuration.txt"

**Play Treasurebox**

Run :
bin/console treasurebox:play [OPTION: yes / no]

The option is used to display each step of the game when adventurer make a move [A], not when adventurer turns.
If "no" is chosen, it will only show the first and last step.

This command will use the file "public/inputs/game_configuration.txt" to init game and write the result at "public/outputs/game_output.txt"

**Tests**

This section is for developer only.

The current project is tested with phpunit. You'll see them in "tests/" folder. 

In order to run unit tests, docker must be installed on your machine.

Run "docker-compose up -d" in a shell at the root of this project and wait until it finishes building docker environment.
Use "docker exec -it treasurebox_www_1 bash" to access bash of the docker environment.
And then submit the command "vendor/bin/phpunit" to test the application

**Technology**

The current project is written using <a href='https://symfony.com/4'>Symfony4</a> by myself.
You can find the console's commands programs in "src/Command" folder, that's the starting point.

Enjoy and here you go. Voilà !

PS : I'm not just a developer back end. Fullstack is what I wanna be :)