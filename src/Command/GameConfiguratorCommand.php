<?php

namespace App\Command;

use function Couchbase\defaultDecoder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GameConfiguratorCommand extends Command
{
    private $gameConfiguration = [
        'width' => 0,
        'height' => 0,
        'treasures' => [],
        'mountains' => [],
        'adventurers' => [],
    ];
    private $mapWidth = 0;

    private $mapHeight = 0;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    private $helperQuestion;

    private $finished = false;

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

        $this->gameConfiguration['width'] = $this->askMapDimension('width');
        $this->gameConfiguration['height'] = $this->askMapDimension('height');

        while (!$this->finished) {
            $this->askOption();
        }

        $this->output->writeln('/*****************************');
        $this->output->writeln('/************ RECAP **********');
        $this->output->writeln('/*****************************');

        $this->displayRecap();
    }

    private function askMapDimension($type, $displayError = false)
    {
        if ($displayError) {
            $this->output->writeln('Please provide an integer value.. That integer must be higher than 0');
        }

        $question = new Question('Provide a map ' . $type . ' > 0 (int) : ');
        $dimension = $this->integerMoreThanZero($this->helperQuestion->ask($this->input, $this->output, $question));

        if ($dimension > 0) {
            return $dimension;
        }
        return $this->askMapDimension($type, true);
    }

    private function integerMoreThanZero($varToTest)
    {
        if (ctype_digit($varToTest)) {
            return $varToTest;
        }
        return 0;
    }

    private function displayRecap()
    {
        $this->output->writeln('Map dimension (width*height) : ' . $this->mapWidth . '*' . $this->mapHeight);
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

    private function askOption()
    {
        $question = new Question('What would you like to add in this map ? (T: treasure, M: mountain, A: adventurer, F: Stop the script and generate configuration)');
        $option = $this->helperQuestion->ask($this->input, $this->output, $question);

        $this->output->writeln('chosen : ' . $option);

        switch ($option) {
            case 'T':
                $this->output->writeln('add new treasure');
                break;
            case 'M':
                $this->output->writeln('add new mountain');
                break;
            case 'A':
                $this->output->writeln('add new adventurer');
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
}