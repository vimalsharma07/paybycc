@extends('layouts.marketing')

@section('title', 'Terms & conditions — '.config('app.name'))
@section('meta_description', 'Terms of use for '.config('app.name').' — eligibility, payments, liability, and acceptable use.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">Legal</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Terms &amp; conditions</h1>
        <p class="mt-4 text-sm text-slate-500">Last updated: {{ date('F j, Y') }}</p>

        <div class="mt-12 scroll-mt-24 space-y-10 text-slate-400 leading-relaxed">
            <p>These Terms govern access to and use of {{ config('app.name') }} (“Service”). By creating an account or using the Service, you agree to these Terms and our <a href="{{ route('privacy') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Privacy Policy</a>.</p>

            <section>
                <h2 class="text-xl font-semibold text-white">1. Eligibility</h2>
                <p class="mt-4">You must be legally able to enter a binding contract in your jurisdiction. You must provide accurate registration information and keep it updated. Business users remain responsible for authorized users on their accounts.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">2. Accounts &amp; security</h2>
                <p class="mt-4">You are responsible for safeguarding credentials and for activity under your account. Notify us promptly of unauthorized use via <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact</a>.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">3. KYC &amp; verification</h2>
                <p class="mt-4">Certain features (including payments) may require identity verification. You agree to submit truthful documents/information. We may suspend access if verification fails or regulations require.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">4. Payments &amp; settlements</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li>Payment availability depends on configured gateways and operational status.</li>
                    <li>Amount limits (min/max/daily) may apply per gateway configuration.</li>
                    <li>Settlement timing depends on banks, card networks, and partners — estimates shown in the product are indicative, not guarantees.</li>
                    <li>Wallet balances and payout preferences are tools for managing funds; actual transfers depend on banking rails and successful processing.</li>
                    <li>Fees (if any) will be disclosed before confirmation where required.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">5. Acceptable use</h2>
                <p class="mt-4">You will not misuse the Service — including fraud, unlawful activity, interfering with systems, scraping beyond permitted means, or attempting to bypass security. We may suspend or terminate access for violations.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">6. Intellectual property</h2>
                <p class="mt-4">{{ config('app.name') }} and its branding, UI, and content (excluding user-supplied materials) are protected. You receive a limited, revocable license to use the Service as intended.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">7. Third-party services</h2>
                <p class="mt-4">Gateways, banks, and infrastructure providers have their own terms. Our Service integrates with them as configured; we are not responsible for third-party outages beyond reasonable control.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">8. Disclaimers</h2>
                <p class="mt-4">The Service is provided “as is” and “as available” to the extent permitted by law. We disclaim implied warranties where allowed. Nothing here excludes liability that cannot be excluded under applicable law.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">9. Limitation of liability</h2>
                <p class="mt-4">To the maximum extent permitted, {{ config('app.name') }} and its affiliates will not be liable for indirect, incidental, special, consequential, or punitive damages, or loss of profits/data. Aggregate liability for claims relating to the Service may be capped at amounts you paid us in fees for the Service in the prior three months (if any), or INR 5,000 — whichever is greater — unless a higher minimum applies under mandatory law.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">10. Indemnity</h2>
                <p class="mt-4">You agree to indemnify and hold harmless {{ config('app.name') }} against claims arising from your misuse of the Service, violation of these Terms, or infringement of third-party rights — except to the extent caused by our willful misconduct.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">11. Suspension &amp; termination</h2>
                <p class="mt-4">We may suspend or terminate access for risk, legal, or operational reasons. You may stop using the Service at any time. Surviving provisions (e.g. liability limits, indemnity) continue where applicable.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">12. Governing law &amp; disputes</h2>
                <p class="mt-4">These Terms are governed by the laws of India, unless mandatory consumer protections in your region say otherwise. Replace “insert applicable venue” with your chosen courts when you incorporate — subject to mandatory arbitration rules if you adopt them later.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">13. Changes</h2>
                <p class="mt-4">We may modify these Terms; continued use after notice constitutes acceptance where permitted. Material payment-related changes should be highlighted in-product where feasible.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">14. Contact</h2>
                <p class="mt-4">Questions? <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact us</a>.</p>
            </section>
        </div>
    </article>
@endsection
