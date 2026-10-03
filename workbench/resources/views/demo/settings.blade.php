<div class="flex flex-col gap-4">
    <x-atrium::card title="Store details" subtitle="A plugin contributes this panel; Atrium renders it inside the settings page.">
        <div class="flex flex-col gap-4">
            <x-atrium::form.input name="store_name" label="Store name" value="Acme Shop" hint="Not saved in the workbench." />
            <x-atrium::form.input name="support_email" label="Support email" value="support@acme.test" type="email" />
            <x-atrium::form.select name="currency" label="Currency" selected="usd"
                                   :options="['usd' => 'US dollar (USD)', 'eur' => 'Euro (EUR)', 'gbp' => 'Pound sterling (GBP)']" />
            <x-atrium::form.checkbox name="order_emails" label="Email customers when an order ships" :checked="true" />
        </div>
    </x-atrium::card>

    <x-atrium::card title="Feature flags" subtitle="Answered by the workbench's feature resolver. Gated navigation follows these.">
        <ul class="flex flex-col gap-3">
            @foreach ($features as $feature => $enabled)
                <li class="flex items-center justify-between gap-3 text-sm" data-testid="feature-{{ $feature }}">
                    <span class="flex items-center gap-2">
                        <x-atrium::status-dot :variant="$enabled ? 'success' : 'neutral'" :label="$enabled ? 'On' : 'Off'" />
                        <code>{{ $feature }}</code>
                    </span>
                    <x-atrium::badge :variant="$enabled ? 'success' : 'neutral'">{{ $enabled ? 'On' : 'Off' }}</x-atrium::badge>
                </li>
            @endforeach
        </ul>
    </x-atrium::card>
</div>
