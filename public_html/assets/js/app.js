(function () {
    var basePath = document.body ? document.body.getAttribute('data-base-path') || '' : '';

    function endpoint(path) {
        return basePath + path;
    }

    function number(form, name, fallback) {
        var field = form.elements[name];
        var value = field ? parseFloat(field.value) : fallback;
        return Number.isFinite(value) && value > 0 ? value : fallback;
    }

    function round(value) {
        return Math.round((value + Number.EPSILON) * 100) / 100;
    }

    function formatQty(value) {
        if (!Number.isFinite(value)) {
            return '';
        }

        var rounded = Math.round((value + Number.EPSILON) * 1000000) / 1000000;
        return rounded.toFixed(6).replace(/\.?0+$/, '');
    }

    function withWaste(value, waste) {
        return round(value * (1 + waste / 100));
    }

    function item(category, name, unit, qty, notes) {
        return { category: category, name: name, unit: unit, qty: qty, notes: notes };
    }

    function ceiling(form) {
        var area = number(form, 'ceiling_area', 12);
        var boardCount = area / 2.88;

        return {
            type: 'ceiling',
            title: 'Furring system estimate',
            area: area,
            summary: [
                ['Area', area, 'sqm'],
                ['Boards', boardCount, 'pcs']
            ],
            items: [
                item('Boards', 'Gypsum Board 1.2 x 2.4mtrs', 'pcs', boardCount, 'Area / 2.88'),
                item('Framing', 'Main Channel', 'pcs', area * 0.4, 'Area * 1.2 / 3'),
                item('Framing', 'Furring Channel', 'pcs', area * 0.5, 'Area * 1.5 / 3'),
                item('Framing', 'Angle', 'pcs', area / 3, 'Area / 3'),
                item('Suspension', 'Hanger Wire', 'roll', area / 100, 'Area / 100'),
                item('Suspension', 'Adjustable Clip', 'pcs', area, 'Area * 1'),
                item('Fixing', 'Wire Clip', 'pcs', area * 3, 'Area * 3'),
                item('Fixing', 'Channel Bracket', 'pcs', area, 'Area * 1'),
                item('Fixing', 'Clip Nail and Cartridge', 'pcs', area * 3, 'Area * 3'),
                item('Fixing', 'Steel Nail', 'pcs', area * 10, 'Area * 10'),
                item('Finishing', 'Fiber Tape', 'roll', area * 2 / 90, 'Area * 2 / 90'),
                item('Finishing', 'Ready Mix', 'drum', area * 0.5 / 28, 'Area * 0.5 / 28'),
                item('Fixing', 'Screw 1"', 'pcs', area * 10, 'Area * 10')
            ]
        };
    }

    function partition(form) {
        var area = number(form, 'partition_area', 293);
        var glasswool = form.elements.partition_glasswool && form.elements.partition_glasswool.checked;
        var boardCount = area / 2.88;

        var items = [
            item('Boards', 'Gypsum Board 1.2 x 2.4mtrs', 'pcs', boardCount, 'Area / 2.88'),
            item('Framing', 'Stud', 'pcs', area * 2.5 / 3, 'Area * 2.5 / 3'),
            item('Framing', 'Track', 'pcs', area * 1.2 / 3, 'Area * 1.2 / 3'),
            item('Fixing', 'Steel Nail', 'pcs', area * 10, 'Area * 10'),
            item('Finishing', 'Fiber Tape', 'roll', area * 2 / 90, 'Area * 2 / 90'),
            item('Finishing', 'Ready Mix', 'drum', area * 0.5 / 28, 'Area * 0.5 / 28'),
            item('Fixing', 'Screw 1"', 'pcs', area * 10, 'Area * 10'),
            item('Fixing', 'screw 1/2"', 'pcs', area * 6, 'Area * 6')
        ];

        if (glasswool) {
            items.push(item('Insulation', 'Glasswool 60 x 120cm', 'pcs', area / 0.72, 'Area / 0.72'));
        }

        return {
            type: 'partition',
            title: 'Partition system estimate',
            area: area,
            summary: [
                ['Area', area, 'sqm'],
                ['Boards', boardCount, 'pcs']
            ],
            items: items
        };
    }

    function tile(form) {
        var area = number(form, 'tile_area', 20);
        var tileSystem = form.elements.tile_system.value;
        var clipIn = tileSystem === 'clip-in';

        return clipIn
            ? {
                type: 'tile',
                title: 'Clip-in tile estimate',
                area: area,
                summary: [
                    ['Area', area, 'sqm'],
                    ['System', 'Clip In', ''],
                ],
                items: [
                    item('Tiles', 'Clip In Tile', 'sqm', area, 'Area'),
                    item('Grid', 'Spring Tee', 'pcs', area * 0.4, 'Area * 0.4'),
                    item('Perimeter', 'Edge Trim', 'pcs', area * 0.33, 'Area * 0.33'),
                    item('Framing', 'Main Channel', 'pcs', area * 0.23, 'Area * 0.23'),
                    item('Fixing', 'Wire Clip', 'pcs', area * 2, 'Area * 2')
                ]
            }
            : {
                type: 'tile',
                title: 'Lay-in tile estimate',
                area: area,
                summary: [
                    ['Area', area, 'sqm'],
                    ['System', 'Lay In', ''],
                ],
                items: [
                    item('Tiles', 'Tile', 'sqm', area, 'Area'),
                    item('Grid', 'Main Tee 3.6 m', 'pcs', area * 0.225, 'Area * 0.225'),
                    item('Perimeter', 'Wall Angle 3.6 m', 'pcs', area * 0.225, 'Area * 0.225'),
                    item('Perimeter', 'Wall Angle 3 m', 'pcs', area * 0.25, 'Area * 0.25'),
                    item('Grid', 'Cross Tee 120', 'pcs', area * 1.3, 'Area * 1.3'),
                    item('Grid', 'Cross Tee 60', 'pcs', area * 1.3, 'Area * 1.3'),
                    item('Suspension', 'Wire Roll', 'roll', area * 1.415 / 90, 'Area * 1.415 / 90'),
                    item('Suspension', 'Tie Hanger', 'pkt', area / 100, 'Area / 100'),
                    item('Fixing', 'Adjustable Clip', 'pcs', area * 1.012, 'Area * 1.012'),
                    item('Fixing', 'Nail', 'pcs', area * 3, 'Area * 3')
                ]
            };
    }

    function acoustic(form) {
        var length = number(form, 'acoustic_length', 10);
        var width = number(form, 'acoustic_width', 7);
        var height = number(form, 'acoustic_height', 3.2);
        var existing = Math.min(number(form, 'existing_absorption', 0.12), 0.7);
        var nrc = Math.min(number(form, 'panel_nrc', 0.85), 1);
        var targets = { office: 0.7, classroom: 0.6, cinema: 0.35, restaurant: 0.75, mosque: 1.1 };
        var roomType = form.elements.room_type.value;
        var target = targets[roomType] || 0.7;
        var volume = round(length * width * height);
        var surface = round(2 * (length * width + length * height + width * height));
        var existingAbsorption = round(surface * existing);
        var required = Math.max(0, round((0.161 * volume) / target - existingAbsorption));
        var treatment = round(required / nrc);

        return {
            type: 'acoustic',
            title: 'Room acoustic treatment estimate',
            area: treatment,
            summary: [
                ['Volume', volume, 'cum'],
                ['Target RT', target, 'sec'],
                ['Existing absorption', existingAbsorption, 'sabins'],
                ['Treatment', treatment, 'sqm']
            ],
            items: [
                item('Acoustic', 'Ceiling cloud or baffle absorption', 'sqm', treatment * 0.45, 'Approx. 45% of added absorption.'),
                item('Acoustic', 'Wall acoustic panels', 'sqm', treatment * 0.55, 'Approx. 55% of added absorption.'),
                item('Engineering', 'Site acoustic verification', 'visit', 1, 'Recommended for critical rooms.')
            ]
        };
    }

    function render(root, result) {
        var summary = root.querySelector('[data-summary]');
        var body = root.querySelector('[data-boq-body]');
        summary.innerHTML = result.summary.map(function (row) {
            return '<div class="summary-item"><span>' + row[0] + '</span><strong>' + formatQty(row[1]) + '</strong><small>' + row[2] + '</small></div>';
        }).join('');

        body.innerHTML = result.items.map(function (row) {
            return '<tr><td>' + row.category + '</td><td><strong>' + row.name + '</strong></td><td>' + row.unit + '</td><td>' + formatQty(row.qty) + '</td><td>' + row.notes + '</td></tr>';
        }).join('');
    }

    function initCalculator(root) {
        var form = root.querySelector('.calculator-form');
        var mode = form.elements.mode;
        var tileSystem = form.elements.tile_system;
        var status = root.querySelector('[data-calculator-status]');
        var currentResult = null;
        var exportButtons = root.querySelectorAll('[data-export-boq]');

        function updateSections() {
            root.querySelectorAll('[data-section]').forEach(function (section) {
                section.classList.toggle('is-hidden', section.getAttribute('data-section') !== mode.value);
            });
        }

        function calculate() {
            currentResult = mode.value === 'partition' ? partition(form) : mode.value === 'tile' ? tile(form) : mode.value === 'acoustic' ? acoustic(form) : ceiling(form);
            render(root, currentResult);
            status.textContent = currentResult.title + ' generated.';
        }

        mode.addEventListener('change', function () {
            updateSections();
            calculate();
        });

        if (tileSystem) {
            tileSystem.addEventListener('change', function () {
                if (mode.value === 'tile') {
                    calculate();
                }
            });
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            calculate();
        });

        root.querySelector('[data-save-boq]').addEventListener('click', function () {
            if (!currentResult) {
                calculate();
            }

            fetch(endpoint('/api/boq'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    project_name: form.elements.project_name.value,
                    calculator_type: currentResult.type,
                    area: currentResult.area,
                    summary: currentResult.summary,
                    items: currentResult.items
                })
            })
                .then(function (response) { return response.json(); })
                .then(function (data) { status.textContent = data.message || 'BOQ saved.'; })
                .catch(function () { status.textContent = 'Unable to save BOQ.'; });
        });

        function exportBoq(format) {
            if (!currentResult) {
                calculate();
            }

            status.textContent = 'Preparing ' + format.toUpperCase() + ' export...';

            fetch(endpoint('/api/boq/export'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    project_name: form.elements.project_name.value,
                    calculator_type: currentResult.type,
                    area: currentResult.area,
                    summary: currentResult.summary,
                    items: currentResult.items,
                    format: format
                })
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Export failed');
                    }

                    return response.blob().then(function (blob) {
                        return {
                            blob: blob,
                            filename: (format === 'pdf' ? 'boq-estimate.pdf' : 'boq-estimate.csv')
                        };
                    });
                })
                .then(function (file) {
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(file.blob);
                    link.download = file.filename;
                    link.click();
                    URL.revokeObjectURL(link.href);
                    status.textContent = format.toUpperCase() + ' export ready.';
                })
                .catch(function () {
                    status.textContent = 'Unable to export BOQ.';
                });
        }

        exportButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                exportBoq(button.getAttribute('data-export-boq'));
            });
        });

        root.querySelector('[data-download-boq]').addEventListener('click', function () {
            if (!currentResult) {
                calculate();
            }

            var blob = new Blob([JSON.stringify(currentResult, null, 2)], { type: 'application/json' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'boq-estimate.json';
            link.click();
            URL.revokeObjectURL(link.href);
        });

        updateSections();
        calculate();
    }

    function initInquiry(form) {
        var status = form.querySelector('[data-inquiry-status]');

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            var payload = Object.fromEntries(new FormData(form).entries());

            fetch(endpoint('/api/inquiries'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    status.textContent = data.message || 'Inquiry sent.';
                    if (data.ok) {
                        form.reset();
                    }
                })
                .catch(function () { status.textContent = 'Unable to send inquiry.'; });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-calculator]').forEach(initCalculator);
        document.querySelectorAll('[data-inquiry-form]').forEach(initInquiry);
    });
}());
