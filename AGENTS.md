# Atrium

This repository is a Laravel package. Keep the package focused, idiomatic, and easy for Laravel developers to install, test, and maintain.

## Package Conventions

- Use Laravel-native package APIs and the existing service provider shape before adding abstractions.
- Keep package names, namespaces, Composer metadata, publish tags, documentation, and examples aligned with `jayi/atrium`.
- Add only the files and dependencies needed for the package behavior being implemented.
- Prefer explicit Laravel package code over helper abstractions unless the extension point is real.
- Keep tests focused on observable package behavior through public APIs, service provider wiring, commands, routes, published resources, and documentation promises.

## Architecture Conventions

These are recorded in full under `agent-os/standards/`; the short form:

- **Actions own mutating logic.** Extend `Atrium\Atrium\Actions\Action`, implement a `protected handle()` with a concrete return type, and let callers use `execute()`. Wrap mutations in `DB::transaction()` and dispatch events through `DB::afterCommit()` inside the closure.
- **Requests own validation, authorization, and the call into an action.** Extend `Atrium\Atrium\Http\Requests\Request` and implement `persist()`. Controllers only `return $request->persist();` and never contain database calls.
- **Events extend `Atrium\Atrium\Events\Event`.** Business events are named `{Entity}{Verb}ActionEvent`; model lifecycle hooks map to `{Model}{Hook}Event` via `$dispatchesEvents`.
- Do not add a feature-flag dependency such as Pennant to the package; gating is the host application's concern.

## PHP Conventions

- The package targets PHP 8.4. Use its syntax where it earns its place.
- Value objects (`NavItem`, `WidgetDefinition`, `SettingsPanel`, `SearchSource`, `SearchResult`) expose state as `public private(set)` properties rather than getter methods, so they read as `$item->label` and stay immutable from the outside. Configure them through the fluent setters, which share the property's name.
- Closures and other internals stay `private`; only meaningful state is publicly readable.

## Frontend Conventions

- Components use Tailwind CSS 4 utility classes in the markup, adapted from Penguin UI (MIT). There are no semantic `atrium-*` classes.
- Styling tokens are design tokens in `resources/css/atrium.css` (`--color-primary`, `--color-on-surface`, and so on), overridable at runtime through `config('atrium.theme')`.
- Run `npm run build:css` after changing any Blade file, and commit the compiled `public/atrium.css`. The build regenerates the `@source` list, so new views are picked up automatically.
- Test hooks use `data-testid` attributes, which the browser suite selects with `@name`.

## Quick Commands

- Full validation: `composer test`
- Formatting check: `composer lint:check`
- Static analysis: `composer analyse`
- Pest tests: `composer test:unit`
- Browser tests: `composer test:browser` (needs `npm install`)
- Stylesheet build: `npm run build:css`
- Workbench build: `composer build`
- Workbench server: `composer serve`

## Local Skills

- `package-scaffold`: use when adding package capabilities or wiring them through the service provider, including commands, migrations, routes, config, views, translations, assets, middleware, publish tags, workbench files, and console-only behavior.
- `package-testing`: use when adding or changing package tests with Pest 4/5 and Orchestra Testbench.
- `package-release`: use when preparing changelog, release notes, tags, or GitHub release workflow changes.
- `package-compatibility`: use when reviewing code, dependencies, or CI against the PHP and Laravel support matrix.
- `package-generate-skill`: use when updating the bundled Boost skill from the package implementation, README, and examples.
