(function () {
    var boardArea = 2.88;
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

    function withWaste(value, waste) {
        return round(value * (1 + waste / 100));
    }

    function item(category, name, unit, qty, notes) {
        return { category: category, name: name, unit: unit, qty: round(qty), notes: notes };
    }

    function ceiling(form) {
        var length = number(form, 'ceiling_length', 12);
        var width = number(form, 'ceiling_width', 8);
        var layers = number(form, 'ceiling_layers', 1);
        var waste = number(form, 'ceiling_waste', 8);
        var area = round(length * width);
        var perimeter = round((length + width) * 2);
        var boardCount = Math.ceil(withWaste((area * layers) / boardArea, waste));
        var mainLines = Math.ceil(width / 1.2) + 1;
        var furringLines = Math.ceil(length / 0.4) + 1;
        var hangers = (Math.ceil(length / 1.2) + 1) * (Math.ceil(width / 1.2) + 1);

        return {
            title: 'Gypsum ceiling estimate',
            summary: [
                ['Area', area, 'sqm'],
                ['Perimeter', perimeter, 'lm'],
                ['Boards', boardCount, 'pcs'],
                ['Waste', waste, '%']
            ],
            items: [
                item('Boards', 'Gypsum board 1200 x 2400 mm', 'pcs', boardCount, 'Based on selected board layers.'),
                item('Framing', 'Main channel', 'lm', withWaste(mainLines * length, waste), 'Typical 1200 mm spacing.'),
                item('Framing', 'Furring channel', 'lm', withWaste(furringLines * width, waste), 'Typical 400 mm spacing.'),
                item('Framing', 'Perimeter wall angle', 'lm', withWaste(perimeter, waste), 'Room perimeter allowance.'),
                item('Suspension', 'Threaded rod and hanger set', 'sets', Math.ceil(withWaste(hangers, waste)), 'Typical 1200 x 1200 mm hanger grid.'),
                item('Fixing', 'Gypsum screws', 'pcs', boardCount * 45, 'Approximate screw allowance.'),
                item('Finishing', 'Joint compound', 'kg', withWaste(area * 0.35 * layers, waste), 'Joint treatment allowance.')
            ]
        };
    }

    function partition(form) {
        var length = number(form, 'partition_length', 18);
        var height = number(form, 'partition_height', 3.2);
        var layers = number(form, 'partition_layers', 1);
        var spacing = Math.min(number(form, 'partition_spacing', 0.6), 0.6);
        var waste = number(form, 'partition_waste', 8);
        var rockwool = form.elements.partition_rockwool && form.elements.partition_rockwool.checked;
        var oneSideArea = round(length * height);
        var totalBoardArea = round(oneSideArea * 2 * layers);
        var boardCount = Math.ceil(withWaste(totalBoardArea / boardArea, waste));
        var studs = Math.ceil(length / spacing) + 1;

        var items = [
            item('Boards', 'Gypsum plasterboard', 'pcs', boardCount, 'Both faces and selected layers.'),
            item('Framing', 'Floor and ceiling track', 'lm', withWaste(length * 2, waste), 'Top and bottom track.'),
            item('Framing', 'C-stud', 'pcs', Math.ceil(withWaste(studs, waste)), 'Based on center-to-center spacing.'),
            item('Fixing', 'Drywall screws', 'pcs', boardCount * 55, 'Approximate screw allowance.'),
            item('Finishing', 'Joint compound', 'kg', withWaste(totalBoardArea * 0.28, waste), 'Jointing allowance.'),
            item('Acoustic', 'Acoustic sealant', 'tubes', Math.ceil(withWaste((length * 2 + height * 2) / 9, waste)), 'Seal perimeter gaps.')
        ];

        if (rockwool) {
            items.push(item('Insulation', 'Rockwool slab', 'sqm', withWaste(oneSideArea, waste), 'Cavity insulation allowance.'));
        }

        return {
            title: 'Drywall partition estimate',
            summary: [
                ['One side area', oneSideArea, 'sqm'],
                ['Board area', totalBoardArea, 'sqm'],
                ['Studs', studs, 'pcs'],
                ['Waste', waste, '%']
            ],
            items: items
        };
    }

    function tile(form) {
        var length = number(form, 'tile_length', 14);
        var width = number(form, 'tile_width', 9);
        var waste = number(form, 'tile_waste', 7);
        var tileSize = form.elements.tile_size.value;
        var tileArea = tileSize === '600x1200' ? 0.72 : 0.36;
        var area = round(length * width);
        var tileCount = Math.ceil(withWaste(area / tileArea, waste));
        var mainLines = Math.ceil(width / 1.2) + 1;
        var crossRows = Math.ceil(length / 0.6) + 1;

        return {
            title: 'Acoustic ceiling tile estimate',
            summary: [
                ['Area', area, 'sqm'],
                ['Tile size', tileSize.replace('x', ' x '), 'mm'],
                ['Tiles', tileCount, 'pcs'],
                ['Waste', waste, '%']
            ],
            items: [
                item('Tiles', 'Mineral fiber acoustic tile', 'pcs', tileCount, 'Verify edge detail.'),
                item('Grid', 'Main tee runner', 'lm', withWaste(mainLines * length, waste), 'Typical 1200 mm spacing.'),
                item('Grid', 'Cross tee', 'lm', withWaste(crossRows * width, waste), 'Allowance for 600 mm module.'),
                item('Grid', 'Wall angle', 'lm', withWaste((length + width) * 2, waste), 'Perimeter trim.'),
                item('Suspension', 'Hanger wire and anchor', 'sets', Math.ceil(withWaste((Math.ceil(length / 1.2) + 1) * mainLines, waste)), 'Main runner hanger spacing.')
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
            title: 'Room acoustic treatment estimate',
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
            return '<div class="summary-item"><span>' + row[0] + '</span><strong>' + row[1] + '</strong><small>' + row[2] + '</small></div>';
        }).join('');

        body.innerHTML = result.items.map(function (row) {
            return '<tr><td>' + row.category + '</td><td><strong>' + row.name + '</strong></td><td>' + row.unit + '</td><td>' + row.qty + '</td><td>' + row.notes + '</td></tr>';
        }).join('');
    }

    function initCalculator(root) {
        var form = root.querySelector('.calculator-form');
        var mode = form.elements.mode;
        var status = root.querySelector('[data-calculator-status]');
        var currentResult = null;

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
                    items: currentResult.items
                })
            })
                .then(function (response) { return response.json(); })
                .then(function (data) { status.textContent = data.message || 'BOQ saved.'; })
                .catch(function () { status.textContent = 'Unable to save BOQ.'; });
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
