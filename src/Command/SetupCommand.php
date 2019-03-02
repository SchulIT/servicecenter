<?php

namespace App\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetupCommand extends Command {

    private $em;

    public function __construct(EntityManagerInterface $entityManager, ?string $name = null) {
        parent::__construct($name);

        $this->em = $entityManager;
    }

    public function configure() {
        $this
            ->setName('app:setup')
            ->setDescription('Runs the initial setup');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $this->setupSessions();
        $io->success('Create sessions table');

        $io->success('Setup completed');
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager() {
        return $this->em;
    }

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

    }
}