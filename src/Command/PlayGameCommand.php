<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\GameService;

class PlayGameCommand extends Command
{
    /** @var GameService */
    private $gameService;

    /**
     * PlayGameCommand constructor.
     *
     * @param GameService $gameService
     */
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;

        parent::__construct();
    }

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    protected function configure()
    {
        $this
            ->setName('treasurebox:play')
            ->addArgument('stepDisplay', InputArgument::OPTIONAL, 'Display result on each step')
            ->setDescription('Use configuration data to play game.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output): mixed
    {
        $this->input  = $input;
        $this->output = $output;

        $stepDisplay = false;
        $displayType = $input->getArgument('stepDisplay');
        if ($displayType && filter_var($displayType, FILTER_VALIDATE_BOOLEAN)) {
            $stepDisplay = true;
        }

        try {
            $this->gameService->prepareGameConfiguration();
            $this->output->writeln($this->gameService->getGameSteps($stepDisplay));
            $this->gameService->writeResults();
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
        }

        return;
    }
}
