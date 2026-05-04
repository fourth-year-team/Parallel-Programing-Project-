<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasApiTokens
{
    /**
     * Create a new API token for the user.
     */
    public function createToken(string $name, array $abilities = ['*'])
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = bin2hex(random_bytes(40))),
            'abilities' => $abilities,
        ]);

        return new class($plainTextToken, $token) {
            public $plainTextToken;
            public $accessToken;

            public function __construct($plainTextToken, $accessToken)
            {
                $this->plainTextToken = $plainTextToken;
                $this->accessToken = $accessToken;
            }
        };
    }

    /**
     * Get the current access token being used by the request.
     */
    public function currentAccessToken()
    {
        return $this->accessToken ?? null;
    }

    /**
     * Get all API tokens.
     */
    public function tokens(): MorphMany
    {
        return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
    }
}
