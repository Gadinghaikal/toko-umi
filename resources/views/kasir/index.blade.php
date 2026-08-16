<x-app-layout>
    <x-slot name="title">Kasir POS</x-slot>
    <x-slot name="pageTitle">Kasir (Point of Sales)</x-slot>

    <!-- Hide header on POS for larger workspace -->
    @push('styles')
    <style>
        .page-header { display: none !important; }
        .pos-layout { 
            height: calc(100vh - 70px); 
            margin-top: -1.5rem; 
            margin-inline: -1.5rem; 
            background: #f8f9fa; 
            display: flex;
            overflow: hidden;
        }
        .pos-left { 
            height: 100%; 
            overflow-y: auto; 
            padding: 1.5rem; 
            flex: 1;
        }
        .pos-right { 
            width: 400px;
            min-width: 400px;
            height: 100%; 
            display: flex; 
            flex-direction: column; 
            background: #fff; 
            box-shadow: -2px 0 10px rgba(0,0,0,0.05); 
            z-index: 10; 
        }
        .product-card { 
            cursor: pointer; 
            transition: transform 0.2s, box-shadow 0.2s; 
            border: 1px solid #e9ecef; 
        }
        .product-card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            border-color: #0d6efd; 
        }
        .cart-header {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            background: #fff;
        }
        .cart-items { 
            flex-grow: 1; 
            overflow-y: auto; 
            padding: 1rem; 
            background: #fdfdfd;
        }
        .cart-item { 
            border-bottom: 1px dashed #e9ecef; 
            padding-bottom: 0.75rem; 
            margin-bottom: 0.75rem; 
        }
        .cart-summary { 
            padding: 1rem; 
            background: #fff; 
            border-top: 1px solid #e9ecef; 
            box-shadow: 0 -4px 10px rgba(0,0,0,0.02);
        }
        
        /* Custom scrollbar */
        .pos-left::-webkit-scrollbar, .cart-items::-webkit-scrollbar { width: 6px; }
        .pos-left::-webkit-scrollbar-track, .cart-items::-webkit-scrollbar-track { background: transparent; }
        .pos-left::-webkit-scrollbar-thumb, .cart-items::-webkit-scrollbar-thumb { background-color: #ced4da; border-radius: 10px; }
        
        @media (max-width: 991.98px) {
            .pos-layout { flex-direction: column; height: auto; overflow: visible; }
            .pos-left { flex: none; height: auto; }
            .pos-right { width: 100%; min-width: 100%; height: auto; border-top: 2px solid #e9ecef; box-shadow: none; }
            .cart-items { max-height: 400px; }
        }
    </style>
    @endpush

    <div class="pos-layout">
        <!-- Area Produk (Kiri) -->
        <div class="pos-left">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0"><i class="bi bi-shop me-2 text-primary"></i>TOKO UMI - Kasir</h4>
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-house me-1"></i> Dashboard
                </a>
            </div>

            <!-- Filter & Search -->
            <div class="row g-2 mb-4">
                <div class="col-12 col-md-4">
                    <select class="form-select border-0 shadow-sm" id="posCategoryFilter">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-8">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-0" id="posSearch" placeholder="Cari nama atau kode barang..." autocomplete="off">
                    </div>
                </div>
            </div>

            <!-- Grid Produk -->
            <div class="row g-3" id="productGrid">
                @forelse($products as $product)
                    <div class="col-6 col-md-4 col-xl-3 product-item-wrapper">
                        <div class="card h-100 product-card rounded-3 product-item" 
                             data-id="{{ $product->id }}" 
                             data-name="{{ $product->name }}" 
                             data-code="{{ $product->code }}" 
                             data-price="{{ $product->selling_price }}" 
                             data-stock="{{ $product->stock }}"
                             data-category="{{ $product->category_id }}">
                            <div class="card-body p-3 d-flex flex-column text-center user-select-none">
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark font-monospace mb-2 border">{{ $product->code }}</span>
                                </div>
                                <h6 class="card-title fw-bold mb-1 flex-grow-1" style="font-size: 0.9rem;">{{ $product->name }}</h6>
                                <div class="text-success fw-bold mb-2">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                                <div class="small border-top pt-2 {{ $product->stock <= $product->min_stock ? 'text-danger fw-bold' : 'text-muted' }}">
                                    Sisa Stok: {{ $product->stock }} {{ $product->unit }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Tidak ada produk aktif dengan stok tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Area Keranjang (Kanan) -->
        <div class="pos-right">
            <div class="cart-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-cart3 me-2 text-primary"></i>Keranjang</h5>
                <button type="button" class="btn btn-sm btn-outline-danger" id="btnClearCart" title="Kosongkan Keranjang">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
            <div class="cart-items" id="cartContainer">
                <div class="text-center text-muted py-5 mt-5" id="emptyCartMsg">
                    <i class="bi bi-cart-x fs-1 mb-2 d-block text-black-50"></i>
                    Keranjang masih kosong.<br>Klik barang di sebelah kiri untuk menambah.
                </div>
                <!-- Cart items dirender via JS -->
            </div>

            <div class="cart-summary">
                <form action="{{ route('kasir.transaction.store') }}" method="POST" id="formTransaction">
                    @csrf
                    <input type="hidden" name="cart" id="inputCartPayload">
                    <input type="hidden" name="total_amount" id="inputTotalAmount">

                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" name="customer_name" placeholder="Nama Pelanggan (Opsional - Default: Umum)">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-600">Total Item</span>
                        <span class="fw-bold small" id="txtTotalItems">0</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fs-6 fw-bold">Grand Total</span>
                        <span class="fs-5 fw-bold text-primary" id="txtGrandTotal">Rp 0</span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">Pembayaran</label>
                            <select class="form-select form-select-sm" name="payment_method" id="paymentMethod" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        <div class="col-6" id="cashInputGroup">
                            <label class="form-label small text-muted mb-1">Jml Bayar <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white">Rp</span>
                                <input type="number" class="form-control fw-bold" name="paid_amount" id="paidAmount" required min="0">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-2 mb-3 bg-light rounded border" id="changeGroup">
                        <span class="text-muted fw-600 small">Kembalian</span>
                        <span class="fs-6 fw-bold text-success" id="txtChange">Rp 0</span>
                    </div>

                    <button type="button" class="btn btn-action w-100 py-2 fw-bold shadow-sm text-white" id="btnProcessTx" disabled>
                        <i class="bi bi-check-circle me-1"></i> Proses Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script type="module">
        $(document).ready(function() {
            // State
            let cart = {}; // Format: { "id": { id, name, price, qty, stock, maxStock } }
            let grandTotal = 0;
            let totalItems = 0;

            // DOM Elements
            const $cartContainer = $('#cartContainer');
            const $emptyMsg = $('#emptyCartMsg');
            const $txtGrandTotal = $('#txtGrandTotal');
            const $txtTotalItems = $('#txtTotalItems');
            const $inputCartPayload = $('#inputCartPayload');
            const $inputTotalAmount = $('#inputTotalAmount');
            const $btnProcess = $('#btnProcessTx');
            const $paidAmount = $('#paidAmount');
            const $txtChange = $('#txtChange');
            const $paymentMethod = $('#paymentMethod');
            const $cashInputGroup = $('#cashInputGroup');
            const $changeGroup = $('#changeGroup');
            const $formTransaction = $('#formTransaction');

            // Format Rupiah
            const formatRp = (num) => 'Rp ' + parseInt(num).toLocaleString('id-ID');

            // Event Delegation untuk Add Product to Cart
            $(document).on('click', '.product-item', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = parseInt($(this).data('price'));
                const maxStock = parseInt($(this).data('stock'));

                if (!cart[id]) {
                    cart[id] = { id, name, price, qty: 1, maxStock };
                } else {
                    if (cart[id].qty < maxStock) {
                        cart[id].qty++;
                    } else {
                        alert(`Stok maksimal untuk ${name} adalah ${maxStock}`);
                    }
                }

                renderCart();
            });

            // Render Cart
            function renderCart() {
                $cartContainer.find('.cart-item').remove(); // Hapus item lama, biarkan emptyMsg
                grandTotal = 0;
                totalItems = 0;
                let cartArray = [];

                if (Object.keys(cart).length === 0) {
                    $emptyMsg.show();
                    $btnProcess.prop('disabled', true);
                } else {
                    $emptyMsg.hide();
                    $btnProcess.prop('disabled', false);

                    for (const id in cart) {
                        const item = cart[id];
                        const subtotal = item.price * item.qty;
                        grandTotal += subtotal;
                        totalItems += item.qty;
                        cartArray.push(item);

                        const html = `
                            <div class="cart-item">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="fw-600" style="font-size: 0.85rem; line-height: 1.2;">${item.name}</div>
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2 btn-remove" data-id="${item.id}" title="Hapus"><i class="bi bi-x-circle-fill"></i></button>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">${formatRp(item.price)}</div>
                                    
                                    <div class="input-group input-group-sm mx-2" style="width: 100px;">
                                        <button class="btn btn-outline-secondary btn-minus" data-id="${item.id}" type="button">-</button>
                                        <input type="text" class="form-control text-center px-0 fw-bold" value="${item.qty}" readonly>
                                        <button class="btn btn-outline-secondary btn-plus" data-id="${item.id}" type="button">+</button>
                                    </div>
                                    
                                    <div class="fw-bold text-end" style="width: 80px; font-size: 0.9rem;">${formatRp(subtotal)}</div>
                                </div>
                            </div>
                        `;
                        $cartContainer.append(html);
                    }
                }

                // Scroll to bottom of cart
                $cartContainer.scrollTop($cartContainer[0].scrollHeight);

                // Update UI Summary
                $txtGrandTotal.text(formatRp(grandTotal));
                $txtTotalItems.text(totalItems);
                $inputTotalAmount.val(grandTotal);
                
                // Set payload untuk disubmit
                $inputCartPayload.val(JSON.stringify(cartArray));

                // Trigger calculate change
                calculateChange();
            }

            // Event Delegation untuk tombol dalam keranjang
            $cartContainer.on('click', '.btn-plus', function(e) {
                e.stopPropagation();
                const id = $(this).data('id');
                if (cart[id].qty < cart[id].maxStock) {
                    cart[id].qty++;
                    renderCart();
                } else {
                    alert('Stok tidak mencukupi!');
                }
            });

            $cartContainer.on('click', '.btn-minus', function(e) {
                e.stopPropagation();
                const id = $(this).data('id');
                if (cart[id].qty > 1) {
                    cart[id].qty--;
                    renderCart();
                } else {
                    delete cart[id];
                    renderCart();
                }
            });

            $cartContainer.on('click', '.btn-remove', function(e) {
                e.stopPropagation();
                const id = $(this).data('id');
                delete cart[id];
                renderCart();
            });

            // Kosongkan Keranjang
            $('#btnClearCart').on('click', function() {
                if (Object.keys(cart).length > 0 && confirm('Anda yakin ingin mengosongkan keranjang belanja?')) {
                    cart = {};
                    $paidAmount.val('');
                    renderCart();
                }
            });

            // Live Search & Filter
            $('#posSearch, #posCategoryFilter').on('input change', function() {
                const keyword = $('#posSearch').val().toLowerCase();
                const category = $('#posCategoryFilter').val();

                $('.product-item-wrapper').each(function() {
                    const $item = $(this).find('.product-item');
                    const name = $item.data('name').toLowerCase();
                    const code = $item.data('code').toString().toLowerCase();
                    const catId = $item.data('category').toString();

                    const matchKeyword = name.includes(keyword) || code.includes(keyword);
                    const matchCategory = (category === 'all' || category === catId);

                    if (matchKeyword && matchCategory) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Handle Payment Method & Change Calculation
            function calculateChange() {
                const method = $paymentMethod.val();
                
                if (method === 'tunai') {
                    const paid = parseInt($paidAmount.val()) || 0;
                    const change = paid - grandTotal;
                    
                    if (change >= 0) {
                        $txtChange.text(formatRp(change)).removeClass('text-danger').addClass('text-success');
                    } else {
                        $txtChange.text('Kurang: ' + formatRp(Math.abs(change))).removeClass('text-success').addClass('text-danger');
                    }
                } else {
                    // Jika Non-cash (Transfer/QRIS), otomatis set paidAmount sama dengan grandTotal
                    $paidAmount.val(grandTotal);
                    $txtChange.text(formatRp(0)).removeClass('text-danger').addClass('text-success');
                }
            }

            // Realtime hitung kembalian saat uang diketik
            $paidAmount.on('input', calculateChange);

            // Ganti metode pembayaran
            $paymentMethod.on('change', function() {
                const method = $(this).val();
                if (method === 'tunai') {
                    $paidAmount.prop('readonly', false).val('');
                    $paidAmount.focus();
                } else {
                    $paidAmount.prop('readonly', true).val(grandTotal);
                }
                calculateChange();
            });

            // Proses Transaksi
            $btnProcess.on('click', function() {
                const method = $paymentMethod.val();
                const paid = parseInt($paidAmount.val()) || 0;

                // Validasi keranjang
                if (Object.keys(cart).length === 0) {
                    alert('Keranjang belanja kosong!');
                    return;
                }

                // Validasi jumlah uang
                if (method === 'tunai' && paid < grandTotal) {
                    alert('Nominal uang pembayaran kurang dari total belanja!');
                    $paidAmount.focus();
                    return;
                }

                if (confirm(`Apakah Anda yakin ingin memproses transaksi ini dengan Total ${formatRp(grandTotal)}?`)) {
                    $formTransaction.submit();
                    // Disable tombol untuk mencegah double submit
                    $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...');
                }
            });

        });
    </script>
    @endpush
</x-app-layout>
