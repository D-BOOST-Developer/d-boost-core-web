{{--
    Payment Info Partial
    Required vars:
      $paymentMethod  — e.g. 'bank_transfer', 'qris', 'cash', 'e_wallet', etc.
      $businessProfile — UserBusinessProfile|null
      $whatsappNumber — formatted WA number string (e.g. '628123456789')
      $invoiceNumber  — order invoice for WA message (optional)
      $totalAmount    — order total (optional)
--}}
@php
    $pm      = $paymentMethod ?? 'cash';
    $bp      = $businessProfile ?? null;

    // Build WhatsApp link
    $rawPhone = preg_replace('/\D/', '', $bp?->contact_phone ?? '');
    if (str_starts_with($rawPhone, '0')) {
        $rawPhone = '62' . substr($rawPhone, 1);
    }
    $waLink = $rawPhone
        ? 'https://wa.me/' . $rawPhone . '?text=' . rawurlencode(
            'Halo, saya ingin konfirmasi pembayaran pesanan saya.' .
            (isset($invoiceNumber) ? ' No. Invoice: ' . $invoiceNumber : '') .
            (isset($totalAmount)   ? ' Total: Rp ' . number_format($totalAmount, 0, ',', '.') : '')
          )
        : null;

    $banks       = \App\Models\UserBusinessProfile::getIndonesianBanks();
    $bankLabel   = $banks[$bp?->bank_name ?? ''] ?? ($bp?->bank_name ?? '');
    $qrisUrl     = $bp?->qris_image_url ?? null;
@endphp

@if($pm === 'bank_transfer')
    {{-- ── Bank Transfer ─────────────────────────────────── --}}
    <div class="mt-6 border border-[var(--light)] bg-[var(--paper)] p-5">
        <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-4">Detail Transfer Bank</p>
        @if($bp && $bp->bank_name && $bp->bank_account_number)
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-[0.2em] text-[var(--mid)]">Bank</span>
                    <span class="font-medium text-[var(--ink)] text-sm">{{ $bankLabel }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-[0.2em] text-[var(--mid)]">No. Rekening</span>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[var(--ink)] text-sm tracking-wider" id="accNumber-{{ $invoiceNumber ?? 'co' }}">{{ $bp->bank_account_number }}</span>
                        <button type="button"
                                onclick="navigator.clipboard.writeText('{{ $bp->bank_account_number }}').then(()=>{ const el=this; el.textContent='✓'; setTimeout(()=>el.textContent='⧉',1500) })"
                                class="text-[10px] uppercase tracking-[0.2em] text-[var(--mid)] hover:text-[var(--ink)] transition-colors px-2 py-0.5 border border-[var(--light)]">⧉</button>
                    </div>
                </div>
                @if($bp->bank_account_holder)
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase tracking-[0.2em] text-[var(--mid)]">Atas Nama</span>
                        <span class="text-[var(--ink)] text-sm">{{ $bp->bank_account_holder }}</span>
                    </div>
                @endif
            </div>
            @if($waLink)
                <a href="{{ $waLink }}" target="_blank" rel="noopener"
                   class="mt-5 flex items-center justify-center gap-2 bg-emerald-600 text-white text-xs uppercase tracking-[0.2em] font-medium py-3 hover:bg-emerald-700 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Konfirmasi Pembayaran via WhatsApp
                </a>
            @endif
        @else
            <p class="text-sm text-[var(--mid)] italic">Info rekening belum tersedia. Hubungi penjual via WhatsApp.</p>
            @if($waLink)
                <a href="{{ $waLink }}" target="_blank" rel="noopener"
                   class="mt-4 flex items-center justify-center gap-2 bg-emerald-600 text-white text-xs uppercase tracking-[0.2em] font-medium py-3 hover:bg-emerald-700 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Hubungi Penjual via WhatsApp
                </a>
            @endif
        @endif
    </div>

@elseif($pm === 'qris')
    {{-- ── QRIS ────────────────────────────────────────────── --}}
    <div class="mt-6 border border-[var(--light)] bg-[var(--paper)] p-5">
        <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-4">Scan QRIS untuk Pembayaran</p>
        @if($qrisUrl)
            <div class="flex flex-col items-center gap-4">
                <div class="border-4 border-[var(--ink)] p-2 inline-block">
                    <img src="{{ $qrisUrl }}" alt="QRIS {{ $config->storefront_title }}"
                         class="w-56 h-56 object-contain block">
                </div>
                <p class="text-xs text-[var(--mid)] text-center leading-relaxed max-w-xs">
                    Scan kode QRIS di atas menggunakan aplikasi dompet digital atau mobile banking mana pun.
                </p>
            </div>
        @else
            <p class="text-sm text-[var(--mid)] italic">Kode QRIS belum tersedia. Hubungi penjual.</p>
        @endif
        @if($waLink)
            <a href="{{ $waLink }}" target="_blank" rel="noopener"
               class="mt-5 flex items-center justify-center gap-2 bg-emerald-600 text-white text-xs uppercase tracking-[0.2em] font-medium py-3 hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Konfirmasi Sudah Bayar via WhatsApp
            </a>
        @endif
    </div>

@elseif(in_array($pm, ['cash', 'e_wallet', 'debit_card', 'credit_card']))
    {{-- ── Cash / COD / E-Wallet / Kartu ──────────────────── --}}
    <div class="mt-6 border border-[var(--light)] bg-[var(--paper)] p-5">
        <p class="text-[10px] uppercase tracking-[0.32em] text-[var(--mid)] mb-3">Konfirmasi Pembayaran</p>
        <p class="text-sm text-[var(--ink)] leading-relaxed mb-4">
            @if($pm === 'cash')
                Pembayaran dilakukan saat barang diterima (Cash on Delivery). Penjual akan menghubungi Anda untuk koordinasi pengiriman.
            @elseif($pm === 'e_wallet')
                Detail nomor e-wallet akan dikonfirmasi oleh penjual via WhatsApp.
            @else
                Detail pembayaran akan dikonfirmasi oleh penjual via WhatsApp.
            @endif
        </p>
        @if($waLink)
            <a href="{{ $waLink }}" target="_blank" rel="noopener"
               class="flex items-center justify-center gap-2 bg-emerald-600 text-white text-xs uppercase tracking-[0.2em] font-medium py-3 hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Hubungi Penjual via WhatsApp
            </a>
        @endif
    </div>
@endif
