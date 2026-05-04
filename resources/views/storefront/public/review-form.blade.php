<x-storefront.public-layout :config="$config" pageTitle="Tulis Ulasan" navMode="solid">

    @push('head')
        <style>
            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            body>main {
                flex: 1 0 auto;
            }

            body>footer {
                flex-shrink: 0;
            }
        </style>
    @endpush

    {{-- Header --}}
    <section class="relative bg-[var(--ink)] text-[var(--paper)] overflow-hidden mb-12">
        {{-- Decorative rings --}}
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none">
            <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute top-10 right-10 w-[300px] h-[300px] rounded-full border border-[var(--paper)]"></div>
            <div class="absolute -bottom-40 left-1/3 w-[400px] h-[400px] rounded-full border border-[var(--paper)]">
            </div>
        </div>

        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 py-20 md:py-28 text-center">
            <p class="text-[10px] uppercase tracking-[0.4em] text-[var(--paper)]/50 mb-5">
                ○ &nbsp; Pengalaman Anda &nbsp; ○
            </p>
            <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl leading-[1.05]">
                Bagikan <em class="text-[var(--paper)]/70">Cerita</em> Anda
            </h1>
            <p class="max-w-md mx-auto text-sm text-[var(--paper)]/55 leading-relaxed mt-6">
                Berbagi pengalaman bersama {{ $config->storefront_title }}.
            </p>
        </div>
    </section>

    <div class="max-w-2xl mx-auto mb-16">
        <div class="bg-white border border-[var(--light)] p-8 sm:p-10">
            <form id="reviewForm" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-2">
                        Nama Anda <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="client_name" required placeholder="Nama lengkap"
                        class="input-field w-full px-4 py-3 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-2">
                        Email <span class="text-[var(--mid)]">(opsional)</span>
                    </label>
                    <input type="email" name="client_email" placeholder="email@anda.com"
                        class="input-field w-full px-4 py-3 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-3">
                        Penilaian <span class="text-red-600">*</span>
                    </label>
                    <div class="flex items-center gap-4">
                        <div class="flex gap-2" id="ratingStars">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating({{ $i }})" data-rating="{{ $i }}"
                                    class="text-4xl text-[var(--light)] hover:text-[var(--ink)] transition-colors rating-star">
                                    ★
                                </button>
                            @endfor
                        </div>
                        <span id="ratingText" class="text-sm font-serif italic text-[var(--ink)]">Sangat Baik</span>
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" value="5">
                </div>

                <div>
                    <label class="block text-[10px] uppercase tracking-[0.24em] text-[var(--mid)] mb-2">
                        Ulasan Anda <span class="text-red-600">*</span>
                    </label>
                    <textarea name="review_text" required rows="6" placeholder="Ceritakan pengalaman Anda…"
                        class="input-field w-full px-4 py-3 bg-[var(--paper)] border border-[var(--light)] text-sm focus:border-[var(--ink)] focus:outline-none resize-none"></textarea>
                    <p class="text-[10px] uppercase tracking-[0.18em] text-[var(--mid)] mt-2">
                        Ulasan Anda akan tampil setelah disetujui admin.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit" id="submitBtn"
                        class="flex-1 bg-[var(--ink)] text-[var(--paper)] text-xs uppercase tracking-[0.2em] font-medium py-4 hover:opacity-90 transition-opacity">
                        Kirim Ulasan
                    </button>
                    <a href="{{ route('public.store.show', $config->slug) }}"
                        class="border border-[var(--ink)] text-[var(--ink)] text-xs uppercase tracking-[0.2em] font-medium px-6 py-4 text-center hover:bg-[var(--ink)] hover:text-[var(--paper)] transition-colors">
                        Batal
                    </a>
                </div>
            </form>

            <div id="reviewMessage" class="mt-6 p-4 border hidden"></div>
        </div>

        <p class="text-center mt-6">
            <a href="{{ route('public.store.show', $config->slug) }}"
                class="text-xs uppercase tracking-[0.2em] text-[var(--mid)] hover:text-[var(--ink)] transition-colors">
                ← Kembali ke {{ $config->storefront_title }}
            </a>
        </p>
    </div>

    @push('scripts')
        <script>
            const ratingLabels = ['Kurang', 'Cukup', 'Baik', 'Sangat Baik', 'Luar Biasa'];

            function setRating(rating) {
                document.getElementById('ratingInput').value = rating;
                document.getElementById('ratingText').textContent = ratingLabels[rating - 1];
                const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#0a0a0a';
                const lightColor  = getComputedStyle(document.documentElement).getPropertyValue('--light').trim()  || '#e8e6e1';
                document.querySelectorAll('.rating-star').forEach((star, index) => {
                    star.style.color = index < rating ? accentColor : lightColor;
                });
            }
            setRating(5);

            document.getElementById('reviewForm').addEventListener('submit', async function (e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                const messageEl = document.getElementById('reviewMessage');
                const submitBtn = document.getElementById('submitBtn');

                submitBtn.disabled = true;
                submitBtn.textContent = 'Mengirim…';

                try {
                    const response = await fetch('/store/{{ $config->slug }}/review', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            client_name: formData.get('client_name'),
                            client_email: formData.get('client_email'),
                            review_text: formData.get('review_text'),
                            rating: parseInt(formData.get('rating'))
                        })
                    });
                    const data = await response.json();

                    messageEl.classList.remove('hidden');
                    if (data.success) {
                        messageEl.className = 'mt-6 p-4 border border-emerald-300 bg-emerald-50 text-emerald-800';
                        messageEl.innerHTML = '<p class="text-sm font-medium">Terima kasih atas ulasan Anda!</p><p class="text-xs mt-1">Ulasan akan tampil setelah disetujui.</p>';
                        form.reset();
                        setRating(5);
                        setTimeout(() => {
                            window.location.href = '{{ route('public.store.show', $config->slug) }}#reviews';
                        }, 2500);
                    } else {
                        messageEl.className = 'mt-6 p-4 border border-red-300 bg-red-50 text-red-800 text-sm';
                        messageEl.textContent = data.message || 'Gagal mengirim ulasan. Coba lagi.';
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Kirim Ulasan';
                    }
                } catch (err) {
                    messageEl.classList.remove('hidden');
                    messageEl.className = 'mt-6 p-4 border border-red-300 bg-red-50 text-red-800 text-sm';
                    messageEl.textContent = 'Terjadi kesalahan. Coba lagi.';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Kirim Ulasan';
                }
            });
        </script>
    @endpush

</x-storefront.public-layout>