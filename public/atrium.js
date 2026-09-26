/**
 * Atrium dashboard behavior.
 *
 * Registers the Alpine components the shipped views expect. Alpine itself is
 * loaded separately; see the layout.
 *
 * The dashboard keeps its layout in Alpine state, seeded from the server. The
 * widgets themselves are rendered server-side so their views keep full Blade
 * and Livewire support; this component only decides which of those rendered
 * widgets is shown, how wide it is, and in what order.
 */
(function () {
    'use strict'

    var COLUMNS = 12

    function csrf() {
        var meta = document.querySelector('meta[name="csrf-token"]')
        return meta ? meta.getAttribute('content') : null
    }

    /**
     * Storage can throw (private windows, blocked site data), and a lost
     * preference is harmless, so both helpers swallow failures.
     */
    function remember(key, value) {
        try {
            if (value === null) {
                localStorage.removeItem(key)
            } else {
                localStorage.setItem(key, value)
            }
        } catch (error) {}
    }

    function recall(key) {
        try {
            return localStorage.getItem(key)
        } catch (error) {
            return null
        }
    }

    /**
     * The sidebar shell: the off-canvas drawer below `lg`, the collapsed
     * icon rail above it, which navigation groups are folded, and the
     * floating label or child menu shown when hovering the rail.
     *
     * The rail's collapsed state lives on <html> as data-atrium-sidebar, set
     * before paint by the layout's head script, so every rail style is plain
     * CSS and nothing here has to run for the page to look right.
     */
    window.atriumShell = function () {
        var closedGroups = []

        try {
            closedGroups = JSON.parse(recall('atrium.nav-groups') || '[]')
        } catch (error) {}

        return {
            drawer: false,
            collapsed: document.documentElement.dataset.atriumSidebar === 'collapsed',
            closedGroups: closedGroups,
            flyout: null,
            hideTimer: null,

            toggleCollapsed: function () {
                this.collapsed = !this.collapsed
                this.flyout = null

                if (this.collapsed) {
                    document.documentElement.dataset.atriumSidebar = 'collapsed'
                } else {
                    delete document.documentElement.dataset.atriumSidebar
                }

                remember('atrium.sidebar', this.collapsed ? 'collapsed' : null)
            },

            isGroupOpen: function (name) {
                return this.closedGroups.indexOf(name) === -1
            },

            toggleGroup: function (name) {
                var index = this.closedGroups.indexOf(name)

                if (index === -1) {
                    this.closedGroups.push(name)
                } else {
                    this.closedGroups.splice(index, 1)
                }

                remember('atrium.nav-groups', JSON.stringify(this.closedGroups))
            },

            /** Whether the sidebar is currently showing as the icon rail. */
            isRail: function () {
                return this.collapsed && window.matchMedia('(min-width: 64rem)').matches
            },

            /**
             * Show an item's label, and its children if it has any, beside
             * the rail. It is positioned fixed rather than nested in the item
             * because the scrolling nav would clip anything that overflows it.
             */
            peek: function (element) {
                if (!this.isRail()) return

                clearTimeout(this.hideTimer)

                var rect = element.getBoundingClientRect()
                var data = JSON.parse(element.dataset.flyout || '{}')

                data.top = rect.top
                data.left = rect.right + 8
                this.flyout = data
            },

            /** Hide after a beat, so the pointer can cross into a child menu. */
            unpeek: function () {
                var self = this

                clearTimeout(this.hideTimer)

                this.hideTimer = setTimeout(function () {
                    self.flyout = null
                }, 120)
            },

            holdFlyout: function () {
                clearTimeout(this.hideTimer)
            },
        }
    }

    /**
     * Light, dark, or following the system. The head script applies the
     * stored choice before paint; this keeps it applied when the choice or
     * the system setting changes.
     */
    window.atriumAppearance = function () {
        return {
            theme: recall('atrium.theme') || 'system',

            init: function () {
                var self = this

                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
                    self.apply()
                })
            },

            choose: function (theme) {
                this.theme = theme

                remember('atrium.theme', theme === 'system' ? null : theme)

                this.apply()
            },

            apply: function () {
                var dark = this.theme === 'dark' ||
                    (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)

                document.documentElement.classList.toggle('dark', dark)
            },
        }
    }

    window.atriumDashboard = function (config) {
        return {
            editing: false,
            saving: false,
            saved: 0,
            dragging: null,
            endpoint: config.endpoint,
            placements: config.placements || [],

            indexOf: function (id) {
                for (var i = 0; i < this.placements.length; i++) {
                    if (this.placements[i].id === id) return i
                }

                return -1
            },

            /** Whether a server-rendered widget is still placed. */
            has: function (id) {
                return this.indexOf(id) !== -1
            },

            /**
             * Width and position come from state, so reordering and resizing
             * apply without re-rendering the widget itself.
             */
            styleFor: function (id) {
                var index = this.indexOf(id)
                if (index === -1) return ''

                var placement = this.placements[index]

                return '--atrium-widget-width: ' + placement.grid_width +
                    '; --atrium-widget-height: ' + placement.grid_height +
                    '; order: ' + index + ';'
            },

            /**
             * Place a widget the user picked. Nothing places itself; this only
             * ever runs from a click in the picker. The new widget has no
             * server-rendered markup yet, so this path reloads once saved.
             */
            add: function (key, width, height) {
                this.placements.push({
                    id: null,
                    widget_key: key,
                    grid_width: width || 4,
                    grid_height: height || 2,
                })

                this.$dispatch('atrium-modal-close', 'atrium-widget-picker')

                this.save(true)
            },

            remove: function (id) {
                var index = this.indexOf(id)
                if (index === -1) return

                this.placements.splice(index, 1)
                this.save()
            },

            /** Grow or shrink a widget within the 12-column grid. */
            widen: function (id, delta) {
                var index = this.indexOf(id)
                if (index === -1) return

                var placement = this.placements[index]

                placement.grid_width = Math.min(
                    COLUMNS,
                    Math.max(1, placement.grid_width + delta)
                )

                this.save()
            },

            startDrag: function (id, event) {
                if (!this.editing) {
                    event.preventDefault()

                    return
                }

                this.dragging = id

                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move'
                    event.dataTransfer.setData('text/plain', String(id))
                }
            },

            dragOver: function (id) {
                if (this.dragging === null || this.dragging === id) return

                var from = this.indexOf(this.dragging)
                var to = this.indexOf(id)

                if (from === -1 || to === -1) return

                var moved = this.placements.splice(from, 1)[0]
                this.placements.splice(to, 0, moved)
            },

            drop: function () {
                if (this.dragging === null) return

                this.dragging = null
                this.save()
            },

            endDrag: function () {
                this.dragging = null
            },

            /**
             * Saves are queued rather than fired in parallel. Two overlapping
             * requests can arrive out of order, and because a save replaces
             * the whole layout, a late one carrying stale widgets would undo
             * the newer edit.
             */
            save: function (reload) {
                var self = this

                if (!this.endpoint) return Promise.resolve()

                var widgets = this.placements.map(function (placement, index) {
                    return {
                        widget_key: placement.widget_key,
                        grid_row: 0,
                        grid_column: 0,
                        grid_width: placement.grid_width,
                        grid_height: placement.grid_height,
                        sort: index,
                    }
                })

                this.saving = true

                this.queue = (this.queue || Promise.resolve()).then(function () {
                    return self.send(widgets, reload)
                })

                return this.queue
            },

            send: function (widgets, reload) {
                var self = this

                return fetch(this.endpoint, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf(),
                    },
                    body: JSON.stringify({ widgets: widgets }),
                }).then(function () {
                    if (reload) {
                        window.location.reload()

                        return
                    }

                    // Increments once per completed save. The layout is on the
                    // server by the time this changes, which gives both the
                    // interface and the browser tests something real to wait
                    // on rather than a fixed delay.
                    self.saved++
                }).finally(function () {
                    self.saving = false
                })
            },
        }
    }
})()
