<section class="panel calculator-panel" data-calculator>
    <div class="panel-heading">
        <p class="eyebrow">Estimator</p>
        <h2>Material calculator and BOQ preview</h2>
    </div>

    <form class="calculator-form">
        <label>
            <span>Project name</span>
            <input type="text" name="project_name" value="UAE fit-out estimate" required>
        </label>

        <label>
            <span>System</span>
            <select name="mode" data-mode>
                <option value="ceiling">Gypsum ceiling</option>
                <option value="partition">Drywall partition</option>
                <option value="tile">Acoustic ceiling tile</option>
                <option value="acoustic">Room acoustic treatment</option>
            </select>
        </label>

        <div class="form-grid calculator-section" data-section="ceiling">
            <label><span>Length (m)</span><input type="number" step="0.1" name="ceiling_length" value="12"></label>
            <label><span>Width (m)</span><input type="number" step="0.1" name="ceiling_width" value="8"></label>
            <label><span>Board layers</span><input type="number" step="1" name="ceiling_layers" value="1"></label>
            <label><span>Waste (%)</span><input type="number" step="1" name="ceiling_waste" value="8"></label>
        </div>

        <div class="form-grid calculator-section is-hidden" data-section="partition">
            <label><span>Length (m)</span><input type="number" step="0.1" name="partition_length" value="18"></label>
            <label><span>Height (m)</span><input type="number" step="0.1" name="partition_height" value="3.2"></label>
            <label><span>Layers each side</span><input type="number" step="1" name="partition_layers" value="1"></label>
            <label><span>Stud spacing (m)</span><input type="number" step="0.1" name="partition_spacing" value="0.6"></label>
            <label><span>Waste (%)</span><input type="number" step="1" name="partition_waste" value="8"></label>
            <label class="check-row"><input type="checkbox" name="partition_rockwool" checked><span>Include rockwool</span></label>
        </div>

        <div class="form-grid calculator-section is-hidden" data-section="tile">
            <label><span>Length (m)</span><input type="number" step="0.1" name="tile_length" value="14"></label>
            <label><span>Width (m)</span><input type="number" step="0.1" name="tile_width" value="9"></label>
            <label><span>Waste (%)</span><input type="number" step="1" name="tile_waste" value="7"></label>
            <label>
                <span>Tile size</span>
                <select name="tile_size">
                    <option value="600x600">600 x 600 mm</option>
                    <option value="600x1200">600 x 1200 mm</option>
                </select>
            </label>
        </div>

        <div class="form-grid calculator-section is-hidden" data-section="acoustic">
            <label><span>Length (m)</span><input type="number" step="0.1" name="acoustic_length" value="10"></label>
            <label><span>Width (m)</span><input type="number" step="0.1" name="acoustic_width" value="7"></label>
            <label><span>Height (m)</span><input type="number" step="0.1" name="acoustic_height" value="3.2"></label>
            <label><span>Existing absorption</span><input type="number" step="0.01" name="existing_absorption" value="0.12"></label>
            <label><span>Panel NRC</span><input type="number" step="0.05" name="panel_nrc" value="0.85"></label>
            <label>
                <span>Room type</span>
                <select name="room_type">
                    <option value="office">Office</option>
                    <option value="classroom">Classroom</option>
                    <option value="cinema">Cinema</option>
                    <option value="restaurant">Restaurant</option>
                    <option value="mosque">Mosque</option>
                </select>
            </label>
        </div>

        <div class="button-row">
            <button class="button primary" type="submit">Calculate</button>
            <button class="button" type="button" data-save-boq>Save BOQ</button>
            <button class="button" type="button" data-download-boq>Download JSON</button>
        </div>
    </form>

    <div class="summary-grid" data-summary></div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Item</th>
                    <th>Unit</th>
                    <th>Qty</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody data-boq-body></tbody>
        </table>
    </div>
    <p class="status-message" data-calculator-status></p>
</section>
