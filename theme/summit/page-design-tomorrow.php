<?php
/**
 * Design Tomorrow Page Template
 * Auto-used by WordPress for the page with slug: design-tomorrow
 *
 * Primary CTA destination for the site. Contains a contact form
 * that creates an enquiry CPT post and sends an email notification.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main site-main--design-tomorrow" role="main">

	<!-- ── Hero ────────────────────────────────────────────────────────── -->
	<section class="dt-hero" aria-labelledby="dt-hero-title">
		<div class="container container--medium">
			<header class="dt-hero__header">
				<h1 class="dt-hero__title" id="dt-hero-title" data-reveal>
					Let's Design Tomorrow
				</h1>
				<p class="dt-hero__intro" data-reveal data-reveal-delay="1">
					We partner with ambitious brands to sharpen positioning, elevate creative expression and build digital experiences of lasting value. If you have a project in mind, we would like to hear about it.
				</p>
			</header>
		</div>
	</section>

	<!-- ── Contact form ────────────────────────────────────────────────── -->
	<section class="dt-form-section" aria-label="Contact form">
		<div class="container container--narrow">

			<form class="dt-form" id="summit-enquiry-form" method="post" novalidate data-reveal data-reveal-delay="2">

				<?php wp_nonce_field( 'summit_enquiry_submit', 'summit_enquiry_nonce' ); ?>
				<input type="hidden" name="action" value="summit_enquiry">
				<input type="hidden" name="enq_form_source" value="/design-tomorrow">

				<div class="dt-form__field">
					<label for="enq-name" class="dt-form__label">Name</label>
					<input type="text"
					       id="enq-name"
					       name="enq_name"
					       required
					       autocomplete="name"
					       class="dt-form__input"
					       placeholder="Your name">
				</div>

				<div class="dt-form__field">
					<label for="enq-email" class="dt-form__label">Email</label>
					<input type="email"
					       id="enq-email"
					       name="enq_email"
					       required
					       autocomplete="email"
					       class="dt-form__input"
					       placeholder="you@company.com">
				</div>

				<div class="dt-form__field">
					<label for="enq-company" class="dt-form__label">Company</label>
					<input type="text"
					       id="enq-company"
					       name="enq_company"
					       autocomplete="organization"
					       class="dt-form__input"
					       placeholder="Your company or brand">
				</div>

				<div class="dt-form__field">
					<label for="enq-type" class="dt-form__label">How can we help?</label>
					<select id="enq-type" name="enq_type" required class="dt-form__select">
						<option value="" disabled selected>Select an area</option>
						<option value="brand_strategy">Brand Strategy</option>
						<option value="experience_design">Experience Design</option>
						<option value="digital_transformation">Digital Transformation</option>
						<option value="general">General Enquiry</option>
					</select>
				</div>

				<div class="dt-form__field">
					<label for="enq-message" class="dt-form__label">Tell us about your project</label>
					<textarea id="enq-message"
					          name="enq_message"
					          required
					          rows="5"
					          class="dt-form__textarea"
					          placeholder="A brief description of what you're looking to achieve..."></textarea>
				</div>

				<div class="dt-form__actions">
					<button type="submit" class="btn btn--primary dt-form__submit">
						Send Enquiry
					</button>
				</div>

				<div class="dt-form__feedback" aria-live="polite">
					<p class="dt-form__success" hidden>
						Thank you. We've received your enquiry and will be in touch shortly.
					</p>
					<p class="dt-form__error" hidden>
						Something went wrong. Please try again or email us directly.
					</p>
				</div>

			</form>

		</div>
	</section>

	<!-- ── Context strip ───────────────────────────────────────────────── -->
	<section class="dt-context" aria-label="Contact details">
		<div class="container container--narrow">
			<div class="dt-context__inner">
				<p class="dt-context__positioning">
					Independent design consultancy for luxury brands.
				</p>
				<p class="dt-context__location">
					London&nbsp;+44
				</p>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
