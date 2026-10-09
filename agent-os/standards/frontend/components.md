# Frontend: Atrium owns the suite's components and styles

- Every Blade component and every style of the first-party suite (Cortex, Impex, Keen, PennantPlus, Polycart, Roster, Showroom) lives in Atrium. Those packages ship no `resources/css`, no compiled stylesheet, no `Atrium::css()` / `Atrium::stylesheet()` call and no component namespace.
- Package views are domain screens built from `x-atrium::*` components plus the layout utilities Atrium safelists in `resources/css/atrium.css` (`@source inline(...)`): grid columns and spans, flex alignment, gap, spacing, widths and heights, sizes, text sizes, font and wrapping helpers, overflow, positioning, borders and the surface/text tokens.
- A screen that needs something the components and safelist do not cover gets a new generic component in Atrium (or a safelist entry), never package CSS, a `<style>` block or a `style` attribute.
- Generic pieces that already exist: `description-list`, bare form controls (`bare`), input `prefix`/`suffix`, `form.combobox`, `search-input`, `chip`, `banner` (`standalone` for host-app pages), `form.actions`, `flash`, `guest`, `audit-trail`, `audit.entries`, `audit.changes`.
- Each first-party package has a test asserting `AtriumStyles::missingClasses()` and `AtriumStyles::inlineStyles()` are empty for its `resources/views`.
- Third-party packages may still register styles with `Atrium::css()` / `Atrium::stylesheet()` and their own component namespace.
- After changing any Atrium Blade file or the safelist, run `npm run build:css` and commit `public/atrium.css`.
