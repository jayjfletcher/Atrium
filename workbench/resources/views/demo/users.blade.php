<x-atrium::layout title="Team">
    <x-slot:header>
        <x-atrium::page-header title="Team" description="Everyone with access to the shop, read from the users table.">
            <x-slot:actions>
                <x-atrium::button variant="outline">
                    {!! \JayI\Atrium\Support\Icons::svg('user-plus') !!}
                    Invite someone
                </x-atrium::button>
            </x-slot:actions>
        </x-atrium::page-header>
    </x-slot:header>

    <x-atrium::card>
        <x-atrium::table striped>
            <x-slot:head>
                <x-atrium::table.row>
                    <x-atrium::table.cell heading>Name</x-atrium::table.cell>
                    <x-atrium::table.cell heading>Status</x-atrium::table.cell>
                    <x-atrium::table.cell heading>Joined</x-atrium::table.cell>
                    <x-atrium::table.cell heading><span class="sr-only">Actions</span></x-atrium::table.cell>
                </x-atrium::table.row>
            </x-slot:head>

            @forelse($users as $user)
                <x-atrium::table.row data-testid="user-{{ $user->id }}">
                    <x-atrium::table.cell>
                        <div class="flex items-center gap-3">
                            <x-atrium::avatar size="sm" :alt="$user->name"
                                              :initials="collect(explode(' ', $user->name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('')" />
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $user->name }}</span>
                                <span class="text-xs text-on-surface dark:text-on-surface-dark">{{ $user->email }}</span>
                            </div>
                        </div>
                    </x-atrium::table.cell>
                    <x-atrium::table.cell>
                        <span class="inline-flex items-center gap-2">
                            @if ($user->email_verified_at)
                                <x-atrium::status-dot variant="success" label="Active" /> Active
                            @else
                                <x-atrium::status-dot variant="info" label="Invitation pending" /> Invitation pending
                            @endif
                        </span>
                    </x-atrium::table.cell>
                    <x-atrium::table.cell>{{ $user->created_at?->toFormattedDateString() }}</x-atrium::table.cell>
                    <x-atrium::table.cell numeric>
                        <span class="inline-flex items-center gap-1">
                            <x-atrium::icon-button icon="envelope" label="Email {{ $user->name }}" size="sm" href="mailto:{{ $user->email }}" />
                            <x-atrium::icon-button icon="pencil-square" label="Edit {{ $user->name }}" size="sm" />
                        </span>
                    </x-atrium::table.cell>
                </x-atrium::table.row>
            @empty
                <x-atrium::table.row>
                    <x-atrium::table.cell colspan="4">
                        <x-atrium::empty-state title="No users yet" description="Run composer build to seed the workbench database." />
                    </x-atrium::table.cell>
                </x-atrium::table.row>
            @endforelse
        </x-atrium::table>
    </x-atrium::card>
</x-atrium::layout>
