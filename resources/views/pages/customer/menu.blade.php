<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan di Kios Pak Kumis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2c3e50;
            --warning: #f39c12;
            --success: #27ae60;
        }
        body {
            background-color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header-custom {
            background-color: var(--primary);
            color: #fff;
            padding: 1rem 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .menu-item-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: transform 0.2s;
            background-color: #fff;
        }
        .menu-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .item-details {
            flex-grow: 1;
        }
        .btn-order {
            background-color: var(--warning);
            border-color: var(--warning);
            font-weight: bold;
        }
    </style>
</head>
<body>

<header class="header-custom text-center sticky-top">
    <h1 class="h4 mb-0"><i class="fas fa-utensils me-2"></i> Menu Pak Kumis</h1>
</header>

<div class="container py-4">
    
    <div class="alert alert-success text-center" role="alert">
        <i class="fas fa-qrcode me-1"></i> Anda memesan melalui QR Scan. Pembayaran dilakukan di Kantin (untuk Dine-in/Takeaway) atau Cash/Transfer/QR (untuk Runner).
    </div>

    <form id="orderForm" action="{{ route('customer.submit_order') }}" method="POST">
        @csrf
        
        <h2 class="h5 mb-3 text-primary"><i class="fas fa-drumstick-bite me-2"></i> Menu Ayam (Nasi Termasuk)</h2>
        <div class="row">
            
            {{-- ITEM 1: Ayam Penyet --}}
            <div class="col-12">
                <div class="menu-item-card p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                    <div class="item-details mb-2 mb-sm-0">
                        <h6 class="mb-0 fw-bold">Ayam Penyet</h6>
                        <p class="text-muted mb-0 small">RM 10.00 | Ayam goreng, sambal, nasi.</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <select name="level_1" id="level_1" class="form-select form-select-sm me-2" style="width: 100px;">
                            <option value="Level 0" selected>Level 0</option>
                            <option value="Level 1">Level 1</option>
                            <option value="Level 2">Level 2</option>
                            <option value="Level 3">Level 3</option>
                        </select>
                        <div class="input-group input-group-sm" style="width: 100px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_1', -1)">-</button>
                            <input type="number" name="qty_1" id="qty_1" value="0" min="0" class="form-control text-center" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_1', 1)">+</button>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- ITEM 2: Ayam Gepuk --}}
            <div class="col-12">
                <div class="menu-item-card p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                    <div class="item-details mb-2 mb-sm-0">
                        <h6 class="mb-0 fw-bold">Ayam Gepuk</h6>
                        <p class="text-muted mb-0 small">RM 10.00 | Ayam gepuk, sambal bawang, nasi.</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <select name="level_2" id="level_2" class="form-select form-select-sm me-2" style="width: 100px;">
                            <option value="Level 0" selected>Level 0</option>
                            <option value="Level 1">Level 1</option>
                            <option value="Level 2">Level 2</option>
                            <option value="Level 3">Level 3</option>
                            <option value="Level 5">Level 5</option>
                        </select>
                        <div class="input-group input-group-sm" style="width: 100px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_2', -1)">-</button>
                            <input type="number" name="qty_2" id="qty_2" value="0" min="0" class="form-control text-center" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_2', 1)">+</button>
                        </div>
                    </div>
                </div>
            </div>

             {{-- ITEM 3: Ayam Geprek --}}
            <div class="col-12">
                <div class="menu-item-card p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                    <div class="item-details mb-2 mb-sm-0">
                        <h6 class="mb-0 fw-bold">Ayam Geprek</h6>
                        <p class="text-muted mb-0 small">RM 10.00 | Ayam krispi, sambal geprek, nasi.</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <select name="level_3" id="level_3" class="form-select form-select-sm me-2" style="width: 100px;">
                            <option value="Level 0" selected>Level 0</option>
                            <option value="Level 1">Level 1</option>
                            <option value="Level 2">Level 2</option>
                            <option value="Level 3">Level 3</option>
                        </select>
                        <div class="input-group input-group-sm" style="width: 100px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_3', -1)">-</button>
                            <input type="number" name="qty_3" id="qty_3" value="0" min="0" class="form-control text-center" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_3', 1)">+</button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <h2 class="h5 mb-3 mt-4 text-primary"><i class="fas fa-mug-hot me-2"></i> Minuman Sejuk</h2>
        <div class="row">
            
             {{-- ITEM 4: Tea Ice --}}
            <div class="col-12">
                <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold">Tea Ice (Teh Susu Ais)</h6>
                        <p class="text-muted mb-0 small">RM 3.50 | Teh dengan susu dan gula.</p>
                    </div>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_4', -1)">-</button>
                        <input type="number" name="qty_4" id="qty_4" value="0" min="0" class="form-control text-center" readonly>
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_4', 1)">+</button>
                    </div>
                </div>
            </div>
            
            {{-- ITEM 5: Tea O Ice --}}
            <div class="col-12">
                <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold">Tea O Ice (Teh O Ais)</h6>
                        <p class="text-muted mb-0 small">RM 3.00 | Teh tanpa susu.</p>
                    </div>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_5', -1)">-</button>
                        <input type="number" name="qty_5" id="qty_5" value="0" min="0" class="form-control text-center" readonly>
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_5', 1)">+</button>
                    </div>
                </div>
            </div>
            
            {{-- ITEM 6: Milo Ice --}}
            <div class="col-12">
                <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold">Milo Ice</h6>
                        <p class="text-muted mb-0 small">RM 4.50 | Minuman Milo dengan susu.</p>
                    </div>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_6', -1)">-</button>
                        <input type="number" name="qty_6" id="qty_6" value="0" min="0" class="form-control text-center" readonly>
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_6', 1)">+</button>
                    </div>
                </div>
            </div>
            
             {{-- ITEM 7: Milo O Ice --}}
            <div class="col-12">
                <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold">Milo O Ice (Milo Kosong Ais)</h6>
                        <p class="text-muted mb-0 small">RM 4.00 | Minuman Milo tanpa susu.</p>
                    </div>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_7', -1)">-</button>
                        <input type="number" name="qty_7" id="qty_7" value="0" min="0" class="form-control text-center" readonly>
                        <button type="button" class="btn btn-outline-secondary" onclick="updateQty('item_7', 1)">+</button>
                    </div>
                </div>
            </div>
            
        </div>
        
        <hr class="my-4">
        
        <h2 class="h5 mb-3 text-primary"><i class="fas fa-user-check me-2"></i> Data Pelanggan & Tipe Pesanan</h2>

        <div class="card p-3 mb-4">
            <div class="mb-3">
                <label class="form-label fw-bold">Tipe Pesanan <span class="text-danger">*</span></label>
                <div class="btn-group w-100" role="group" id="order_type_group">
                    <input type="radio" class="btn-check" name="order_type" id="type_dinein" value="Dine-in" required checked>
                    <label class="btn btn-outline-success" for="type_dinein"><i class="fas fa-chair me-1"></i> Dine-in</label>

                    <input type="radio" class="btn-check" name="order_type" id="type_takeaway" value="Takeaway" required>
                    <label class="btn btn-outline-success" for="type_takeaway"><i class="fas fa-box me-1"></i> Takeaway</label>
                    
                    <input type="radio" class="btn-check" name="order_type" id="type_delivery" value="Delivery" required>
                    <label class="btn btn-outline-success" for="type_delivery"><i class="fas fa-motorcycle me-1"></i> Runner</label>
                </div>
            </div>

            <div id="runner-options" style="display: none;">
                 <hr>
                 <div class="mb-3">
                    <label class="form-label fw-bold">Opsi Runner (Penghantaran) <span class="text-danger">*</span></label>
                    <select name="runner_option" id="runner_option" class="form-select" onchange="updateSummary()">
                        <option value="Normal">Normal (Tambahan RM 3.00)</option>
                        <option value="Prioritas">Prioritas (Tambahan RM 6.00)</option>
                    </select>
                    <small class="text-muted">Pilih prioritas penghantaran Anda.</small>
                 </div>
                 <div class="mb-3">
                    <label for="delivery_address" class="form-label fw-bold">Alamat Penghantaran <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="delivery_address" name="delivery_address" placeholder="Tulis Alamat Lengkap Anda" rows="2"></textarea>
                 </div>
                 
            </div>
            
            <hr>
            
            <div class="mb-3">
                <label for="customer_name" class="form-label fw-bold">Nama Anda <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Contoh: Rina / Meja 5" required>
            </div>
            
            <div class="mb-3">
                <label for="customer_phone" class="form-label fw-bold">Nomor HP (WhatsApp) <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="customer_phone" name="customer_phone" placeholder="Cth: 01xxxxxxx" required>
            </div>
            
        </div>

        <div class="card p-4 sticky-bottom shadow-lg">
            <h5 class="mb-3">Ringkasan Pesanan</h5>
            <div id="summary-items">
                <p class="text-muted small mb-0">Sila pilih menu di atas.</p>
            </div>
            <div id="runner-fee-display" class="mb-2 small text-end fw-bold" style="display: none;">
                Fee Runner: <span class="text-danger" id="fee_amount_display">RM 0.00</span>
                <input type="hidden" name="runner_fee" id="runner_fee_input" value="0.00">
            </div>
            
            <p class="h4 mt-3 fw-bold">Total Akhir: <span id="total_price_display" class="text-success">RM 0.00</span></p>
            
            <button type="submit" class="btn btn-lg btn-order w-100 mt-2" id="submitOrderBtn" disabled>
                <i class="fas fa-paper-plane me-1"></i> Kirim Pesanan ke Kios
            </button>
            <small class="text-center text-muted mt-2">Pastikan data Anda benar sebelum mengirim pesanan.</small>
        </div>
        
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Data Menu (Harga dalam Ringgit - RM)
    const menuData = {
        'item_1': { name: 'Ayam Penyet', price: 10.00, hasLevel: true },
        'item_2': { name: 'Ayam Gepuk', price: 10.00, hasLevel: true },
        'item_3': { name: 'Ayam Geprek', price: 10.00, hasLevel: true },
        'item_4': { name: 'Tea Ice', price: 3.50, hasLevel: false },
        'item_5': { name: 'Tea O Ice', price: 3.00, hasLevel: false },
        'item_6': { name: 'Milo Ice', price: 4.50, hasLevel: false },
        'item_7': { name: 'Milo O Ice', price: 4.00, hasLevel: false },
    };
    
    // Biaya Runner
    const runnerFees = {
        'Normal': 3.00,
        'Prioritas': 6.00
    };

    const orderQuantities = {};
    const orderLevels = {}; 

    function updateQty(itemId, change) {
        const input = document.getElementById(`qty_${itemId.split('_')[1]}`);
        let currentQty = parseInt(input.value);
        let newQty = currentQty + change;
        
        if (newQty < 0) newQty = 0;
        
        input.value = newQty;
        orderQuantities[itemId] = newQty;
        
        updateSummary();
    }

    function updateSummary() {
        let totalMenu = 0;
        let totalItems = 0;
        let runnerFee = 0.00;
        
        const summaryDiv = document.getElementById('summary-items');
        summaryDiv.innerHTML = '';
        
        // 1. Kumpulkan Level Pedas
        for (let i = 1; i <= 3; i++) {
             const levelSelect = document.getElementById(`level_${i}`);
             if (levelSelect) {
                orderLevels[`item_${i}`] = levelSelect.value;
             }
        }
        
        // 2. Hitung Total Harga Menu
        for (const itemId in orderQuantities) {
            const qty = orderQuantities[itemId];
            if (qty > 0) {
                const item = menuData[itemId];
                const subtotal = qty * item.price;
                totalMenu += subtotal;
                totalItems += qty;
                
                let itemLabel = item.name;
                if (item.hasLevel && orderLevels[itemId]) {
                    itemLabel += ` (${orderLevels[itemId]})`;
                }

                const p = document.createElement('p');
                p.className = 'mb-1 small';
                p.innerHTML = `<span class="fw-bold">${qty}x</span> ${itemLabel} <span class="float-end">RM ${subtotal.toFixed(2)}</span>`;
                summaryDiv.appendChild(p);
            }
        }
        
        // 3. Tambahkan Fee Runner jika Tipe Pesanan = Delivery
        const orderType = document.querySelector('input[name="order_type"]:checked').value;
        const runnerOption = document.getElementById('runner_option').value;
        
        const runnerFeeDisplay = document.getElementById('runner-fee-display');
        const feeAmountDisplay = document.getElementById('fee_amount_display');
        const runnerFeeInput = document.getElementById('runner_fee_input');

        if (orderType === 'Delivery') {
            runnerFee = runnerFees[runnerOption];
            
            runnerFeeDisplay.style.display = 'block';
            feeAmountDisplay.textContent = `RM ${runnerFee.toFixed(2)}`;
            runnerFeeInput.value = runnerFee.toFixed(2);
        } else {
            runnerFee = 0.00;
            runnerFeeDisplay.style.display = 'none';
            runnerFeeInput.value = '0.00';
        }

        const totalAkhir = totalMenu + runnerFee;

        // 4. Update UI
        if (totalItems === 0) {
            summaryDiv.innerHTML = '<p class="text-muted small mb-0">Sila pilih menu di atas.</p>';
            document.getElementById('submitOrderBtn').disabled = true;
        } else {
            document.getElementById('submitOrderBtn').disabled = false;
        }

        document.getElementById('total_price_display').textContent = `RM ${totalAkhir.toFixed(2)}`;
    }
    
    function toggleDeliveryFields() {
        const orderType = document.querySelector('input[name="order_type"]:checked').value;
        const runnerOptionsDiv = document.getElementById('runner-options');
        const addressField = document.getElementById('delivery_address');
        const phoneField = document.getElementById('customer_phone');
        
        if (orderType === 'Delivery') {
            runnerOptionsDiv.style.display = 'block';
            addressField.required = true;
            phoneField.required = true; // Nomor HP wajib untuk Delivery
        } else {
            runnerOptionsDiv.style.display = 'none';
            addressField.required = false;
            phoneField.required = true; // Tetap wajib, tapi mungkin logikanya bisa berbeda di Kios
        }
        
        updateSummary();
    }

    // Initialize quantities and attach listeners on load
    document.addEventListener('DOMContentLoaded', () => {
        // Inisialisasi Kuantitas
        for (const itemId in menuData) {
            orderQuantities[itemId] = 0;
        }
        
        // Listener untuk Level Pedas
        for (let i = 1; i <= 3; i++) {
            const levelSelect = document.getElementById(`level_${i}`);
            if (levelSelect) {
                levelSelect.addEventListener('change', updateSummary);
            }
        }
        
        // Listener untuk Tipe Pesanan dan Opsi Runner
        document.getElementById('order_type_group').addEventListener('change', toggleDeliveryFields);
        document.getElementById('runner_option').addEventListener('change', updateSummary);

        // Inisialisasi Awal
        toggleDeliveryFields(); // Panggil ini untuk set default (Dine-in/Takeaway)
        updateSummary();
    });
</script>
</body>
</html>