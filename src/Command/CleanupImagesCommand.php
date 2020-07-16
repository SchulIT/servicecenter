<?php

namespace App\Command;

use App\Entity\Announcement;
use App\Entity\Comment;
use App\Entity\Problem;
use App\Entity\WikiArticle;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("*\/15 * * * *")
 */
class CleanupImagesCommand extends Command {

    private $filesystem;
    private $em;

    private const GitIgnore = '.gitignore';

    public function __construct(EntityManagerInterface $entityManager, FilesystemInterface $filesystem, ?string $name = null) {
        parent::__construct($name);

        $this->filesystem = $filesystem;
        $this->em = $entityManager;
    }

    const DRY_RUN = 'dry-run';

    public function configure() {
        $this
            ->setName('app:uploads:cleanup')
            ->setDescription('Deletes unused uploaded images')
            ->addOption(static::DRY_RUN, 'd', InputOption::VALUE_NONE, 'Command does not delete images in dry-run mode');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $files = $this->filesystem->listFiles();

        $output->writeln('Deleting unused uploaded images');

        if($input->getOption(static::DRY_RUN) === true) {
            $io->note('Option -dry-run detected - this execution does not delete any files');
        }

        $deleted = 0;
        $numFiles = 0;

        foreach($files as $fileInfo) {
            $fileName = $fileInfo['basename'];

            if($fileName === static::GitIgnore) {
                continue;
            }

            $numFiles++;
            $io->section(sprintf('Checking file "%s": ', $fileName));

            $num = $this->getTotalNumber($fileName);;
            $io->text(sprintf('Found %d references', $num));

            if($num === 0 && $input->getOption(static::DRY_RUN) !== true) {
                $this->filesystem->delete($fileName);
                $io->text('File deleted.');
                $deleted++;
            }
        }

        $io->success(sprintf('Deleted %d/%d files.', $deleted, $numFiles));

        return 0;
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
        $qb = $this->em->createQueryBuilder();

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