<div class="modal fade" id="manualOrderModal" tabindex="-1" aria-labelledby="manualOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="manualOrderModalLabel"><i class="fas fa-keyboard me-2"></i> Input Pesanan Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label for="orderType" class="form-label fw-bold">Tipe Pesanan</label>
                        <select class="form-select" id="orderType" name="type" required>
                            <option value="Dine-in">Dine-in (Makan di Tempat)</option>
                            <option value="Takeaway" selected>Takeaway (Bawa Pulang)</option>
                            <option value="Delivery">Delivery (Diambil Runner)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="deliveryDetails" style="display: none;">
                        <label for="deliveryAddress" class="form-label fw-bold">Alamat/Tujuan Pengiriman</label>
                        <textarea class="form-control" id="deliveryAddress" name="delivery_address" rows="2" placeholder="Contoh: Gedung A, Ruang 101"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="orderItems" class="form-label fw-bold">Detail Pesanan (Menu)</label>
                        <textarea class="form-control" id="orderItems" name="items_raw" rows="4" placeholder="Contoh: 1 Nasi Goreng Spesial, 2 Es Teh Manis" required></textarea>
                        <small class="text-muted">Ini akan diolah oleh Staf untuk dimasak.</small>
                    </div>

                    <div class="mb-3">
                        <label for="totalAmount" class="form-label fw-bold">Total Pembayaran (Wajib diisi di Kios)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="totalAmount" name="total" placeholder="Contoh: 25000" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i> Simpan & Mulai Masak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const orderTypeSelect = document.getElementById('orderType');
        const deliveryDetails = document.getElementById('deliveryDetails');
        
        // Logic menampilkan/menyembunyikan kolom alamat
        orderTypeSelect.addEventListener('change', function() {
            if (this.value === 'Delivery') {
                deliveryDetails.style.display = 'block';
                document.getElementById('deliveryAddress').setAttribute('required', 'required');
            } else {
                deliveryDetails.style.display = 'none';
                document.getElementById('deliveryAddress').removeAttribute('required');
            }
        });
    });
</script>