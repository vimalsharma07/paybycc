{{-- Homepage: structured payment methods showcase --}}
<section class="checkout-showcase relative overflow-hidden border-y border-white/10 px-4 py-20 sm:px-6 sm:py-24">
    <div class="checkout-showcase__mesh pointer-events-none absolute inset-0" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-6xl">
        {{-- Section header --}}
        <header class="checkout-showcase__header mx-auto max-w-3xl text-center">
            <p class="animate-fade-up checkout-showcase__eyebrow">Licensed checkout</p>
            <h2 class="animate-fade-up animate-delay-100 checkout-showcase__title">
                Every way your clients pay —
                <span class="checkout-showcase__title-accent">one business settlement.</span>
            </h2>
            <p class="animate-fade-up animate-delay-200 checkout-showcase__lead">
                UPI, cards, net banking &amp; wallets through RBI-authorised partners. Your customer picks what feels natural — you get one clear order and one bank payout.
            </p>
        </header>

        {{-- Methods panel --}}
        <div class="animate-fade-up animate-delay-300 checkout-panel checkout-panel--methods mt-12">
            <div class="checkout-panel__bar">
                <div class="checkout-panel__bar-left">
                    <span class="checkout-panel__bar-icon" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </span>
                    <span class="checkout-panel__bar-title">Payment methods at checkout</span>
                </div>
                <span class="checkout-panel__bar-badge">5 channels</span>
            </div>

            <div class="checkout-methods-grid">
                @foreach ([
                    ['num' => '01', 'tone' => 'emerald', 'label' => 'UPI', 'desc' => 'Instant for most Indian clients', 'tags' => ['GPay', 'PhonePe', 'Paytm'], 'icon' => 'upi'],
                    ['num' => '02', 'tone' => 'indigo', 'label' => 'Credit card', 'desc' => 'Retainers & milestones', 'tags' => ['Visa', 'MC', 'RuPay'], 'icon' => 'card'],
                    ['num' => '03', 'tone' => 'violet', 'label' => 'Debit card', 'desc' => 'Familiar bank checkout', 'tags' => ['All banks'], 'icon' => 'debit'],
                    ['num' => '04', 'tone' => 'cyan', 'label' => 'Net banking', 'desc' => 'Trusted by businesses', 'tags' => ['50+ banks'], 'icon' => 'bank'],
                    ['num' => '05', 'tone' => 'fuchsia', 'label' => 'Wallets', 'desc' => 'Where enabled on gateway', 'tags' => ['Paytm & more'], 'icon' => 'wallet'],
                ] as $card)
                    <article class="checkout-card checkout-card--{{ $card['tone'] }}">
                        <div class="checkout-card__stripe" aria-hidden="true"></div>
                        <div class="checkout-card__top">
                            <span class="checkout-card__num">{{ $card['num'] }}</span>
                            <div class="checkout-card__icon" aria-hidden="true">
                                @include('partials.checkout-method-icon', ['name' => $card['icon']])
                            </div>
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
        </div>

        {{-- Settlement flow panel --}}
        <div class="animate-fade-up animate-delay-400 checkout-panel checkout-panel--flow mt-6">
            <div class="checkout-panel__bar checkout-panel__bar--compact">
                <div class="checkout-panel__bar-left">
                    <span class="checkout-panel__bar-icon checkout-panel__bar-icon--flow" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </span>
                    <span class="checkout-panel__bar-title">How checkout feels</span>
                </div>
                <span class="checkout-panel__bar-meta">One order · One payout</span>
            </div>

            <div class="checkout-flow">
                <div class="checkout-flow__step checkout-flow__step--1">
                    <div class="checkout-flow__bubble">1</div>
                    <div class="checkout-flow__copy">
                        <p class="checkout-flow__name">Client pays</p>
                        <p class="checkout-flow__hint">UPI, card or bank</p>
                    </div>
                </div>
                <div class="checkout-flow__connector" aria-hidden="true">
                    <span class="checkout-flow__connector-line"></span>
                    <span class="checkout-flow__connector-arrow">›</span>
                </div>
                <div class="checkout-flow__step checkout-flow__step--2">
                    <div class="checkout-flow__bubble">2</div>
                    <div class="checkout-flow__copy">
                        <p class="checkout-flow__name">Licensed gateway</p>
                        <p class="checkout-flow__hint">RBI-authorised partner</p>
                    </div>
                </div>
                <div class="checkout-flow__connector" aria-hidden="true">
                    <span class="checkout-flow__connector-line"></span>
                    <span class="checkout-flow__connector-arrow">›</span>
                </div>
                <div class="checkout-flow__step checkout-flow__step--3">
                    <div class="checkout-flow__bubble">3</div>
                    <div class="checkout-flow__copy">
                        <p class="checkout-flow__name">Your bank</p>
                        <p class="checkout-flow__hint">Settlement to account</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Trust footer --}}
        <div class="animate-fade-up animate-delay-400 checkout-trust mt-8">
            <span class="checkout-trust__item">RBI-authorised partners</span>
            <span class="checkout-trust__sep" aria-hidden="true"></span>
            <span class="checkout-trust__item">Secure checkout</span>
            <span class="checkout-trust__sep" aria-hidden="true"></span>
            <span class="checkout-trust__item">KYC-ready</span>
            <span class="checkout-trust__sep" aria-hidden="true"></span>
            <a href="{{ route('terms') }}" class="checkout-trust__link">Terms</a>
            <span class="checkout-trust__sep" aria-hidden="true"></span>
            <a href="{{ route('privacy') }}" class="checkout-trust__link">Privacy</a>
        </div>
    </div>
</section>
