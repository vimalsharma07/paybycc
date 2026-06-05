@extends('layouts.marketing')

@section('title', 'Terms & conditions — '.$siteSettings->displayName())
@section('meta_description', 'Terms of use for '.$siteSettings->displayName().' — marketplace roles, orders, UPI & card payments, settlements, and acceptable use.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">Legal</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Terms &amp; conditions</h1>
        <p class="mt-4 text-sm text-slate-500">Last updated: {{ date('F j, Y') }}</p>

        <div class="mt-12 scroll-mt-24 space-y-10 text-slate-400 leading-relaxed">
            <p>These Terms govern your use of {{ $siteSettings->displayName() }} (“Platform”, “we”, “us”). The Platform is a <strong class="text-slate-300">freelancer and service marketplace</strong> where customers can discover sellers, place orders, and pay; sellers can offer services and receive settlements subject to verification and compliance. By registering or using the Platform, you agree to these Terms and our <a href="{{ route('privacy') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Privacy Policy</a>.</p>

            <section>
                <h2 class="text-xl font-semibold text-white">1. Roles</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5">
                    <li><strong class="text-slate-200">Customer / buyer:</strong> searches for freelancers, places orders, and pays through the Platform.</li>
                    <li><strong class="text-slate-200">Seller / freelancer:</strong> lists services, fulfils orders, and receives settlements after applicable checks.</li>
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
                    <li>Payments are processed through licensed <strong class="text-slate-200">payment gateways</strong>. Supported methods may include <strong class="text-slate-200">UPI, credit and debit cards, net banking, wallets,</strong> and other options enabled on the gateway and your merchant account.</li>
                    <li>Payment status (pending, authorized, paid, failed, refunded, etc.) is shown in the product where available.</li>
                    <li>You authorize us and partners to charge the selected method for confirmed orders and applicable fees disclosed before confirmation.</li>
                    <li>Chargebacks and card-network rules may affect settlements; you agree to cooperate with reasonable fraud and dispute investigations.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">7. Fees, GST, TDS &amp; TCS</h2>
                <p class="mt-4">Platform fees, GST, TDS, TCS, and net settlement amounts may apply as shown at checkout or in seller reports. Tax treatment depends on transaction facts and law; sellers remain responsible for their statutory obligations unless we explicitly state otherwise.</p>
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
                <h2 class="text-xl font-semibold text-white">9. Acceptable use</h2>
                <p class="mt-4">You will not use the Platform for fraud, illegal services, money laundering, harassment, circumventing security, or misrepresenting identity. We may suspend or terminate access and report activity to authorities where required.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">10. Intellectual property</h2>
                <p class="mt-4">Platform branding, software, and content (excluding user content) remain our property or licensors’. You grant us a license to host content you submit for operating the marketplace.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">11. Third-party services</h2>
                <p class="mt-4">Gateways, banks, SMS, and infrastructure providers have their own terms. Outages beyond our reasonable control are not our liability.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">12. Disclaimers</h2>
                <p class="mt-4">The Platform is provided “as is” and “as available” to the extent permitted by law. We do not guarantee uninterrupted service or that every seller will complete work to your satisfaction.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">13. Limitation of liability</h2>
                <p class="mt-4">To the maximum extent permitted, we are not liable for indirect, incidental, special, consequential, or punitive damages. Aggregate liability for Platform-related claims may be capped at fees you paid us in the prior three months (if any) or INR 5,000 — whichever is greater — unless mandatory law requires more.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">14. Indemnity</h2>
                <p class="mt-4">You indemnify us against claims arising from your misuse, your transactions with other users, or violation of these Terms — except where caused by our willful misconduct.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">15. Suspension &amp; termination</h2>
                <p class="mt-4">We may suspend accounts for risk, legal, or operational reasons. You may stop using the Platform anytime. Surviving clauses (liability, indemnity, disputes) continue where applicable.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">16. Governing law</h2>
                <p class="mt-4">These Terms are governed by the laws of India, subject to mandatory consumer protections in your region. Courts in India shall have jurisdiction unless otherwise required by law.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">17. Changes</h2>
                <p class="mt-4">We may update these Terms; material payment or settlement changes will be highlighted where feasible. Continued use after notice may constitute acceptance where permitted.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-white">18. Contact</h2>
                <p class="mt-4"><a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact us</a> with questions.</p>
            </section>
        </div>
    </article>
@endsection
