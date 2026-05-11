{{-- Tailwind via CDN — no Node/npm required. Custom animations & auth form styles: public/css/custom.css --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700" rel="stylesheet" />
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Plus Jakarta Sans', 'Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                },
            },
        },
    };
</script>
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
