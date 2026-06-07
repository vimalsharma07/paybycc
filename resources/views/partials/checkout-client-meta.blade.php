<input type="hidden" name="checkout_meta" value="" data-checkout-meta>
@once
    @push('scripts')
        <script src="{{ asset('js/checkout-client-meta.js') }}?v={{ file_exists(public_path('js/checkout-client-meta.js')) ? filemtime(public_path('js/checkout-client-meta.js')) : 1 }}" defer></script>
    @endpush
@endonce
