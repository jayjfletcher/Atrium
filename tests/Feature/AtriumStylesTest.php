<?php

declare(strict_types=1);

use JayI\Atrium\Testing\AtriumStyles;

it('compiles every class atrium\'s own views use', function (): void {
    expect(AtriumStyles::missingClasses(dirname(__DIR__, 2).'/resources/views'))->toBe([]);
});

it('reports classes the stylesheet lacks', function (): void {
    $views = sys_get_temp_dir().'/atrium-styles-'.uniqid();
    mkdir($views);
    file_put_contents($views.'/page.blade.php', '<div class="flex not-a-real-utility" @class([\'gap-2\', \'also-missing\' => $x])></div>');

    expect(AtriumStyles::missingClasses($views))->toBe([
        'also-missing' => 'page.blade.php',
        'not-a-real-utility' => 'page.blade.php',
    ]);
});

it('finds views that style themselves', function (): void {
    $views = sys_get_temp_dir().'/atrium-inline-'.uniqid();
    mkdir($views);
    file_put_contents($views.'/a.blade.php', '<style>body { color: red }</style>');
    file_put_contents($views.'/b.blade.php', '<div style="color: red"></div>');
    file_put_contents($views.'/c.blade.php', '<x-atrium::banner standalone :x-bind:style="$s" />');

    expect(AtriumStyles::inlineStyles($views))->toBe(['a.blade.php', 'b.blade.php']);
});

it('keeps the layout utilities packages rely on', function (string $class): void {
    $views = sys_get_temp_dir().'/atrium-safelist-'.uniqid();
    mkdir($views);
    file_put_contents($views.'/page.blade.php', '<div class="'.$class.'"></div>');

    expect(AtriumStyles::missingClasses($views))->toBe([]);
})->with(['lg:grid-cols-5', 'sm:pt-6.5', 'w-72', 'font-mono', 'max-h-60', 'aspect-square', 'gap-x-6', 'hover:underline', 'dark:bg-surface-dark-alt/50', 'break-all']);
