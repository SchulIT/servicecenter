<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand('app:metadata:download', description: 'Lädt die Metadaten XML vom Single Sign-On herunter, sodass das System integriert werden kann.')]
readonly class DownloadMetadataCommand {

    public function __construct(
        #[Autowire(env: 'IDP_METADATA_URL')] private string $metadataEndpoint,
        #[Autowire(param: 'idp_metadata_file')] private string $metadataFile,
        private HttpClientInterface $client
    ) {

    }

    public function __invoke(
        SymfonyStyle $style,
        #[Option] bool $force = false
    ): int {
        $style->section('Metadaten XML herunterladen');
        $style->info(sprintf('Endpunkt-URL: %s', $this->metadataEndpoint));
        $style->info(sprintf('Pfad zur XML-Datei: %s', $this->metadataFile));

        if($force === true) {
            $style->note('Lade XML-Datei auf jeden Fall herunter und speichere sie ab.');
        }

        if($force === false && file_exists($this->metadataFile)) {
            $style->success('XML-Datei existiert bereits, mache nichts. Mit der Option `--force` kann die Datei trotzdem heruntergeladen werden.');
            return Command::SUCCESS;
        }

        $style->write('Lade XML-Datei herunter');
        $response = $this->client->request('GET', $this->metadataEndpoint);

        if($response->getStatusCode() !== 200) {
            $style->error(sprintf('Fehler. HTTP Status-Code: %s', $this->metadataEndpoint));
            return Command::FAILURE;
        }

        $metadata = $response->getContent();
        file_put_contents($this->metadataFile, $metadata);

        $style->success('XML-Datei erfolgreich herunterladen und gespeichert.');

        return Command::SUCCESS;
    }
}