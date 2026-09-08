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
