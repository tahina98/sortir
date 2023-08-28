<?php

namespace App\Command;

use App\Repository\SortieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ArchiveSortie',
    description: 'Add a short description for your command',
)]
class ArchiveSortieCommand extends Command
{
    public function __construct(
        private SortieRepository $sortieRepository
    ){
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);
        $this->sortieRepository->archivageSorties();
        $io->success('Archivage réussi');

        return Command::SUCCESS;
    }
}
