<?php

namespace App\Command;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

#[AsCommand('app:setup', 'Installiert die Anwendung')]
class SetupCommand extends Command {
    public function __construct(private readonly EntityManagerInterface $em, private readonly PdoSessionHandler $pdoSessionHandler, ?string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        try {
            $this->setupSessions($io);
        } catch(Exception $e) {
            $this->getApplication()->renderThrowable($e, $output);
        }

        return 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function setupSessions(SymfonyStyle $style): void {
        $style->section('Sessions-Tabelle');

        $sql = "SHOW TABLES LIKE 'sessions';";
        $row = $this->em->getConnection()->executeQuery($sql);

        if($row->fetchAssociative() === false) {
            $this->pdoSessionHandler->createTable();
            $style->success('Sessions-Tabelle erstellt');
        } else {
            $style->success('Sessions-Tabelle bereits vorhanden.');
        }
    }
}