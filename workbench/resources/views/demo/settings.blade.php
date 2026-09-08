<x-atrium::card title="Demo settings">
    <p>A plugin contributes this panel. Atrium renders it inside the settings page.</p>

    <x-atrium::form.input name="demo_label" label="Dashboard label" value="Demo" hint="Not persisted in the workbench." />
    <x-atrium::form.checkbox name="demo_enabled" label="Enabled" :checked="true" />
</x-atrium::card>
