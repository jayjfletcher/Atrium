<x-atrium::layout title="Users">
    <x-slot:header>
        <x-atrium::page-header title="Users" description="Everyone in the workbench application." />
    </x-slot:header>

    <x-atrium::card>
        <x-atrium::table striped>
            <x-slot:head>
                <x-atrium::table.row>
                    <x-atrium::table.cell heading>Name</x-atrium::table.cell>
                    <x-atrium::table.cell heading>Email</x-atrium::table.cell>
                </x-atrium::table.row>
            </x-slot:head>

            @forelse($users as $user)
                <x-atrium::table.row>
                    <x-atrium::table.cell>{{ $user->name }}</x-atrium::table.cell>
                    <x-atrium::table.cell>{{ $user->email }}</x-atrium::table.cell>
                </x-atrium::table.row>
            @empty
                <x-atrium::table.row>
                    <x-atrium::table.cell colspan="2">
                        <x-atrium::empty-state title="No users yet" description="Seed the workbench database." />
                    </x-atrium::table.cell>
                </x-atrium::table.row>
            @endforelse
        </x-atrium::table>
    </x-atrium::card>
</x-atrium::layout>
