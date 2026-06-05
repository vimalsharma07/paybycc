{{-- Homepage: payment methods spotlight with animated cards --}}
<section class="checkout-showcase relative overflow-hidden border-y border-white/5 px-4 py-20 sm:px-6 sm:py-24">
    <div class="checkout-showcase__mesh pointer-events-none absolute inset-0" aria-hidden="true"></div>
    <div class="checkout-showcase__orb checkout-showcase__orb--a pointer-events-none absolute" aria-hidden="true"></div>
    <div class="checkout-showcase__orb checkout-showcase__orb--b pointer-events-none absolute" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-6xl">
        <div class="mx-auto max-w-3xl text-center">
            <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-emerald-500/25 bg-emerald-500/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-300">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></span>
                Licensed checkout
            </p>
            <h2 class="animate-fade-up animate-delay-100 mt-5 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-[2.75rem]">
                Every way your clients pay — <span class="bg-gradient-to-r from-emerald-300 via-cyan-300 to-indigo-300 bg-clip-text text-transparent">one business settlement.</span>
            </h2>
            <p class="animate-fade-up animate-delay-200 mt-4 text-lg leading-relaxed text-slate-400">
                UPI, cards, net banking &amp; wallets through RBI-authorised partners. Your customer picks what feels natural — you get one clear order and one bank payout.
            </p>
        </div>

        <div class="mt-14 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-5">
            @foreach ([
                ['tone' => 'emerald', 'label' => 'UPI', 'desc' => 'Instant for most Indian clients', 'tags' => ['GPay', 'PhonePe', 'Paytm'], 'icon' => 'upi', 'delay' => '100'],
                ['tone' => 'indigo', 'label' => 'Credit card', 'desc' => 'Retainers & milestones', 'tags' => ['Visa', 'MC', 'RuPay'], 'icon' => 'card', 'delay' => '180'],
                ['tone' => 'violet', 'label' => 'Debit card', 'desc' => 'Familiar bank checkout', 'tags' => ['All banks'], 'icon' => 'debit', 'delay' => '260'],
                ['tone' => 'cyan', 'label' => 'Net banking', 'desc' => 'Trusted by businesses', 'tags' => ['50+ banks'], 'icon' => 'bank', 'delay' => '340'],
                ['tone' => 'fuchsia', 'label' => 'Wallets', 'desc' => 'Where enabled on gateway', 'tags' => ['Paytm & more'], 'icon' => 'wallet', 'delay' => '420'],
            ] as $card)
                <article class="checkout-card checkout-card--{{ $card['tone'] }} animate-fade-up animate-delay-{{ $card['delay'] }} group col-span-1 {{ $loop->last && $loop->count % 2 !== 0 ? 'max-md:col-span-2 md:max-xl:col-span-1' : '' }}">
                    <div class="checkout-card__shine" aria-hidden="true"></div>
                    <div class="checkout-card__icon" aria-hidden="true">
                        @include('partials.checkout-method-icon', ['name' => $card['icon']])
                    </div>
                    <h3 class="checkout-card__title">{{ $card['label'] }}</h3>
                    <p class="checkout-card__desc">{{ $card['desc'] }}</p>
                    <div class="checkout-card__tags">
                        @foreach ($card['tags'] as $tag)
                            <span class="checkout-card__tag">{{ $tag }}</span>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>

        <div class="animate-fade-up animate-delay-400 checkout-flow mx-auto mt-12 max-w-3xl">
            <p class="mb-4 text-center text-xs font-bold uppercase tracking-widest text-slate-500">How checkout feels</p>
            <div class="checkout-flow__track">
                <div class="checkout-flow__step checkout-flow__step--active">
                    <span class="checkout-flow__dot"></span>
                    <span class="checkout-flow__label">Client pays</span>
                </div>
                <div class="checkout-flow__line checkout-flow__line--animate" aria-hidden="true"></div>
                <div class="checkout-flow__step">
                    <span class="checkout-flow__dot"></span>
                    <span class="checkout-flow__label">Licensed gateway</span>
                </div>
                <div class="checkout-flow__line checkout-flow__line--animate checkout-flow__line--delay" aria-hidden="true"></div>
                <div class="checkout-flow__step">
                    <span class="checkout-flow__dot"></span>
                    <span class="checkout-flow__label">Your bank</span>
                </div>
            </div>
        </div>

        <p class="animate-fade-up animate-delay-400 mt-10 text-center text-sm text-slate-500">
            Powered by <span class="font-semibold text-slate-400">RBI-authorised payment gateway partners</span> · Secure · KYC-ready · <a href="{{ route('terms') }}" class="text-indigo-400 hover:text-white">Terms</a> · <a href="{{ route('privacy') }}" class="text-indigo-400 hover:text-white">Privacy</a>
        </p>
    </div>
</section>
