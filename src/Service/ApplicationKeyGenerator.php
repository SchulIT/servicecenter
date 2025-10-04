<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Application;
use App\Repository\ApplicationRepositoryInterface;

/**
 * Helper class for api key generation.
 */
readonly class ApplicationKeyGenerator {

    public function __construct(private ApplicationRepositoryInterface $repository)
    {
    }

    /**
     * Generates a not used api key.
     */
    public function generateApiKey(): string {
        do {
            $apiKey = bin2hex(openssl_random_pseudo_bytes(32));
            $application = $this->repository
                ->findOneByApiKey($apiKey);
        } while($application instanceof Application);

        return $apiKey;
    }
}
