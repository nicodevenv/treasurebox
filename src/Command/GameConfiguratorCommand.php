<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use App\Service\GameService;

class GameConfiguratorCommand extends Command
{
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;

        parent::__construct();
    }

    private $gameConfiguration = [
        'map_dimension' => 0,
        'treasures' => [],
        'mountains' => [],
        'adventurers' => [],
    ];

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    private $helperQuestion;

    private $finished = false;

    private function askQuestion($message)
    {
        $question = new Question($message . ' : ');
        return $this->helperQuestion->ask($this->input, $this->output, $question);
    }

    private function askXY($separator, $isPosition = true)
    {
        // defaulting to width / height
        $horizontal = 'Provide map width';
        $vertical = 'Provide map height';

        if ($isPosition) {
            $vertical = 'Provide y position (vertical)';
            $horizontal = 'Provide x position (horizontal)';
        }

        $positionX = $this->askQuestion($horizontal);
        $positionY = $this->askQuestion($vertical);

        return $positionX . $separator . $positionY;
    }

    private function askOption()
    {
        $input = $this->askQuestion('What would you like to add in this map ? (T: treasure, M: mountain, A: adventurer, F: Stop the script and generate configuration)');
        $this->output->writeln('chosen : ' . $input);


        switch ($input) {
            case 'T':
                $this->gameConfiguration['treasures'][] = $this->askXY(',');
                break;
            case 'M':
                $this->gameConfiguration['mountains'][] = $this->askXY(',');
                break;
            case 'A':
                $this->gameConfiguration['adventurers'][] = [
                    'name' => $this->askQuestion('Name'),
                    'direction' => $this->askQuestion('Direction (N : North / S : South / E : East / W: West'),
                    'position' => $this->askXY(','),
                ];
                break;
            case 'F':
                if ($this->canBeFinished()) {
                    $this->finished = true;
                }
                break;
            default:
                $this->output->writeln('Option can only be : T / M / A / F');
                break;
        }
    }

    private function canBeFinished()
    {
        $nbTreasures = count($this->gameConfiguration['treasures']);
        $nbAdventurers = count($this->gameConfiguration['adventurers']);
        if ($nbAdventurers > 0 && $nbTreasures > 0) {
            return true;
        }

        $this->output->writeln('You must have at least 1 treasure and 1 adventurer to generate configuration');
        $this->output->writeln('Number of adventurers : ' . $nbAdventurers);
        $this->output->writeln('Number of treasures : ' . $nbTreasures);

        return false;
    }

    private function displayRecap()
    {
        $this->output->writeln('Map dimension (width*height) : ' . $this->gameConfiguration['map_dimension']);
        foreach ($this->gameConfiguration['adventurers'] as $index => $adventurerData) {
            $this->output->writeln('Adventurer #' . ($index+1) . ' -----------');
            $this->output->writeln('Name : ' . $adventurerData['name']);
            $this->output->writeln('Direction : ' . $adventurerData['direction']);
        }
        $this->output->writeln('Mountains -----------');
        array_walk($this->gameConfiguration['mountains'], function ($mountainPosition) {
            $this->output->writeln($mountainPosition);
        });
        $this->output->writeln('Treasures -----------');
        array_walk($this->gameConfiguration['treasures'], function ($treasurePosition) {
            $this->output->writeln($treasurePosition);
        });
    }

    protected function configure()
    {
        $this
            ->setName('treasurebox:configurator')
            ->setDescription('Provide data to create automatically a game configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->helperQuestion = $this->getHelper('question');
        $this->input = $input;
        $this->output = $output;

        /*$this->gameConfiguration['map_dimension'] = $this->askXY('*', false);

        while (!$this->finished) {
            $this->askOption();
        }

        $this->output->writeln('/*****************************');
        $this->output->writeln('/********** Summary **********');
        $this->output->writeln('/*****************************');

        $this->displayRecap();*/

        $this->gameConfiguration['map_dimension'] = '10*10';
        $this->gameConfiguration['mountains'][] = '1,1';
        $this->gameConfiguration['treasures'][] = '2,2';
        $this->gameConfiguration['adventurers'][] = [
            'name' => 'Nicolas',
            'position' => '4,4',
            'direction' => 'N',
        ];

        $this->gameService->generateConfigurationFromArray($this->gameConfiguration);
    }
}