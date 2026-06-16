<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Request;

use App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Response\GetCharacterMapper;
use IntegrationEngine\Core\Contract\Action\AbstractAction;

final class GetCharacterAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'GetCharacter';
    }

    public static function hasResponse(): bool
    {
        return true;
    }

    public static function mapper(): string
    {
        return GetCharacterMapper::class;
    }
}
