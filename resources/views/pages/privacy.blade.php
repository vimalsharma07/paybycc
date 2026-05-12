@extends('layouts.marketing')

@section('title', 'Privacy policy — '.$siteSettings->displayName())
@section('meta_description', 'How '.$siteSettings->displayName().' collects, uses, stores, and protects your personal information.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">Legal</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Privacy policy</h1>
        <p class="mt-4 text-sm text-slate-500">Last updated: {{ date('F j, Y') }}</p>

        <div class="mt-12 scroll-mt-24 space-y-10 text-slate-400 leading-relaxed">
            <p>{{ $siteSettings->displayName() }} (“we”, “us”, “our”) respects your privacy. This policy explains what we collect, why we collect it, how long we keep it, and the choices you have. It applies to our website and related services where this policy is linked.</p>

            <section>
            <h2 class="text-xl font-semibold text-white">1. Who we are</h2>
            @if (filled($siteSettings->address))
                <p class="mt-4">The operator of {{ $siteSettings->displayName() }} is registered at the following address:</p>
                <p class="mt-2 whitespace-pre-line rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-200">{{ $siteSettings->address }}</p>
            @else
                <p class="mt-4">The operator of {{ $siteSettings->displayName() }} is the entity managing this deployment. Add your registered business name and address in <strong class="text-slate-300">Admin → Website</strong> so they appear here automatically.</p>
            @endif
            <p class="mt-4">For privacy requests, use the <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact</a> page@if ($siteSettings->support_email) or email <a href="mailto:{{ $siteSettings->support_email }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">{{ $siteSettings->support_email }}</a>@endif.</p>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">2. Information we collect</h2>
            <p class="mt-4">Depending on how you use the service, we may process:</p>
            <ul class="mt-4 list-disc space-y-2 pl-5">
                <li><strong class="text-slate-200">Account &amp; profile:</strong> name, email, phone, user identifier (e.g. user code), authentication secrets (stored hashed), and optional identity fields collected for KYC where applicable.</li>
                <li><strong class="text-slate-200">Payment data:</strong> transaction amounts, currency, status, gateway references, provider payloads needed to complete or reconcile payments, and timestamps.</li>
                <li><strong class="text-slate-200">Bank account details:</strong> bank name, account holder, masked or full account identifiers, IFSC, and flags such as primary account — submitted by you for payouts/settlement flows.</li>
                <li><strong class="text-slate-200">Gateway credentials (administrative):</strong> keys or secrets you configure for payment gateways — stored encrypted at rest where implemented.</li>
                <li><strong class="text-slate-200">Technical data:</strong> IP address, device/browser signals, cookies or similar technologies associated with sessions (see §8).</li>
                <li><strong class="text-slate-200">Communications:</strong> messages you send via contact forms or support channels.</li>
            </ul>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">3. Legal bases &amp; purposes (summary)</h2>
            <p class="mt-4">We process personal data to:</p>
            <ul class="mt-4 list-disc space-y-2 pl-5">
                <li>Provide, operate, and secure the platform (contract / legitimate interests).</li>
                <li>Authenticate users, prevent fraud and abuse, comply with financial/KYC obligations where applicable (legal obligation / legitimate interests).</li>
                <li>Process payments and maintain settlement records (contract).</li>
                <li>Respond to inquiries (contract / legitimate interests).</li>
                <li>Improve reliability and safety of the service (legitimate interests), including logs with appropriate retention.</li>
            </ul>
            <p class="mt-4">Where consent is required for optional processing (e.g. certain marketing communications), we will ask separately.</p>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">4. Sharing of information</h2>
            <p class="mt-4">We do not sell your personal information. We may share data with:</p>
            <ul class="mt-4 list-disc space-y-2 pl-5">
                <li><strong class="text-slate-200">Payment partners:</strong> acquiring banks, payment gateways, or processors necessary to authorize and settle transactions.</li>
                <li><strong class="text-slate-200">Infrastructure providers:</strong> hosting, email delivery, logging — under contractual safeguards.</li>
                <li><strong class="text-slate-200">Authorities:</strong> when required by applicable law, regulation, legal process, or to protect rights and safety.</li>
                <li><strong class="text-slate-200">Professional advisers:</strong> auditors or lawyers where permitted.</li>
            </ul>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">5. Retention</h2>
            <p class="mt-4">We retain information only as long as necessary for the purposes above, including legal, regulatory, tax, and dispute-resolution requirements. Backup copies may persist for a limited period consistent with those needs.</p>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">6. Security</h2>
            <p class="mt-4">We implement administrative, technical, and organizational measures appropriate to the risk — including encryption for sensitive fields where configured (e.g. gateway secrets), access controls, and secure transport (HTTPS). No method of transmission or storage is 100% secure; we strive to follow industry practices.</p>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">7. Your rights</h2>
            <p class="mt-4">Depending on your jurisdiction, you may have rights to access, correction, deletion, restriction, portability, or objection. To exercise rights, contact us via the <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact</a> page. We may need to verify your identity. You may also lodge a complaint with your local supervisory authority.</p>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">8. Cookies &amp; sessions</h2>
            <p class="mt-4">We use cookies or similar technologies necessary for authentication (e.g. session cookies), CSRF protection, and preferences. You can control cookies through browser settings; disabling essential cookies may affect login.</p>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">9. Children</h2>
            <p class="mt-4">The service is not directed at children under 16 (or the minimum age in your region). We do not knowingly collect personal information from children.</p>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">10. International transfers</h2>
            <p class="mt-4">If data is processed across borders, we implement suitable safeguards as required by law (e.g. standard contractual clauses where applicable).</p>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">11. Changes</h2>
            <p class="mt-4">We may update this policy from time to time. Material changes will be indicated by updating the “Last updated” date and, where appropriate, additional notice.</p>
            </section>

            <section>
            <h2 class="text-xl font-semibold text-white">12. Contact</h2>
            <p class="mt-4">Questions about privacy? Reach us through <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact us</a>.</p>
            </section>
        </div>
    </article>
@endsection
