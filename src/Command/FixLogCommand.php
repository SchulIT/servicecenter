<?php

namespace App\Command;

use App\Entity\Priority;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:fix')]
class FixLogCommand extends Command {

    public function __construct(private readonly EntityManagerInterface $em, string $name = null) {
        parent::__construct($name);
    }


    public function execute(InputInterface $input, OutputInterface $output) {
        $entries = $this->em->getConnection()->fetchAllAssociative('SELECT * FROM ext_log_entries');

        foreach($entries as $entry) {
            if(mb_strpos($entry['data'], 'priority') === false) {
                continue;
            }

            $entry['data'] = preg_replace_callback('/O:\d+:"App\\\\Entity\\\\Priority":\d:{s:\d+:".*?";s:\d+:"(.*?)";.*?}(;?)/', function($matches) {
                return serialize(Priority::from($matches[1]));
            }, $entry['data']);

            $stmt = $this->em->getConnection()->prepare('UPDATE ext_log_entries SET `data` = ? WHERE id = ?');
            $stmt->executeQuery([
                $entry['data'],
                $entry['id']
            ]);
        }

        return 0;
    }
}