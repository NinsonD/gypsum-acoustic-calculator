<section class="page-heading">
    <p class="eyebrow">Contractor inquiry</p>
    <h1>Send a project request</h1>
    <p>Use this form for gypsum ceiling, drywall partition, acoustic treatment, BOQ, or technical submittal requests.</p>
</section>

<section class="panel">
    <form class="contact-form" data-inquiry-form>
        <div class="form-grid">
            <label><span>Name</span><input name="name" required></label>
            <label><span>Email</span><input type="email" name="email" required></label>
            <label><span>Phone</span><input name="phone" required></label>
            <label>
                <span>Inquiry type</span>
                <select name="inquiry_type">
                    <option>BOQ request</option>
                    <option>Gypsum ceiling</option>
                    <option>Drywall partition</option>
                    <option>Acoustic treatment</option>
                    <option>Technical submittal</option>
                </select>
            </label>
        </div>
        <label><span>Message</span><textarea name="message" rows="6" required></textarea></label>
        <button class="button primary" type="submit">Send inquiry</button>
        <p class="status-message" data-inquiry-status></p>
    </form>
</section>
