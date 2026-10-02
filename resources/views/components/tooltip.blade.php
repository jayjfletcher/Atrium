@props([
    'text',
    'position' => 'top',
])

{{-- The bubble is teleported to <body> and fixed beside the trigger, so no
     scrolling or overflow-hidden ancestor can clip it. It closes on scroll
     rather than drifting from its trigger, and stays inside the viewport. --}}
<span {{ $attributes->class('relative inline-flex') }}
      x-data="{
          show: false,
          style: '',
          open() {
              const r = this.$el.getBoundingClientRect(), gap = 8;
              const [x, y, shift] = {
                  top: [r.left + r.width / 2, r.top - gap, '-50%, -100%'],
                  bottom: [r.left + r.width / 2, r.bottom + gap, '-50%, 0'],
                  left: [r.left - gap, r.top + r.height / 2, '-100%, -50%'],
                  right: [r.right + gap, r.top + r.height / 2, '0, -50%'],
              }[@js($position)] ?? [r.left + r.width / 2, r.top - gap, '-50%, -100%'];
              this.style = `left: ${x}px; top: ${y}px; transform: translate(${shift})`;
              this.show = true;
              this.$nextTick(() => {
                  const tip = this.$refs.tip.getBoundingClientRect(), edge = 4;
                  const nudge = tip.left < edge ? edge - tip.left : (tip.right > innerWidth - edge ? innerWidth - edge - tip.right : 0);
                  if (nudge) this.style += `; margin-left: ${nudge}px`;
              });
          },
      }"
      x-on:mouseenter="open()" x-on:mouseleave="show = false"
      x-on:focusin="open()" x-on:focusout="show = false"
      x-on:scroll.window.capture="show = false">
    {{ $slot }}

    <template x-teleport="body">
        <span x-ref="tip" :style="style"
              class="pointer-events-none fixed z-50 whitespace-nowrap rounded-md bg-on-surface-strong px-2 py-1 text-xs font-medium text-surface shadow-lg dark:bg-on-surface-dark-strong dark:text-surface-dark"
              x-show="show" x-cloak x-transition.opacity.duration.100ms role="tooltip">{{ $text }}</span>
    </template>
</span>
