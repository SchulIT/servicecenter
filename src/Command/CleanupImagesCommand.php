<?php

namespace App\Command;

use App\Entity\Announcement;
use App\Entity\Comment;
use App\Entity\Problem;
use App\Entity\WikiArticle;
use Doctrine\ORM\EntityManager;
use League\Flysystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupImagesCommand extends ContainerAwareCommand {
    const DRY_RUN = 'dry-run';

    public function configure() {
        $this
            ->setName('app:uploads:cleanup')
            ->setDescription('Deletes unused uploaded images')
            ->addOption(static::DRY_RUN, 'd', InputOption::VALUE_NONE, 'Command does not delete images in dry-run mode');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        /** @var Filesystem $filesystem */
        $filesystem = $this->getContainer()
            ->get('oneup_flysystem.uploads_filesystem');

        $files = $filesystem->listFiles();

        $output->writeln('Deleting unused uploaded images');

        if($input->getOption(static::DRY_RUN) === true) {
            $output->writeln('Option -dry-run detected - this execution does not delete any files');
        }

        $progress = new ProgressBar($output, count($files));

        foreach($files as $fileInfo) {
            $fileName = $fileInfo['basename'];
            $output->writeln('', OutputInterface::VERBOSITY_VERBOSE);
            $output->write(sprintf('Checking file "%s": ', $fileName), OutputInterface::VERBOSITY_VERBOSE);

            $num = $this->getTotalNumber($fileName);;
            $output->writeln(sprintf('found %d references', $num), OutputInterface::VERBOSITY_VERBOSE);

            if($num === 0) {
                if($input->getOption(static::DRY_RUN) !== true) {
                    $output->writeln(sprintf('Deleting file "%s"', $fileName), OutputInterface::VERBOSITY_VERBOSE);
                    $filesystem->delete($fileName);
                }
            }

            $progress->advance();
        }

        $progress->finish();

        $output->writeln('');
        $output->writeln('OK');
    }

    private function getTotalNumber($image) {
        $entities = [
            WikiArticle::class => ['content'],
            Problem::class => ['content'],
            Comment::class => ['content'],
            Announcement::class => ['details']
        ];

        $num = 0;

        foreach($entities as $entity => $columns) {
            $num += $this->getNumberOfEntities($entity, $columns, $image);
        }

        return $num;
    }

    private function getNumberOfEntities($entity, array $columns, $value) {
        /** @var EntityManager $em */
        $em = $this->getContainer()
            ->get('doctrine.orm.entity_manager');

        $qb = $em->createQueryBuilder();

        $qb
            ->select('COUNT(1)')
            ->from($entity, 'e');

        if(count($columns) > 1) {
            $orX = $qb->expr()->orX();

            foreach($columns as $column) {
                $orX->add(
                    sprintf('e.%s LIKE :query')
                );
            }
        } else {
            $column = $columns[0];
            $qb->where(
                sprintf('e.%s LIKE :query', $column)
            );
        }

        $qb->setParameter('query', '%' . $value . '%');

        return $qb->getQuery()->getSingleScalarResult();
    }
}