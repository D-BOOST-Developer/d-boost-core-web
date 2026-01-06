<?php

namespace App\Services\NLP;

class KnowledgeBase
{
    /**
     * FAQ Knowledge Base
     */
    private array $faqDatabase = [
        // Onboarding FAQs
        'onboarding.setup_store' => [
            'question' => 'Bagaimana cara membuat toko?',
            'answer' => 'Untuk membuat toko, ikuti langkah berikut:
1. Klik menu "Dashboard" → "Setup Toko"
2. Isi informasi toko: nama, deskripsi, alamat
3. Pilih kategori bisnis
4. Klik "Simpan"
Toko Anda sudah siap digunakan!',
            'keywords' => ['buat toko', 'setup toko', 'mulai bisnis'],
            'category' => 'onboarding',
        ],
        'onboarding.add_first_product' => [
            'question' => 'Bagaimana cara menambah produk pertama?',
            'answer' => 'Untuk menambah produk pertama:
1. Klik menu "Inventory" → "Produk"
2. Klik tombol "+ Tambah Produk"
3. Isi detail produk:
   - Nama produk
   - Kategori
   - Harga jual
   - Harga modal (opsional)
   - Stok awal
   - Deskripsi
4. Upload foto produk (opsional)
5. Klik "Simpan"
Produk Anda sudah tersimpan!',
            'keywords' => ['tambah produk', 'produk pertama', 'input produk'],
            'category' => 'onboarding',
        ],
        'onboarding.connect_marketplace' => [
            'question' => 'Bagaimana cara menghubungkan marketplace?',
            'answer' => 'Untuk menghubungkan marketplace:
1. Klik menu "Settings" → "Integrasi Marketplace"
2. Pilih marketplace (Tokopedia, Shopee, Lazada, dll)
3. Klik "Hubungkan"
4. Login dengan akun marketplace Anda
5. Izinkan akses
Marketplace sudah terhubung! Produk Anda akan sinkron otomatis.',
            'keywords' => ['pasang marketplace', 'hubungkan marketplace', 'integrasi'],
            'category' => 'onboarding',
        ],

        // Inventory FAQs
        'inventory.check_stock' => [
            'question' => 'Bagaimana cara mengecek stok produk?',
            'answer' => 'Untuk mengecek stok produk:
1. Klik menu "Inventory" → "Produk"
2. Lihat kolom "Stok" di daftar produk
3. Atau klik nama produk untuk melihat detail stok
Anda juga bisa menggunakan fitur pencarian untuk menemukan produk tertentu.',
            'keywords' => ['cek stok', 'berapa stok', 'lihat stok'],
            'category' => 'inventory',
        ],
        'inventory.update_stock' => [
            'question' => 'Bagaimana cara mengupdate stok?',
            'answer' => 'Untuk mengupdate stok produk:
1. Klik menu "Inventory" → "Produk"
2. Cari produk yang ingin diupdate
3. Klik tombol "Edit" atau nama produk
4. Ubah nilai stok di kolom "Stok"
5. Klik "Simpan"
Stok akan terupdate secara otomatis di semua marketplace yang terhubung.',
            'keywords' => ['update stok', 'ubah stok', 'tambah stok', 'kurangi stok'],
            'category' => 'inventory',
        ],
        'inventory.low_stock_alert' => [
            'question' => 'Bagaimana cara mengatur alert stok rendah?',
            'answer' => 'Untuk mengatur alert stok rendah:
1. Klik menu "Settings" → "Pengaturan Stok"
2. Atur "Minimum Stok" untuk setiap produk
3. Sistem akan memberikan notifikasi jika stok mencapai minimum
Anda juga bisa melihat daftar produk stok rendah di Dashboard.',
            'keywords' => ['stok rendah', 'alert stok', 'minimum stok'],
            'category' => 'inventory',
        ],
        'inventory.restock_reminder' => [
            'question' => 'Bagaimana cara membuat reminder restock?',
            'answer' => 'Untuk membuat reminder restock:
1. Klik menu "Inventory" → "Restock"
2. Klik "+ Buat Reminder Restock"
3. Pilih produk yang ingin direstock
4. Atur jumlah dan tanggal restock
5. Klik "Simpan"
Sistem akan mengingatkan Anda pada tanggal yang ditentukan.',
            'keywords' => ['restock', 'pesan ulang', 'reminder restock'],
            'category' => 'inventory',
        ],

        // Finance FAQs
        'finance.log_expense' => [
            'question' => 'Bagaimana cara mencatat pengeluaran?',
            'answer' => 'Untuk mencatat pengeluaran:
1. Klik menu "Finance" → "Pengeluaran"
2. Klik "+ Catat Pengeluaran"
3. Isi detail:
   - Kategori pengeluaran (supplies, sewa, gaji, dll)
   - Jumlah
   - Tanggal
   - Keterangan
4. Upload bukti (opsional)
5. Klik "Simpan"
Pengeluaran akan tercatat dan mempengaruhi laporan laba rugi.',
            'keywords' => ['catat pengeluaran', 'input pengeluaran', 'expense'],
            'category' => 'finance',
        ],
        'finance.check_revenue' => [
            'question' => 'Bagaimana cara melihat pendapatan?',
            'answer' => 'Untuk melihat pendapatan:
1. Klik menu "Dashboard" → "Statistik Penjualan"
2. Lihat kartu "Total Penjualan" untuk ringkasan
3. Atau klik menu "Finance" → "Laporan Penjualan"
4. Pilih periode (hari, minggu, bulan, tahun)
5. Lihat detail pendapatan per produk atau marketplace
Anda juga bisa export laporan dalam format Excel atau PDF.',
            'keywords' => ['lihat pendapatan', 'berapa pendapatan', 'total penjualan'],
            'category' => 'finance',
        ],
        'finance.calculate_profit_loss' => [
            'question' => 'Bagaimana cara menghitung laba rugi?',
            'answer' => 'Untuk melihat laporan laba rugi:
1. Klik menu "Finance" → "Laporan Laba Rugi"
2. Pilih periode (bulan, tahun)
3. Sistem akan menampilkan:
   - Total Penjualan
   - Total Pengeluaran
   - Laba Bersih
4. Klik "Detail" untuk melihat breakdown
Laporan ini membantu Anda memahami kesehatan finansial bisnis.',
            'keywords' => ['laba rugi', 'keuntungan', 'profit loss', 'untung rugi'],
            'category' => 'finance',
        ],
        'finance.export_report' => [
            'question' => 'Bagaimana cara export laporan?',
            'answer' => 'Untuk export laporan:
1. Buka laporan yang ingin diexport (Penjualan, Pengeluaran, Laba Rugi)
2. Klik tombol "Export" di atas tabel
3. Pilih format: Excel (.xlsx) atau PDF
4. Laporan akan diunduh ke komputer Anda
Anda bisa membuka file dengan Excel atau Adobe Reader.',
            'keywords' => ['export laporan', 'download laporan', 'cetak laporan'],
            'category' => 'finance',
        ],

        // Analytics FAQs
        'analytics.daily_sales' => [
            'question' => 'Bagaimana cara melihat penjualan hari ini?',
            'answer' => 'Untuk melihat penjualan hari ini:
1. Buka Dashboard (halaman utama)
2. Lihat kartu "Penjualan Hari Ini" di bagian atas
3. Lihat jumlah transaksi dan total omset
4. Klik kartu untuk melihat detail transaksi
Anda juga bisa melihat grafik penjualan per jam di bawahnya.',
            'keywords' => ['penjualan hari ini', 'sales hari ini', 'omset hari ini'],
            'category' => 'analytics',
        ],
        'analytics.top_products' => [
            'question' => 'Bagaimana cara melihat produk terlaris?',
            'answer' => 'Untuk melihat produk terlaris:
1. Buka Dashboard
2. Lihat bagian "Produk Terlaris"
3. Sistem menampilkan 5 produk dengan penjualan terbanyak
4. Lihat jumlah terjual dan total revenue per produk
5. Klik produk untuk melihat detail penjualan
Informasi ini membantu Anda fokus pada produk yang menguntungkan.',
            'keywords' => ['produk terlaris', 'best seller', 'produk paling laku'],
            'category' => 'analytics',
        ],
        'analytics.weekly_trend' => [
            'question' => 'Bagaimana cara melihat trend mingguan?',
            'answer' => 'Untuk melihat trend mingguan:
1. Buka Dashboard
2. Lihat grafik "Trend Penjualan 7 Hari"
3. Grafik menampilkan penjualan per hari dalam 7 hari terakhir
4. Hover di atas grafik untuk melihat detail
5. Klik "Lihat Detail" untuk laporan lengkap
Anda bisa melihat hari mana yang paling ramai penjualan.',
            'keywords' => ['trend mingguan', 'trend minggu', 'grafik minggu'],
            'category' => 'analytics',
        ],

        // CRM FAQs
        'crm.search_customer' => [
            'question' => 'Bagaimana cara mencari data pelanggan?',
            'answer' => 'Untuk mencari data pelanggan:
1. Klik menu "Customer" → "Daftar Pelanggan"
2. Gunakan kolom pencarian untuk cari nama atau nomor telepon
3. Atau filter berdasarkan kota, gender, atau status
4. Klik nama pelanggan untuk melihat detail
5. Lihat riwayat pembelian dan catatan follow-up
Anda juga bisa export daftar pelanggan dalam format Excel.',
            'keywords' => ['cari pelanggan', 'data pelanggan', 'info pelanggan'],
            'category' => 'crm',
        ],
        'crm.send_broadcast' => [
            'question' => 'Bagaimana cara mengirim broadcast ke pelanggan?',
            'answer' => 'Untuk mengirim broadcast:
1. Klik menu "Customer" → "Broadcast"
2. Klik "+ Buat Broadcast"
3. Pilih target pelanggan (semua, berdasarkan filter)
4. Tulis pesan
5. Pilih channel (WhatsApp, SMS, Email)
6. Klik "Preview" untuk melihat hasil
7. Klik "Kirim" untuk mengirim
Anda bisa melihat statistik pengiriman di halaman broadcast.',
            'keywords' => ['kirim broadcast', 'broadcast pesan', 'notifikasi pelanggan'],
            'category' => 'crm',
        ],
        'crm.add_followup' => [
            'question' => 'Bagaimana cara membuat catatan follow-up?',
            'answer' => 'Untuk membuat catatan follow-up:
1. Buka detail pelanggan
2. Klik tab "Catatan & Follow-up"
3. Klik "+ Tambah Catatan"
4. Tulis catatan tentang pelanggan
5. Atur tanggal follow-up (opsional)
6. Klik "Simpan"
Sistem akan mengingatkan Anda pada tanggal follow-up.',
            'keywords' => ['catatan follow-up', 'follow up', 'reminder pelanggan'],
            'category' => 'crm',
        ],

        // General Help
        'help.dashboard_overview' => [
            'question' => 'Apa saja yang bisa saya lihat di Dashboard?',
            'answer' => 'Di Dashboard Anda bisa melihat:
1. **KPI Cards**: Total penjualan, pelanggan, produk
2. **Grafik Penjualan**: Trend 30 hari terakhir
3. **Produk Terlaris**: 5 produk dengan penjualan terbanyak
4. **Insight Pelanggan**: Demografi dan perilaku
5. **Daftar Penjualan Terbaru**: Transaksi terakhir
6. **Alert Stok Rendah**: Produk yang perlu direstock
Dashboard memberikan ringkasan lengkap kesehatan bisnis Anda.',
            'keywords' => ['dashboard', 'overview', 'ringkasan'],
            'category' => 'help',
        ],
        'help.user_roles' => [
            'question' => 'Apa perbedaan role Admin, Owner, dan Staff?',
            'answer' => '**Admin**: Akses penuh ke semua fitur termasuk audit logs dan settings
**Owner**: Akses ke semua fungsi bisnis (inventory, finance, customer)
**Staff**: Akses terbatas untuk operasional harian (lihat stok, catat penjualan)
Anda bisa mengatur role di menu Settings → User Management.',
            'keywords' => ['role', 'admin', 'owner', 'staff', 'permission'],
            'category' => 'help',
        ],
        'help.export_data' => [
            'question' => 'Bagaimana cara export data?',
            'answer' => 'Untuk export data:
1. Buka halaman yang ingin diexport (Produk, Penjualan, Pelanggan, dll)
2. Klik tombol "Export" di atas tabel
3. Pilih format: Excel (.xlsx) atau CSV
4. File akan diunduh ke komputer Anda
Anda bisa membuka file dengan Excel atau Google Sheets.',
            'keywords' => ['export', 'download', 'excel', 'csv'],
            'category' => 'help',
        ],
    ];

    /**
     * Search FAQ by query
     */
    public function searchFAQ(string $query): array
    {
        $query = strtolower($query);
        $results = [];

        foreach ($this->faqDatabase as $key => $faq) {
            $score = 0;

            // Check question
            if (strpos(strtolower($faq['question']), $query) !== false) {
                $score += 10;
            }

            // Check keywords
            foreach ($faq['keywords'] as $keyword) {
                if (strpos($query, $keyword) !== false || strpos($keyword, $query) !== false) {
                    $score += 5;
                }
            }

            // Check answer
            if (strpos(strtolower($faq['answer']), $query) !== false) {
                $score += 2;
            }

            if ($score > 0) {
                $results[$key] = [
                    'score' => $score,
                    'faq' => $faq,
                ];
            }
        }

        // Sort by score
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, 5, true);
    }

    /**
     * Get FAQ by intent
     */
    public function getFAQByIntent(string $intent): ?array
    {
        return $this->faqDatabase[$intent] ?? null;
    }

    /**
     * Get all FAQs by category
     */
    public function getFAQsByCategory(string $category): array
    {
        return array_filter($this->faqDatabase, fn($faq) => $faq['category'] === $category);
    }

    /**
     * Get tutorial steps for onboarding
     */
    public function getOnboardingTutorial(): array
    {
        return [
            [
                'step' => 1,
                'title' => 'Buat Toko',
                'description' => 'Mulai dengan membuat profil toko Anda',
                'action' => 'setup_store',
                'faq_key' => 'onboarding.setup_store',
            ],
            [
                'step' => 2,
                'title' => 'Tambah Produk Pertama',
                'description' => 'Daftarkan produk yang ingin Anda jual',
                'action' => 'add_first_product',
                'faq_key' => 'onboarding.add_first_product',
            ],
            [
                'step' => 3,
                'title' => 'Atur Harga',
                'description' => 'Tentukan harga jual produk Anda',
                'action' => 'set_price',
                'faq_key' => 'onboarding.set_price',
            ],
            [
                'step' => 4,
                'title' => 'Input Stok',
                'description' => 'Masukkan jumlah stok awal',
                'action' => 'input_stock',
                'faq_key' => 'onboarding.input_stock',
            ],
            [
                'step' => 5,
                'title' => 'Hubungkan Marketplace',
                'description' => 'Integrasikan dengan marketplace favorit Anda',
                'action' => 'connect_marketplace',
                'faq_key' => 'onboarding.connect_marketplace',
            ],
        ];
    }

    /**
     * Get quick tips
     */
    public function getQuickTips(): array
    {
        return [
            'Periksa stok produk setiap hari untuk menghindari kehabisan stok.',
            'Gunakan fitur broadcast untuk memberitahu pelanggan tentang produk baru.',
            'Monitor laporan laba rugi setiap minggu untuk memahami kesehatan bisnis.',
            'Manfaatkan data pelanggan untuk strategi marketing yang lebih baik.',
            'Backup data Anda secara berkala di menu Settings.',
        ];
    }
}
