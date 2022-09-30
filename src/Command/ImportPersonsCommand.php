<?php

namespace App\Command;

use App\Entity\Person;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use League\Csv\Reader;
use Symfony\Component\Console\Helper\ProgressBar;

#[AsCommand(
    name: 'import:persons',
    description: 'Import people from a CSV file',
)]
class ImportPersonsCommand extends Command
{
    public function __construct(
        private ManagerRegistry $doctrine
    )
    {
        parent::__construct();   
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'CSV file (one column, head="name")')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $file = $input->getArgument('file');
        $io->note(sprintf('Importing people from %s', $file));

        $reader = Reader::createFromPath($file, 'r');
        $reader->setHeaderOffset(0);

        $progress  = new ProgressBar($output, count($reader));
        $progress->start();

        $em = $this->doctrine->getManagerForClass(Person::class);

        foreach ($reader as $line) {

            $person = new Person;
            $person->setName($line['name']);

            $em->persist($person);
            $em->flush();

            $progress->advance();
        }


        $io->success('Import done.');

        return Command::SUCCESS;
    }
}
