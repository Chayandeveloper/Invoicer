<?php

namespace App\Socialite;

use SocialiteProviders\Clerk\Provider as ClerkBaseProvider;
use SocialiteProviders\Manager\OAuth2\User;

class ClerkProvider extends ClerkBaseProvider
{
    /**
     * Get the base URL for Clerk.
     */
    protected function getBaseUrl()
    {
        return $this->getConfig('base_url') ?: config('services.clerk.base_url') ?: config('services.clerk.api_url');
    }

    /**
     * {@inheritdoc}
     * 
     * Overriding to fix "Undefined array key" errors when Clerk users 
     * have incomplete profiles.
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['user_id'] ?? $user['id'] ?? null,
            'nickname' => $user['username'] ?? ($user['given_name'] ?? null),
            'name'     => $user['name'] ?? trim(($user['given_name'] ?? '') . ' ' . ($user['family_name'] ?? '')) ?: null,
            'email'    => $user['email'] ?? ($user['email_address'] ?? null),
            'avatar'   => $user['picture'] ?? ($user['image_url'] ?? null),
        ]);
    }
}
