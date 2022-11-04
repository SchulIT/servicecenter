<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetupCommand extends Command {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager, ?string $name = null) {
        parent::__construct($name);

        $this->em = $entityManager;
    }

    public function configure() {
        $this
            ->setName('app:setup')
            ->setDescription('Runs the initial setup');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $io->section('Create session table...');
        try {
            $this->setupSessions();
        } catch(\Exception $e) {
            $this->getApplication()->renderThrowable($e, $output);
        }
        $io->success('Session table created.');

        $io->section('Create key-value store...');
        try {
            $this->setupKeyValueStore();
        } catch (\Exception $e) {
            $this->getApplication()->renderThrowable($e, $output);
            return 1;
        }

        $io->success('Key-value store created.');

        $io->success('Setup completed');
        return 0;
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager() {
        return $this->em;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function setupSessions() {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `sessions` (
    `sess_id` VARCHAR(128) NOT NULL PRIMARY KEY,
    `sess_data` BLOB NOT NULL,
    `sess_time` INTEGER UNSIGNED NOT NULL,
    `sess_lifetime` MEDIUMINT NOT NULL
);
SQL;

        $this->getEntityManager()->getConnection()->exec($sql);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function setupKeyValueStore() {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `idp_exchange` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `meta_key` VARCHAR(255) NULL,
      `meta_value` BLOB NULL,
      PRIMARY KEY (`id`),
      UNIQUE INDEX `meta_key_UNIQUE` (`meta_key` ASC)
);
SQL;
        $this->getEntityManager()->getConnection()->exec($sql);
    }
}