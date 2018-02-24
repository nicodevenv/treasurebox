<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\GameService;

class PlayGameCommand extends Command
{
    private $gameService;

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
            ->setDescription('Use configuration data to play game.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        try {
            $this->gameService->prepareGameConfiguration();
            $this->gameService->playGameConfiguration(true);
        } catch(\Exception $e) {
            $this->output->writeln($e->getMessage());
        }
    }
}
