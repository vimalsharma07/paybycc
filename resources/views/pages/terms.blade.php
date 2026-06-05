@extends('layouts.marketing')

@section('title', 'Terms & conditions — '.$siteSettings->displayName())
@section('meta_description', 'Terms of use for '.$siteSettings->displayName().' — business payments, licensed gateways, KYC, GST/TDS, settlements & acceptable use under Indian law.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">Legal</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Terms &amp; conditions</h1>
        <p class="mt-4 text-sm text-slate-500">Last updated: {{ date('F j, Y') }}</p>

        <div class="mt-12 scroll-mt-24 space-y-10 text-slate-400 leading-relaxed">
            <p>These Terms &amp; Conditions (“Terms”) govern your use of {{ $siteSettings->displayName() }} (“Platform”, “we”, “us”). The Platform enables <strong class="text-slate-300">business owners, freelancers, service providers, startups, and customers</strong> to place orders and accept or make <strong class="text-slate-300">online business payments</strong> through licensed payment partners, subject to identity verification and applicable Indian law. By registering or using the Platform, you agree to these Terms and our <a href="{{ route('privacy') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Privacy Policy</a>.</p>

            <section class="rounded-2xl border border-indigo-500/25 bg-indigo-500/10 p-6">
                <h2 class="text-lg font-semibold text-white">Summary (not a substitute for the full Terms)</h2>
                <ul class="mt-3 list-disc space-y-2 pl-5 text-sm">
                    <li>Payments are processed via <strong class="text-slate-200">RBI-authorised payment gateways</strong> — not informal personal transfers for platform orders.</li>
                    <li><strong class="text-slate-200">Seller KYC (PAN/identity)</strong> is required before bank settlements.</li>
                    <li>Tax fields (GST, TDS, TCS) may apply as shown at checkout; you remain responsible for your statutory filings.</li>
                    <li>Work contracts are between buyer and seller; we provide payment rails, records, and compliance tooling.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">1. Roles</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li><strong class="text-slate-200">Customer / buyer:</strong> searches for freelancers, places orders, and pays through the Platform.</li>
                    <li><strong class="text-slate-200">Seller / business provider:</strong> freelancer, agency, startup, or service business that lists offerings, fulfils orders, and receives settlements after applicable checks.</li>
                    <li><strong class="text-slate-200">Admin:</strong> operates the Platform (not a party to your contract with another user unless stated).</li>
                </ul>
                <p class="mt-4">Contracts for work are between customer and seller. We provide payment, record-keeping, and compliance tooling — not guaranteed outcomes of freelance work unless expressly agreed in writing.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">2. Eligibility &amp; accounts</h2>
                <p class="mt-4">You must be able to enter a binding contract in your jurisdiction and provide accurate information. You are responsible for account security and all activity under your credentials. Notify us promptly of unauthorized use via <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact</a>.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">3. KYC &amp; verification</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li><strong class="text-slate-200">Seller KYC</strong> is mandatory before settlements to a linked bank account.</li>
                    <li><strong class="text-slate-200">Customer KYC</strong> may be required depending on seller settings or Platform rules.</li>
                    <li>You must submit truthful documents. We may suspend access if verification fails or law requires.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">4. Services catalogue</h2>
                <p class="mt-4">Services and sub-services are managed on the Platform. Sellers select from approved categories; new categories may be submitted for admin approval. We may edit, reject, or remove listings that violate policy or law.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">5. Orders</h2>
                <p class="mt-4">An order records the commercial terms between customer and seller (amounts, service, status). Order states may include created, pending, accepted, in progress, completed, cancelled, or disputed. Disputes should be raised promptly via support; we may facilitate but are not obliged to arbitrate every disagreement.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">6. Payments</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li>Payments are processed only through <strong class="text-slate-200">RBI-authorised payment gateway partners</strong> integrated with the Platform. Supported methods may include <strong class="text-slate-200">UPI, credit and debit cards, net banking, wallets,</strong> and other options enabled on the gateway merchant account. We do not store full card numbers when the gateway tokenises them.</li>
                    <li><strong class="text-slate-200">Payment links</strong> created by sellers encode a fixed amount and route payers through the same licensed checkout flow.</li>
                    <li>Payment status (pending, authorized, paid, failed, refunded, etc.) is shown in the product where available.</li>
                    <li>You authorize us and partners to charge the selected method for confirmed orders and applicable fees disclosed before confirmation.</li>
                    <li>Chargebacks and card-network rules may affect settlements; you agree to cooperate with reasonable fraud and dispute investigations.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">7. Fees, GST, TDS &amp; TCS</h2>
                <p class="mt-4">Platform fees, GST on fees where applicable, TDS, TCS, and net settlement amounts are calculated and displayed as shown at checkout or in seller reports, in line with prevailing Indian tax and Platform rules. <strong class="text-slate-200">Sellers and buyers remain responsible</strong> for their own income-tax, GST registration, invoicing, and statutory filings. We do not provide tax advice.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">8. Settlements &amp; Safe Status</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li>Settlements to sellers occur only when payment is successful, seller KYC is complete, and <strong class="text-slate-200">Safe Status</strong> (risk review) permits release — e.g. safe, not hold or rejected.</li>
                    <li>Timing depends on banks, card networks, and partners; estimates are indicative.</li>
                    <li>We may hold or reverse settlements for fraud, disputes, chargebacks, or legal requirements.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">9. Regulatory compliance</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li>You agree to comply with applicable laws including <strong class="text-slate-200">RBI payment-system norms</strong>, <strong class="text-slate-200">PMLA/AML</strong> requirements, <strong class="text-slate-200">Income Tax</strong> and <strong class="text-slate-200">GST</strong> obligations, and the <strong class="text-slate-200">Information Technology Act, 2000</strong> (and rules thereunder) as applicable.</li>
                    <li>We may request information, delay settlements, or report activity to authorities when required by law or reasonable risk controls.</li>
                    <li>Use of the Platform does not make us your tax agent, legal advisor, or guarantor of any commercial outcome between users.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">10. Acceptable use</h2>
                <p class="mt-4">You will not use the Platform for fraud, illegal services, money laundering, harassment, circumventing security, or misrepresenting identity. We may suspend or terminate access and report activity to authorities where required.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">11. Intellectual property</h2>
                <p class="mt-4">Platform branding, software, and content (excluding user content) remain our property or licensors’. You grant us a license to host content you submit for operating the marketplace.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">12. Third-party services</h2>
                <p class="mt-4">Gateways, banks, SMS, and infrastructure providers have their own terms. Outages beyond our reasonable control are not our liability.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">13. Disclaimers</h2>
                <p class="mt-4">The Platform is provided “as is” and “as available” to the extent permitted by law. We do not guarantee uninterrupted service or that every seller will complete work to your satisfaction.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">14. Limitation of liability</h2>
                <p class="mt-4">To the maximum extent permitted, we are not liable for indirect, incidental, special, consequential, or punitive damages. Aggregate liability for Platform-related claims may be capped at fees you paid us in the prior three months (if any) or INR 5,000 — whichever is greater — unless mandatory law requires more.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">15. Indemnity</h2>
                <p class="mt-4">You indemnify us against claims arising from your misuse, your transactions with other users, or violation of these Terms — except where caused by our willful misconduct.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">16. Suspension &amp; termination</h2>
                <p class="mt-4">We may suspend accounts for risk, legal, or operational reasons. You may stop using the Platform anytime. Surviving clauses (liability, indemnity, disputes) continue where applicable.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">17. Governing law &amp; disputes</h2>
                <p class="mt-4">These Terms are governed by the <strong class="text-slate-200">laws of India</strong>, including applicable consumer-protection rules. Courts in India shall have jurisdiction unless mandatory law provides otherwise. You may also raise grievances via <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact</a> before pursuing legal remedies where appropriate.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">18. Changes</h2>
                <p class="mt-4">We may update these Terms; material payment or settlement changes will be highlighted where feasible. Continued use after notice may constitute acceptance where permitted.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">19. Contact</h2>
                <p class="mt-4"><a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact us</a> with questions.</p>
            </section>
        </div>
    </article>
@endsection
