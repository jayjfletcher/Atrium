<?php

declare(strict_types=1);

namespace JayI\Atrium\Pennant;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Grammars\SqlServerGrammar;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Drivers\Decorator;
use Laravel\Pennant\Feature;
use stdClass;

/**
 * Reads the feature flag values Pennant has stored.
 *
 * Pennant's API resolves one feature for one scope at a time and cannot list
 * what it has stored, so this reads the database driver's table directly.
 * Writes go through Pennant itself, in the feature flag actions.
 */
class FeatureFlagManager
{
    /**
     * The scope filter matching values stored for the null scope.
     */
    public const string GLOBAL = 'global';

    /**
     * The scope filter matching plain string scopes that are not models.
     */
    public const string OTHER = 'other';

    /**
     * The Pennant store being managed, null for Pennant's default.
     */
    public function storeName(): ?string
    {
        $store = config('atrium.pennant.store');

        return is_string($store) && $store !== '' ? $store : null;
    }

    public function store(): Decorator
    {
        return Feature::store($this->storeName());
    }

    /**
     * Whether the managed store keeps its values somewhere they can be listed.
     */
    public function supportsListing(): bool
    {
        return $this->storeConfig()['driver'] === 'database';
    }

    /**
     * Stored values, newest first, narrowed by the given filters.
     *
     * The scope filter is `global`, `other`, or a model type as Pennant
     * stores it: the class name, or the morph alias with `useMorphMap()`.
     *
     * @param  array{feature?: string|null, scope?: string|null, scope_id?: string|null}  $filters
     * @return LengthAwarePaginator<int, StoredFeatureValue>
     */
    public function paginate(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = $this->query()->orderByDesc('updated_at')->orderBy('name')->orderBy('scope');

        $feature = $filters['feature'] ?? null;

        if (is_string($feature) && $feature !== '') {
            $query->where('name', $feature);
        }

        $this->applyScopeFilter($query, $filters['scope'] ?? null, $filters['scope_id'] ?? null);

        return $query->paginate($perPage)
            ->through(fn (stdClass $row): StoredFeatureValue => StoredFeatureValue::fromRow($row));
    }

    /**
     * Feature names that are defined or have a stored value.
     *
     * @return array<int, string>
     */
    public function features(): array
    {
        $stored = $this->supportsListing()
            ? $this->query()->distinct()->orderBy('name')->pluck('name')->all()
            : [];

        $names = array_unique([...$this->store()->defined(), ...$stored]);

        sort($names);

        return array_values(array_filter($names, is_string(...)));
    }

    /**
     * The model types that have at least one stored value.
     *
     * @return array<int, string>
     */
    public function scopeTypes(): array
    {
        if (! $this->supportsListing()) {
            return [];
        }

        $query = $this->query();

        $type = $this->typeExpression($query);

        /** @var array<int, string> */
        return $query
            ->where('scope', 'like', '%|%')
            ->distinct()
            ->orderBy($type)
            ->pluck($type)
            ->filter(fn (mixed $type): bool => is_string($type) && $type !== '')
            ->values()
            ->all();
    }

    /**
     * Serialize a scope picked from the dashboard the way Pennant stores it.
     *
     * The global scope is Pennant's null scope. A model scope is its type and
     * key, matching what `Feature::serializeScope()` writes for a model, so
     * the value applies to that model without loading it.
     */
    public function serializeScope(string $type, ?string $id = null): string
    {
        if ($type === self::GLOBAL) {
            return Feature::serializeScope(null);
        }

        if ($type === self::OTHER) {
            return (string) $id;
        }

        return $type.'|'.$id;
    }

    protected function query(): Builder
    {
        $config = $this->storeConfig();

        return DB::connection($config['connection'])->table($config['table']);
    }

    protected function applyScopeFilter(Builder $query, ?string $scope, ?string $id): void
    {
        $id = $id === '' ? null : $id;

        match ($scope) {
            null, '' => null,
            self::GLOBAL => $query->where('scope', Feature::serializeScope(null)),
            self::OTHER => $query
                ->where('scope', '!=', Feature::serializeScope(null))
                ->where('scope', 'not like', '%|%')
                ->when($id !== null, fn (Builder $query): Builder => $query->where('scope', $id)),
            default => $id === null
                ? $query->where('scope', 'like', '%|%')->where($this->typeExpression($query), $scope)
                : $query->where('scope', $scope.'|'.$id),
        };
    }

    /**
     * The model type half of a serialized `Type|key` scope, in SQL.
     *
     * Each form yields the whole scope, never an error, when it holds no
     * separator, so it is safe to evaluate against every row.
     */
    protected function typeExpression(Builder $query): Expression
    {
        $grammar = $query->getGrammar();

        return DB::raw(match (true) {
            $grammar instanceof MySqlGrammar => "substring_index(scope, '|', 1)",
            $grammar instanceof PostgresGrammar => "split_part(scope, '|', 1)",
            $grammar instanceof SqlServerGrammar => "left(scope, charindex('|', scope + '|') - 1)",
            default => "substr(scope, 1, instr(scope || '|', '|') - 1)",
        });
    }

    /**
     * @return array{driver: string|null, connection: string|null, table: string}
     */
    protected function storeConfig(): array
    {
        $store = $this->storeName() ?? config('pennant.default');

        $config = config('pennant.stores.'.(is_string($store) ? $store : ''));

        $config = is_array($config) ? $config : [];

        return [
            'driver' => is_string($config['driver'] ?? null) ? $config['driver'] : null,
            'connection' => is_string($config['connection'] ?? null) ? $config['connection'] : null,
            'table' => is_string($config['table'] ?? null) ? $config['table'] : 'features',
        ];
    }
}
