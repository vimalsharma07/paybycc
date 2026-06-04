@extends('layouts.marketing')

@section('title', 'Privacy policy — '.$siteSettings->displayName())
@section('meta_description', 'How '.$siteSettings->displayName().' collects and protects data for customers, freelancers, orders, and card payments.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">Legal</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Privacy policy</h1>
        <p class="mt-4 text-sm text-slate-500">Last updated: {{ date('F j, Y') }}</p>

        <div class="mt-12 scroll-mt-24 space-y-10 text-slate-400 leading-relaxed">
            <p>{{ $siteSettings->displayName() }} (“we”, “us”) respects your privacy. This policy explains what we collect on our <strong class="text-slate-300">freelancer &amp; service marketplace</strong>, why we use it, and your choices. It applies where this policy is linked.</p>

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
                    <li><strong class="text-slate-200">Orders &amp; payments:</strong> order amounts, fees, tax fields, status, gateway references, card payment metadata from processors (we do not store full card numbers on our servers when the gateway tokenizes them).</li>
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
                    <li>Process <strong class="text-slate-200">card and other payments</strong> via partners.</li>
                    <li>Comply with law (tax, AML, regulatory requests).</li>
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
                <h2 class="text-xl font-semibold text-white">7. Your rights</h2>
                <p class="mt-4">You may have rights to access, correct, delete, or restrict processing depending on your jurisdiction. Contact us to exercise them; we may verify identity first.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">8. Cookies &amp; sessions</h2>
                <p class="mt-4">Essential cookies support login, CSRF protection, and session management. Disabling them may prevent sign-in.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">9. Children</h2>
                <p class="mt-4">The Platform is not directed at children under 16. We do not knowingly collect their data.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">10. International transfers</h2>
                <p class="mt-4">If data crosses borders, we use safeguards required by applicable law.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">11. Changes</h2>
                <p class="mt-4">We may update this policy; the “Last updated” date will change. Material changes may receive additional notice.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">12. Contact</h2>
                <p class="mt-4"><a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact us</a>.</p>
            </section>
        </div>
    </article>
@endsection
