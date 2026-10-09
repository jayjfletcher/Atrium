<?php

declare(strict_types=1);

namespace RefactorCircus\Atrium\Tests\Fixtures\Billing;

use Illuminate\Foundation\Auth\User;
use RefactorCircus\Atrium\Http\Controllers\Concerns\AuthorizesScreens;

/**
 * A package's screen controller, refusing as its package's API does.
 */
final class RefundController
{
    use AuthorizesScreens;

    public function __invoke(): string
    {
        $this->authorizeScreen('refund', User::class);

        return 'refunded';
    }
}
