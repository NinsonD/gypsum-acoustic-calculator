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
                <option value="ceiling">Furring system</option>
                <option value="partition">Partition system</option>
                <option value="tile">Ceiling tile system</option>
                <option value="acoustic">Room acoustic treatment</option>
            </select>
        </label>

        <div class="form-grid calculator-section" data-section="ceiling">
            <label><span>Area (sqm)</span><input type="number" step="0.01" name="ceiling_area" value="100"></label>
        </div>

        <div class="form-grid calculator-section is-hidden" data-section="partition">
            <label><span>Area (sqm)</span><input type="number" step="0.01" name="partition_area" value="293"></label>
            <label class="check-row"><input type="checkbox" name="partition_glasswool" checked><span>Include glasswool</span></label>
        </div>

        <div class="form-grid calculator-section is-hidden" data-section="tile">
            <label>
                <span>Area (sqm)</span>
                <input type="number" step="0.01" name="tile_area" value="20">
            </label>
            <label>
                <span>Tile system</span>
                <select name="tile_system">
                    <option value="clip-in">Clip In</option>
                    <option value="lay-in">Lay In</option>
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
            <button class="button" type="button" data-export-boq="csv">Download Excel</button>
            <button class="button" type="button" data-export-boq="pdf">Download PDF</button>
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
