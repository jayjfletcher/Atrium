<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Blade;
use RefactorCircus\Keystone\Audit\Contracts\AuditTrail;
use RefactorCircus\Keystone\Audit\Data\AuditEntry;
use RefactorCircus\Keystone\Audit\Data\AuditFilter;
use RefactorCircus\Keystone\Audit\Data\AuditPage;
use RefactorCircus\Keystone\Packages\Package;
use RefactorCircus\Keystone\Packages\PackageRegistry;

function auditEntry(string $source = 'showroom', array $changes = []): AuditEntry
{
    return new AuditEntry(
        id: 7,
        source: $source,
        action: 'product.updated',
        surface: 'atrium',
        createdAt: CarbonImmutable::now()->subMinutes(5),
        actorId: '1',
        actorLabel: 'Ada Lovelace',
        subjectType: 'product',
        subjectId: '42',
        subjectLabel: 'Widget',
        changes: $changes,
    );
}

it('renders nothing for the history while no audit log is installed', function (): void {
    expect(trim(Blade::render('<x-atrium::audit-trail source="showroom" />')))->toBe('');
});

it('renders a package history from the installed audit log', function (): void {
    $trail = new class implements AuditTrail
    {
        public ?AuditFilter $filter = null;

        public function available(): bool
        {
            return true;
        }

        public function entries(AuditFilter $filter): AuditPage
        {
            $this->filter = $filter;

            return new AuditPage([auditEntry()]);
        }
    };

    app()->instance(AuditTrail::class, $trail);

    $user = new User;
    $user->id = 3;

    $html = Blade::render('<x-atrium::audit-trail source="showroom" :subject="$subject" :scope="$subject" :limit="5" action="product." />', ['subject' => $user]);

    expect($html)->toContain('data-testid="audit-trail"')
        ->toContain('product.updated')
        ->toContain('Ada Lovelace')
        ->toContain('atrium')
        // One record's history leaves out the record column.
        ->not->toContain('Widget')
        ->and($trail->filter?->source)->toBe('showroom')
        ->and($trail->filter?->subjectId)->toBe('3')
        ->and($trail->filter?->limit)->toBe(5)
        ->and($trail->filter?->action)->toBe('product.')
        ->and($trail->filter?->scopeId)->toBe('3');
});

it('marks entries the application recorded itself', function (): void {
    $html = Blade::render('<x-atrium::audit.entries :entries="$entries" />', ['entries' => [auditEntry('app')]]);

    expect($html)->toContain('Widget')->toMatch('/>\s*App\s*</');
});

it('shows an empty state without entries', function (): void {
    expect(Blade::render('<x-atrium::audit.entries :entries="[]" />'))->toContain('Nothing has been recorded yet.');
});

it('lists field changes with their old and new values', function (): void {
    $html = Blade::render('<x-atrium::audit.changes :changes="$changes" />', ['changes' => ['name' => ['Old', 'New'], 'tags' => [null, ['a']]]]);

    expect($html)->toContain('name')
        ->toContain('&#039;Old&#039;')
        ->toContain('&#039;New&#039;')
        ->toContain('NULL')
        ->toContain('[&quot;a&quot;]');
});

it('says when no fields changed', function (): void {
    expect(Blade::render('<x-atrium::audit.changes :changes="[]" />'))->toContain('No fields changed.');
});

it('hides a package history from those its history endpoint would refuse', function (): void {
    app()->instance(AuditTrail::class, new class implements AuditTrail
    {
        public function available(): bool
        {
            return true;
        }

        public function entries(AuditFilter $filter): AuditPage
        {
            return new AuditPage([auditEntry()]);
        }
    });

    app(PackageRegistry::class)->register(
        Package::make('showroom', 'Showroom')->authorizeHistory(fn (): bool => false),
    );

    expect(trim(Blade::render('<x-atrium::audit-trail source="showroom" />')))->toBe('');
});
