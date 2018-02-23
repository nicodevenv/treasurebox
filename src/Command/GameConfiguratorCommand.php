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

    private $gameConfiguration = [];

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    private $helperQuestion;

    private $finished = false;

    private function requireAnswer($message)
    {
        $question = new Question($message . ' : ');
        return $this->helperQuestion->ask($this->input, $this->output, $question);
    }

    private function requireOptionAnswers($type)
    {
        $separator = ' - ';

        //ask for data
        if ($type === 'A') {
            $adventurerName = $this->requireAnswer('Name');
        }

        $xMessage = 'X position';
        $yMessage = 'Y position';
        if ($type === 'C') {
            $xMessage = 'Map width';
            $yMessage = 'Map Height';
        }

        $positionOrDimension = $this->requireAnswer($xMessage) . ' - ' . $this->requireAnswer($yMessage);

        if ($type === 'T') {
            $treasureCounter = $this->requireAnswer('Number of treasures');
        }

        if ($type === 'A') {
            $adventurerDirection = $this->requireAnswer('Direction');
            $adventurerActions = $this->requireAnswer('Actions');
        }

        //returning data
        if ($type === 'A') {
            return implode($separator, [
                $type,
                $adventurerName,
                $positionOrDimension,
                $adventurerDirection,
                $adventurerActions,
            ]);
        }

        if ($type === 'T') {
            return implode($separator, [$type, $positionOrDimension, $treasureCounter]);
        }

        return implode($separator, [$type, $positionOrDimension]);
    }

    private function askOption($forcedOption = null)
    {
        $chosenOption = $forcedOption;
        if ($chosenOption === null) {
            $this->output->writeln('Would you like to add an option ?');
            $chosenOption = $this->requireAnswer('M: mountain / T: treasure / A: adventurer / F: I\'ve finished');
        }

        if ($chosenOption === 'F') {
            $this->finished = true;
            return;
        }

        $noChoiceFound = false;
        //allowed options
        if (in_array($chosenOption, ['C', 'M', 'T', 'A'])) {
            //already have map dimensions
            if ($chosenOption === 'C' && count($this->gameConfiguration) !== 0) {
                $noChoiceFound = true;
            }

            //require options data
            if (!$noChoiceFound) {
                $this->gameConfiguration[] = $this->requireOptionAnswers($chosenOption);
            }
        } else {
            $noChoiceFound = true;
        }

        if ($noChoiceFound) {
            $this->output->writeln('Sorry we didn\'t find any option matching your choice');
        }
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

        $this->askOption('C');

        while (!$this->finished) {
            $this->askOption();
        }

        $this->gameService->generateConfigurationFromArray($this->gameConfiguration);
    }
}
