<?php

namespace App\Service;

use App\Repository\ApplicationRepositoryInterface;

/**
 * Helper class for api key generation.
 */
class ApplicationKeyGenerator {

    public function __construct(private ApplicationRepositoryInterface $repository)
    {
    }

    /**
     * Generates a not used api key.
     *
     * @return string
     */
    public function generateApiKey() {
        do {
            $apiKey = bin2hex(openssl_random_pseudo_bytes(32));
            $application = $this->repository
                ->findOneByApiKey($apiKey);
        } while($application !== null);

        return $apiKey;
    }
}