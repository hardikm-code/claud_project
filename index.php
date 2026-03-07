<?php
require_once 'config.php';

// Fetch incident updates from DB (fall back to static if table missing)
$updates = [];
$result = $conn->query("SELECT * FROM incident_updates ORDER BY posted_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $updates[] = $row;
    }
}

// Handle lost order claim form
$formMsg = '';
$formSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_claim'])) {
    $name     = sanitize($_POST['cust_name'] ?? '');
    $email    = filter_var(trim($_POST['cust_email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $order_no = sanitize($_POST['order_number'] ?? '');
    $amount   = (float)($_POST['order_amount'] ?? 0);
    $time     = sanitize($_POST['order_time'] ?? '');
    $desc     = sanitize($_POST['description'] ?? '');

    if ($name && $email && $desc) {
        $stmt = $conn->prepare(
            "INSERT INTO order_claims (customer_name, customer_email, order_number, order_amount, order_time, description)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssdss", $name, $email, $order_no, $amount, $time, $desc);
        if ($stmt->execute()) {
            $formSuccess = true;
            $formMsg = 'Your claim has been received. We will contact you at <strong>' . htmlspecialchars($email) . '</strong> within 24 hours.';
        } else {
            $formMsg = 'Something went wrong saving your claim. Please email us directly.';
        }
        $stmt->close();
    } else {
        $formMsg = 'Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Incident Report &amp; Apology | <?php echo COMPANY_NAME; ?></title>
    <meta name="description" content="A formal apology and incident report for the website outage on <?php echo INCIDENT_DATE; ?>.">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Resolved status bar -->
<div class="topbar">
    Current status: &nbsp;
    <span style="background: rgba(255,255,255,0.15); padding: 2px 10px; border-radius: 20px; color: #fff;">
        &#10003; All Systems Operational
    </span>
    &nbsp;&mdash;&nbsp; Incident resolved <?php echo INCIDENT_DATE; ?> at <?php echo INCIDENT_END; ?>
</div>

<!-- Header -->
<header class="site-header">
    <div class="header-inner">
        <div class="logo">
            <div class="logo-icon">T</div>
            <?php echo COMPANY_NAME; ?>
        </div>
        <nav class="header-nav">
            <a href="#incident">Incident Report</a>
            <a href="#actions">Our Actions</a>
            <a href="#claim">Lost Orders</a>
            <a href="admin.php" style="color: var(--gray);">Admin</a>
        </nav>
    </div>
</header>

<!-- Hero Apology Banner -->
<section class="incident-hero">
    <div class="container">
        <div class="eyebrow">&#9888; Post-Incident Report</div>
        <h1>We Sincerely Apologise for<br>Last Night's Outage</h1>
        <p class="subtitle">
            Your store was unavailable for over 3 hours during your sale last night.
            We take full responsibility, and we are deeply sorry for the disruption and any orders lost as a result.
        </p>
        <div class="incident-meta">
            <div class="incident-meta-item">
                <span class="label">Incident Date</span>
                <span class="value"><?php echo INCIDENT_DATE; ?></span>
            </div>
            <div class="incident-meta-item">
                <span class="label">Outage Started</span>
                <span class="value"><?php echo INCIDENT_START; ?></span>
            </div>
            <div class="incident-meta-item">
                <span class="label">Service Restored</span>
                <span class="value"><?php echo INCIDENT_END; ?></span>
            </div>
            <div class="incident-meta-item">
                <span class="label">Total Duration</span>
                <span class="value"><?php echo INCIDENT_DURATION; ?></span>
            </div>
            <div class="incident-meta-item">
                <span class="label">Current Status</span>
                <span class="value">
                    <span class="status-pill status-resolved">Resolved</span>
                </span>
            </div>
        </div>
    </div>
</section>

<!-- Formal Apology Letter -->
<section class="page-section" id="apology">
    <div class="container">
        <div class="letter-card">
            <div class="section-label">Formal Apology</div>
            <h2 class="section-title" style="margin-bottom: 28px;">A Message From Our Team</h2>

            <div class="letter-body">
                <p>Dear <?php echo CLIENT_STORE; ?> Team,</p>

                <p>
                    We are writing to formally apologise for the serious service disruption that occurred on
                    <?php echo INCIDENT_DATE; ?>, beginning at <?php echo INCIDENT_START; ?> and lasting until
                    <?php echo INCIDENT_END; ?> — a total of <strong><?php echo INCIDENT_DURATION; ?></strong>.
                    This outage occurred at the worst possible time: during an active promotional sale, when your
                    customers were actively trying to place orders.
                </p>

                <p>
                    We understand the direct impact this has had on your business — lost revenue, lost customer
                    trust, and the significant effort your team put into planning and promoting this sale event.
                    This is completely unacceptable, and we own that failure entirely.
                </p>

                <p>
                    The root cause was an exhausted database connection pool, triggered by a sudden surge in
                    concurrent traffic from your sale promotion. Our monitoring systems should have caught this
                    before it became critical, and our auto-scaling thresholds should have responded automatically.
                    Neither worked as expected. We have identified the gaps in our infrastructure and processes
                    that allowed this to happen, and we are addressing each one immediately.
                </p>

                <p>
                    We have already implemented emergency fixes overnight and have a structured remediation plan
                    in place to prevent any recurrence. We are also committed to compensating your business fairly
                    for the disruption — your account manager will be in touch within the next business day to
                    discuss this directly.
                </p>

                <p>
                    If any of your customers were unable to complete orders during the outage window, please direct
                    them to the <a href="#claim">Lost Order Claims</a> section on this page. We will assist with
                    recovering or re-processing any affected orders.
                </p>

                <p>
                    We value your trust and your partnership enormously. We know that trust is rebuilt through
                    actions, not words — and we are committed to demonstrating that through everything we do going
                    forward.
                </p>

                <p>Again, we are truly sorry.</p>
            </div>

            <div class="letter-signature">
                <div class="sig-name">The <?php echo COMPANY_NAME; ?> Engineering &amp; Operations Team</div>
                <div class="sig-title"><?php echo date('l, F j, Y'); ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Incident Timeline -->
<section class="page-section" id="incident" style="background: var(--white); padding: 56px 24px;">
    <div class="container">
        <div class="section-label">Incident Timeline</div>
        <h2 class="section-title">What Happened &amp; When</h2>

        <?php if (!empty($updates)): ?>
        <div class="timeline">
            <?php foreach ($updates as $u):
                $slug = strtolower(preg_replace('/[^a-zA-Z]/', '', $u['status_label']));
            ?>
            <div class="timeline-item">
                <div class="timeline-dot <?php echo htmlspecialchars($slug); ?>"></div>
                <div class="timeline-time"><?php echo date('M j, Y — g:i A', strtotime($u['posted_at'])); ?></div>
                <div class="timeline-title">
                    <?php echo htmlspecialchars($u['title']); ?>
                    <span class="status-pill status-<?php echo htmlspecialchars($slug); ?>">
                        <?php echo htmlspecialchars($u['status_label']); ?>
                    </span>
                </div>
                <div class="timeline-body"><?php echo htmlspecialchars($u['message']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color: var(--gray);">Timeline updates will appear here as they are posted.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Root Cause -->
<section class="page-section">
    <div class="container">
        <div class="section-label">Root Cause Analysis</div>
        <h2 class="section-title">Why This Happened</h2>

        <div class="info-box" style="margin-bottom: 24px;">
            <div class="info-box-icon">&#128269;</div>
            <div class="info-box-body">
                <h3>Root Cause: Database Connection Pool Exhaustion</h3>
                <p>
                    The promotional sale generated a traffic surge approximately <strong>8x above the normal peak load</strong>.
                    The database connection pool — which controls how many simultaneous database connections the application
                    can open — reached its hard limit. New requests could not acquire a connection and began timing out,
                    causing the checkout, product catalogue, and cart functions to fail. This cascaded to a full site
                    outage within minutes.
                </p>
            </div>
        </div>

        <div class="info-box" style="background: #fff7ed; border-color: #fed7aa;">
            <div class="info-box-icon">&#9888;</div>
            <div class="info-box-body">
                <h3>Contributing Factors</h3>
                <p>
                    Our automated monitoring alert thresholds were set too conservatively and did not trigger in time.
                    The auto-scaling policy for database read replicas had a 10-minute warm-up delay that made it
                    ineffective for sudden spikes. Additionally, there was no pre-scaling protocol in place ahead of
                    known high-traffic events such as sale campaigns.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Actions Being Taken -->
<section class="page-section" id="actions" style="background: var(--white); padding: 56px 24px;">
    <div class="container">
        <div class="section-label">Remediation Plan</div>
        <h2 class="section-title">Steps We Are Taking to Prevent a Recurrence</h2>

        <div class="steps-grid">
            <div class="step-card done">
                <div class="step-number">1</div>
                <h3>Emergency Connection Pool Increase</h3>
                <p>Tripled the database connection pool limit and optimised connection reuse settings. Applied and live as of <?php echo INCIDENT_END; ?>.</p>
            </div>
            <div class="step-card done">
                <div class="step-number">2</div>
                <h3>Monitoring Alerts Recalibrated</h3>
                <p>Updated alert thresholds to trigger at 60% resource utilisation (down from 85%), giving the team more lead time to respond.</p>
            </div>
            <div class="step-card done">
                <div class="step-number">3</div>
                <h3>Read Replica Auto-Scaling Fixed</h3>
                <p>Reduced the auto-scaling warm-up delay from 10 minutes to under 90 seconds and lowered the scaling trigger threshold.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h3>Pre-Event Scaling Protocol</h3>
                <p>Introducing a mandatory pre-scaling checklist for all high-traffic events. Your team will be able to notify us 48 hours before any sale or campaign.</p>
            </div>
            <div class="step-card">
                <div class="step-number">5</div>
                <h3>Circuit Breaker &amp; Graceful Degradation</h3>
                <p>Implementing a circuit breaker pattern so that if the database becomes overwhelmed, the site shows a maintenance page rather than timing out, preserving SEO and user experience.</p>
            </div>
            <div class="step-card">
                <div class="step-number">6</div>
                <h3>Load Testing Before Major Sales</h3>
                <p>Scheduling a full load test simulating 10x peak traffic one week before any major campaign to validate infrastructure capacity in advance.</p>
            </div>
            <div class="step-card">
                <div class="step-number">7</div>
                <h3>Dedicated Incident Response Runbook</h3>
                <p>Creating a formal incident runbook specific to your store so any on-call engineer can restore service in under 15 minutes, at any hour.</p>
            </div>
            <div class="step-card">
                <div class="step-number">8</div>
                <h3>Monthly Infrastructure Review</h3>
                <p>Beginning monthly capacity reviews with your account team to ensure infrastructure scales ahead of your business growth and planned campaigns.</p>
            </div>
        </div>
    </div>
</section>

<!-- Lost Order Claim Form -->
<section class="page-section" id="claim">
    <div class="container">
        <div class="section-label">Affected Customers</div>
        <h2 class="section-title">Submit a Lost Order Claim</h2>
        <p style="color: var(--gray); margin-bottom: 32px; max-width: 600px; line-height: 1.75;">
            If a customer was unable to complete their order between
            <strong><?php echo INCIDENT_START; ?></strong> and <strong><?php echo INCIDENT_END; ?></strong>
            on <?php echo INCIDENT_DATE; ?>, they can submit a claim below.
            Our team will review every submission and respond within <strong>24 hours</strong>.
        </p>

        <?php if ($formMsg): ?>
        <div class="alert <?php echo $formSuccess ? 'alert-success' : 'alert-error'; ?>">
            <span><?php echo $formSuccess ? '&#10003;' : '&#9888;'; ?></span>
            <div><?php echo $formMsg; ?></div>
        </div>
        <?php endif; ?>

        <?php if (!$formSuccess): ?>
        <div class="form-card">
            <form method="POST" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="cust_name" placeholder="Jane Smith"
                               value="<?php echo htmlspecialchars($_POST['cust_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address <span style="color:var(--danger);">*</span></label>
                        <input type="email" name="cust_email" placeholder="jane@example.com"
                               value="<?php echo htmlspecialchars($_POST['cust_email'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Order Number <span style="color:var(--gray); font-weight:400;">(if available)</span></label>
                        <input type="text" name="order_number" placeholder="#10243"
                               value="<?php echo htmlspecialchars($_POST['order_number'] ?? ''); ?>">
                        <div class="hint">Check your email confirmation or browser history.</div>
                    </div>
                    <div class="form-group">
                        <label>Order Value <span style="color:var(--gray); font-weight:400;">(approximate, optional)</span></label>
                        <input type="number" name="order_amount" placeholder="0.00" step="0.01" min="0"
                               value="<?php echo htmlspecialchars($_POST['order_amount'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Approximate Time You Tried to Order</label>
                    <input type="text" name="order_time" placeholder="e.g. around midnight, 12:30 AM"
                           value="<?php echo htmlspecialchars($_POST['order_time'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Description of What Happened <span style="color:var(--danger);">*</span></label>
                    <textarea name="description" placeholder="Please describe what you were trying to order and what error or issue you encountered..."
                              required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
                    <button type="submit" name="submit_claim" class="btn btn-primary">Submit Claim</button>
                    <span style="font-size:0.8rem; color:var(--gray);">
                        We will respond to <strong>every</strong> claim within 24 hours.
                    </span>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- What's Next -->
<section class="page-section" style="background: var(--white); padding: 56px 24px;">
    <div class="container">
        <div class="section-label">Next Steps</div>
        <h2 class="section-title">What Happens Next</h2>
        <div class="steps-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
            <div class="step-card">
                <div class="step-number" style="background:#fce7f3; color:#9d174d;">24h</div>
                <h3>Your Account Manager Will Call</h3>
                <p>We will reach out personally within one business day to discuss compensation and next steps.</p>
            </div>
            <div class="step-card">
                <div class="step-number" style="background:#ede9fe; color:#5b21b6;">48h</div>
                <h3>Order Claims Reviewed</h3>
                <p>Every lost order claim will be reviewed and we will contact each affected customer directly.</p>
            </div>
            <div class="step-card">
                <div class="step-number" style="background:#ecfdf5; color:#065f46;">7d</div>
                <h3>Full Post-Mortem Delivered</h3>
                <p>A detailed technical post-mortem report will be shared with your team within 7 days.</p>
            </div>
            <div class="step-card">
                <div class="step-number" style="background:#f0fdf4; color:#15803d;">30d</div>
                <h3>Infrastructure Upgrade Complete</h3>
                <p>All 8 remediation steps will be fully implemented and verified within 30 days.</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="site-footer">
    <div class="footer-inner">
        <p><strong style="color:#fff;"><?php echo COMPANY_NAME; ?></strong></p>
        <p>For urgent concerns, contact your account manager directly.</p>
        <p style="margin-top: 16px; font-size:0.8rem; opacity:0.5;">
            Incident Reference: INC-2026-0306 &bull; Report generated <?php echo date('F j, Y'); ?> &bull;
            <a href="admin.php">Admin Panel</a>
        </p>
    </div>
</footer>

</body>
</html>
