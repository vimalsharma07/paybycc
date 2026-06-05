@extends('layouts.marketing')

@section('title', 'Privacy policy — '.$siteSettings->displayName())
@section('meta_description', 'Privacy policy for '.$siteSettings->displayName().' — how we collect, use & protect data for business payments, KYC & settlements under Indian law.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">Legal</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Privacy policy</h1>
        <p class="mt-4 text-sm text-slate-500">Last updated: {{ date('F j, Y') }}</p>

        <div class="mt-12 scroll-mt-24 space-y-10 text-slate-400 leading-relaxed">
            <p>{{ $siteSettings->displayName() }} (“we”, “us”, “Platform”) respects your privacy. This Privacy Policy explains what personal data we collect when <strong class="text-slate-300">business owners, freelancers, service providers, startups, and customers</strong> use our payment and marketplace services, why we use it, how long we keep it, and your rights. It applies wherever this policy is linked, including our website and application.</p>

            <section class="rounded-2xl border border-indigo-500/25 bg-indigo-500/10 p-6">
                <h2 class="text-lg font-semibold text-white">In short</h2>
                <ul class="mt-3 list-disc space-y-2 pl-5 text-sm">
                    <li>We collect only what is needed to run accounts, <strong class="text-slate-200">KYC verification</strong>, orders, and <strong class="text-slate-200">licensed payment processing</strong>.</li>
                    <li>We do <strong class="text-slate-200">not sell</strong> your personal data.</li>
                    <li>Sensitive payment card data is handled by <strong class="text-slate-200">gateway partners</strong>; we do not store full card numbers when tokenised.</li>
                    <li>You may contact us to access, correct, or raise concerns about your data — see Section 12.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">1. Who we are</h2>
                @if (filled($siteSettings->address))
                    <p class="mt-4">The operator of {{ $siteSettings->displayName() }} is registered at:</p>
                    <p class="mt-2 whitespace-pre-line rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-200">{{ $siteSettings->address }}</p>
                @else
                    <p class="mt-4">Add your registered business name and address in <strong class="text-slate-300">Admin → Website</strong> to display them here.</p>
                @endif
                <p class="mt-4">Privacy requests: <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact</a>@if ($siteSettings->support_email) or <a href="mailto:{{ $siteSettings->support_email }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">{{ $siteSettings->support_email }}</a>@endif.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">2. Information we collect</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li><strong class="text-slate-200">Account:</strong> name, email, phone, password (hashed), role (customer/seller), user code.</li>
                    <li><strong class="text-slate-200">Seller profile:</strong> company name, GSTIN (if provided), address, services offered, bank details for settlement.</li>
                    <li><strong class="text-slate-200">KYC:</strong> PAN, Aadhaar or other identity fields where required; verification status.</li>
                    <li><strong class="text-slate-200">Orders &amp; payments:</strong> order amounts, fees, GST/TDS/TCS fields, status, gateway references, payment-link tokens, and payment metadata from licensed processors (we do not store full card numbers when the gateway tokenises them).</li>
                    <li><strong class="text-slate-200">Bank accounts:</strong> bank name, holder name, account number, IFSC for payouts.</li>
                    <li><strong class="text-slate-200">Technical:</strong> IP, device/browser, session cookies, application logs for security and support.</li>
                    <li><strong class="text-slate-200">Communications:</strong> contact form and support messages.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">3. How we use data</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li>Operate the marketplace (search, orders, payments, settlements).</li>
                    <li>Verify identity and manage risk (including Safe Status for settlements).</li>
                    <li>Process <strong class="text-slate-200">UPI, card, net banking, wallet, and other payments</strong> via partners.</li>
                    <li>Comply with applicable Indian law — including <strong class="text-slate-200">tax</strong>, <strong class="text-slate-200">PMLA/AML</strong>, <strong class="text-slate-200">RBI-related payment norms</strong>, and lawful government or court requests.</li>
                    <li>Prevent fraud and improve reliability (including structured audit logs).</li>
                    <li>Respond to support inquiries.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">4. Sharing</h2>
                <p class="mt-4">We do not sell personal information. We may share with:</p>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li>Payment gateways and banks to authorize and settle transactions.</li>
                    <li>KYC or verification partners where used.</li>
                    <li>Hosting, SMS/OTP, email, and infrastructure providers under contract.</li>
                    <li>Authorities when required by law or to protect rights and safety.</li>
                    <li>Other users only as needed for transactions (e.g. seller sees order details relevant to fulfilment).</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">5. Retention</h2>
                <p class="mt-4">We retain data as long as needed for the purposes above, including legal, tax, and dispute requirements. Logs and order records may be kept for compliance periods even after account closure where required.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">6. Security</h2>
                <p class="mt-4">We use HTTPS, access controls, hashing of secrets, and encryption for sensitive configuration where implemented. No system is perfectly secure; report concerns via Contact.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">7. Legal basis &amp; Indian law</h2>
                <p class="mt-4">We process personal data where necessary to perform our contract with you, comply with legal obligations, pursue legitimate interests (fraud prevention, security, service improvement), or with your consent where required. This includes obligations under the <strong class="text-slate-200">Information Technology Act, 2000</strong> (and rules thereunder) and, where applicable, the <strong class="text-slate-200">Digital Personal Data Protection Act, 2023</strong> (DPDP Act) and related guidance as it comes into force.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">8. Your rights</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li>Request <strong class="text-slate-200">access, correction, or deletion</strong> of personal data we control, subject to legal retention needs.</li>
                    <li>Withdraw consent where processing is consent-based (without affecting prior lawful processing).</li>
                    <li>Nominate another individual to exercise rights on your behalf in situations permitted by law.</li>
                    <li>Raise a grievance with us; we will respond within reasonable timelines.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">9. Cookies &amp; sessions</h2>
                <p class="mt-4">Essential cookies support login, CSRF protection, and session management. Disabling them may prevent sign-in.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">10. Children</h2>
                <p class="mt-4">The Platform is not directed at children under 16. We do not knowingly collect their data.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">11. International transfers</h2>
                <p class="mt-4">If data crosses borders, we use safeguards required by applicable law.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">12. Grievance &amp; contact</h2>
                <p class="mt-4">For privacy questions, data requests, or complaints, contact us via <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact</a>@if ($siteSettings->support_email) or email <a href="mailto:{{ $siteSettings->support_email }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">{{ $siteSettings->support_email }}</a>@endif. We will acknowledge and work to resolve grievances within timelines required by applicable law. If you are not satisfied, you may have rights to escalate to the Data Protection Board of India once fully operational under the DPDP Act.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">13. Changes</h2>
                <p class="mt-4">We may update this policy; the “Last updated” date will change. Material changes may receive additional notice.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">14. Related policies</h2>
                <p class="mt-4">Please also read our <a href="{{ route('terms') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Terms &amp; Conditions</a>, which govern use of the Platform alongside this Privacy Policy.</p>
            </section>
        </div>
    </article>
@endsection
