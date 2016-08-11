<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Models;

/**
 * ApiKey
 */
class ApiKey
{
    private $apiKey;
    
    /**
     * Creates a new api key entry with a randomly generated key.
     * @return \self
     */
    public static function generate()
    {
        $length = 64;
        $randomBytes = random_bytes($length);
        $apiKey = base64_encode($randomBytes);

        return new self($apiKey);
    }
    
    /**
     * constructs a new api key entry linking key and user.
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Returns the api key.
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
