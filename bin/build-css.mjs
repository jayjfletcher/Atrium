#!/usr/bin/env node
/**
 * Compiles Atrium's stylesheet.
 *
 * Tailwind is pointed at each Blade file explicitly rather than at the views
 * directory, because directory globs do not reliably pick up `.blade.php`.
 * The source list is regenerated here so adding a view never means editing
 * the stylesheet by hand.
 */
import { execFileSync } from 'node:child_process'
import { readdirSync, readFileSync, statSync, writeFileSync } from 'node:fs'
import { dirname, join, relative } from 'node:path'
import { fileURLToPath } from 'node:url'

const root = join(dirname(fileURLToPath(import.meta.url)), '..')
const stylesheet = join(root, 'resources/css/atrium.css')
const viewsDir = join(root, 'resources/views')

function bladeFiles(dir) {
    return readdirSync(dir).flatMap((entry) => {
        const path = join(dir, entry)

        if (statSync(path).isDirectory()) return bladeFiles(path)

        return entry.endsWith('.blade.php') ? [path] : []
    })
}

const files = bladeFiles(viewsDir).sort()

const sources = files
    .map((file) => `@source '${relative(join(root, 'resources/css'), file)}';`)
    .join('\n')

const marker = '/* @atrium-sources */'
const css = readFileSync(stylesheet, 'utf8')
const start = css.indexOf(marker)

if (start === -1) {
    throw new Error(`Missing ${marker} in resources/css/atrium.css`)
}

const end = css.indexOf('\n\n', start)

writeFileSync(stylesheet, css.slice(0, start) + marker + '\n' + sources + css.slice(end))

console.log(`Registered ${files.length} Blade files as Tailwind sources.`)

execFileSync(
    join(root, 'node_modules/.bin/tailwindcss'),
    ['-i', 'resources/css/atrium.css', '-o', 'public/atrium.css', '--minify'],
    { cwd: root, stdio: 'inherit' },
)
