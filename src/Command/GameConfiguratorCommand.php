<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use App\Service\GameService;

class GameConfiguratorCommand extends Command
{
    /** @var GameService */
    private $gameService;

    private $gameConfiguration = [];

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    private $helperQuestion;

    private $finished = false;

    /**
     * GameConfiguratorCommand constructor.
     *
     * @param GameService $gameService
     */
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('treasurebox:configurator')
            ->setDescription('Provide data to create automatically a game configuration');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->helperQuestion = $this->getHelper('question');
        $this->input          = $input;
        $this->output         = $output;

        $this->askOption('C');

        while (!$this->finished) {
            $this->askOption();
        }

        $this->gameService->generateConfigurationFromArray($this->gameConfiguration);
        $this->output->writeln(sprintf('Game configuration has been generated in : %s', $this->gameService->getConfigurationPath()));

        return;
    }

    /**
     * @param string $message
     * @param array  $choices
     * @param string $choiceError
     *
     * @return string
     */
    private function requireAnswer($message, $choices = [], $choiceError = ""): string
    {
        if (count($choices) > 0) {
            $question = new ChoiceQuestion(
                $message,
                $choices
            );

            $question->setErrorMessage($choiceError);
        } else {
            $question = new Question($message . ' : ');
        }

        return $this->helperQuestion->ask($this->input, $this->output, $question);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function requireOptionAnswers($type): string
    {
        $separator           = ' - ';
        $adventurerName      = '';
        $adventurerDirection = '';
        $adventurerActions   = '';
        $treasureCounter     = 0;

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
            $adventurerDirection = $this->requireAnswer('Direction', ['N', 'S', 'E', 'O'], 'Please select between N, S, E, O');
            $adventurerActions   = $this->requireAnswer('Actions');
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

    /**
     * @param null|string $forcedOption
     */
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
}
