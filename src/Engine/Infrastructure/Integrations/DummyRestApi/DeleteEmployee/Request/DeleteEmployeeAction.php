<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\DeleteEmployee\Request;

use IntegrationEngine\Core\Contract\Action\AbstractAction;

final class DeleteEmployeeAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'DeleteEmployee';
    }

    // No response body on DELETE — engine returns EmptyResponse automatically.
    public static function hasResponse(): bool
    {
        return false;
    }

    public static function mapper(): ?string
    {
        return null;
    }
}
