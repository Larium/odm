<?php

declare(strict_types=1);

namespace Larium\ODM\Support;

/**
 * Skip tests that need a real Firestore client when no service account JSON is available.
 */
trait RequiresGoogleApplicationCredentialsTrait
{
    protected function skipIfGoogleApplicationCredentialsMissing(): void
    {
        if (!$this->googleApplicationCredentialsAreReadable()) {
            $this->markTestSkipped(
                'Google application credentials not found or unreadable. '
                . 'Set GOOGLE_APPLICATION_CREDENTIALS to a service account JSON file, '
                . 'or add resources/Credentials.json at the project root.'
            );
        }
    }

    private function googleApplicationCredentialsAreReadable(): bool
    {
        $path = getenv('GOOGLE_APPLICATION_CREDENTIALS');
        if ($path === false || $path === '') {
            $path = dirname(__DIR__, 2) . '/resources/Credentials.json';
        }

        return is_readable($path);
    }
}
