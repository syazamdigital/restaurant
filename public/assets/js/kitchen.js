// Dashboard Manager
class DashboardManager {
    constructor() {
        this.currentOpenModalOrderId = null;
        this.isModalOpen = false;
        this.updateInterval = null;
        this.config = {
            statusColors: {
                'Pending': 'warning', 'Cooking': 'warning', 'Ready': 'info',
                'Waiting Runner': 'danger', 'Completed': 'success', 'Cancelled': 'secondary'
            },
            statusIcons: {
                'Pending': 'fa-clock', 'Cooking': 'fa-fire', 'Ready': 'fa-check',
                'Waiting Runner': 'fa-person-walking', 'Completed': 'fa-check-double', 'Cancelled': 'fa-ban'
            },
            typeBadges: {
                'Dine-in': ['bg-primary', 'fa-utensils'],
                'Takeaway': ['bg-success', 'fa-bag-shopping'],
                'Delivery': ['bg-secondary', 'fa-truck']
            }
        };
    }

    init() {
        this.highlightNewOrders();
        this.initModalEvents();
        this.startRealTimeUpdates();
        setTimeout(() => this.removeHighlights(), 15000);
    }

    highlightNewOrders() {
        document.querySelectorAll('.order-row.new-order').forEach(row => {
            console.log('New order highlighted');
        });
    }

    removeHighlights() {
        document.querySelectorAll('.order-row.new-order').forEach(row => {
            row.classList.remove('table-warning', 'new-order');
            row.style.animation = 'none';
        });
    }

    initModalEvents() {
        const modal = document.getElementById('orderDetailModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', () => this.isModalOpen = true);
            modal.addEventListener('hide.bs.modal', () => {
                this.isModalOpen = false;
                this.currentOpenModalOrderId = null;
            });
        }

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-outline-primary[data-order-id]');
            if (btn) {
                this.currentOpenModalOrderId = btn.getAttribute('data-order-id');
                this.loadOrderDetails(this.currentOpenModalOrderId);
            }
        });
    }

    startRealTimeUpdates() {
        this.updateInterval = setInterval(() => this.updateDashboardData(), 5000);
    }

    stopRealTimeUpdates() {
        if (this.updateInterval) clearInterval(this.updateInterval);
    }

    updateDashboardData() {
        this.updateOrderCounts();
        this.updateActiveOrders();
        if (this.isModalOpen && this.currentOpenModalOrderId) {
            this.loadOrderDetails(this.currentOpenModalOrderId);
        }
    }

    async updateOrderCounts() {
        try {
            const response = await fetch('/order-counts');
            const data = await response.json();
            this.updateNavbarBadges(data);
            this.updateStatisticsCards(data);
        } catch (error) {
            console.log('Order counts update failed:', error);
        }
    }

    updateNavbarBadges(data) {
        const pendingCount = (data.pending || 0) + (data.cooking || 0) + (data.ready || 0) + (data.waiting_runner || 0);
        const kitchenCount = (data.pending || 0) + (data.cooking || 0);
        
        const orderBadge = document.querySelector('#navbarNav .nav-link[href*="orders"] .badge');
        const kitchenBadge = document.querySelector('#navbarNav .nav-link[href*="kitchen"] .badge');
        
        if (orderBadge) orderBadge.textContent = pendingCount;
        if (kitchenBadge) kitchenBadge.textContent = kitchenCount;
    }

    updateStatisticsCards(data) {
        const kitchenCount = (data.pending || 0) + (data.cooking || 0);
        const elements = {
            kitchen: '.card:nth-child(2) h3',
            pending: '.card:nth-child(3) h3', 
            ready: '.card:nth-child(4) h3'
        };

        Object.entries(elements).forEach(([key, selector]) => {
            const el = document.querySelector(selector);
            if (el) el.textContent = key === 'kitchen' ? kitchenCount : (data[key] || 0);
        });
    }

    async updateActiveOrders() {
        try {
            const response = await fetch('/api/active-orders');
            const data = await response.json();
            if (data.success && data.orders) {
                this.renderOrdersTable(data.orders);
            }
        } catch (error) {
            console.log('Active orders update failed:', error);
        }
    }

    renderOrdersTable(orders) {
        const tbody = document.getElementById('orderListBody');
        if (!tbody) return;

        if (!orders?.length) {
            tbody.innerHTML = this.getEmptyStateHTML();
            return;
        }

        tbody.innerHTML = orders.map(order => this.getOrderRowHTML(order)).join('');
        this.initModalEvents();
        this.highlightNewOrders();
    }

    getOrderRowHTML(order) {
        const createdTime = new Date(order.created_at).toLocaleTimeString('en-US', {
            hour: '2-digit', minute: '2-digit'
        });

        return `
        <tr data-order-id="${order.id}" class="order-row align-middle ${order.status === 'Pending' ? 'table-warning new-order' : ''}">
            <td class="ps-4 fw-bold">${order.order_id}</td>
            <td>${this.getTypeBadgeHTML(order.type)}</td>
            <td>${this.getOrderInfoHTML(order)}</td>
            <td class="fw-bold text-nowrap">RM ${parseFloat(order.total_amount).toFixed(2)}</td>
            <td>${this.getStatusBadgeHTML(order.status)}</td>
            <td class="text-nowrap"><small class="text-muted">${createdTime}</small></td>
            <td class="pe-4">${this.getActionButtonsHTML(order)}</td>
        </tr>`;
    }

    getTypeBadgeHTML(type) {
        const [badgeClass, icon] = this.config.typeBadges[type] || ['bg-dark', 'fa-question'];
        return `<span class="badge ${badgeClass}"><i class="fas ${icon} me-1"></i>${type}</span>`;
    }

    getStatusBadgeHTML(status) {
        const color = this.config.statusColors[status] || 'light';
        const icon = this.config.statusIcons[status] || 'fa-question';
        return `<span class="badge bg-${color} text-dark"><i class="fas ${icon} me-1"></i>${status}</span>`;
    }

    getOrderInfoHTML(order) {
        return `
        <div class="d-flex flex-column">
            <span class="fw-medium" title="${order.items_description}">
                ${this.truncateText(order.items_description, 30)}
            </span>
            ${order.customer_name ? `<small class="text-muted">${order.customer_name}</small>` : ''}
            ${order.customer_phone ? `<small class="text-muted">${order.customer_phone}</small>` : ''}
        </div>`;
    }

    getActionButtonsHTML(order) {
        return `
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-primary" data-order-id="${order.id}" title="View Details">
                <i class="fas fa-eye"></i>
            </button>
            ${this.getActionButtonHTML(order)}
        </div>`;
    }

    getActionButtonHTML(order) {
        const actions = {
            'Pending': () => `<button type="button" class="btn btn-warning text-dark" onclick="dashboard.updateOrderStatus(${order.id}, 'Cooking')" title="Start Cooking"><i class="fas fa-play me-1"></i>Start</button>`,
            'Cooking': () => `<button type="button" class="btn btn-info text-white" onclick="dashboard.updateOrderStatus(${order.id}, 'Ready')" title="Mark as Ready"><i class="fas fa-check me-1"></i>Ready</button>`,
            'Ready': () => `<button type="button" class="btn btn-success" onclick="dashboard.confirmCompleteOrder(${order.id})" title="Complete Order"><i class="fas fa-flag-checkered"></i></button>`,
            'Waiting Runner': () => `<button type="button" class="btn btn-success" onclick="dashboard.confirmCompleteOrder(${order.id})" title="Complete Order"><i class="fas fa-flag-checkered"></i></button>`,
            'Completed': () => `<span class="badge bg-success">Selesai</span>`
        };

        return actions[order.status] ? actions[order.status]() : '';
    }

    getEmptyStateHTML() {
        return `
        <tr>
            <td colspan="7" class="text-center py-4">
                <div class="text-muted">
                    <i class="fas fa-inbox fa-2x mb-3"></i>
                    <p class="mb-0">No active orders at the moment</p>
                    <small>New orders will appear here automatically</small>
                </div>
            </td>
        </tr>`;
    }

    async updateOrderStatus(orderId, newStatus) {
        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('new_status', newStatus);

            const response = await fetch(`/orders/${orderId}/update-status`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                this.showToast('success', data.message);
                this.updateOrderRowUI(orderId, data.order);
                setTimeout(() => this.updateDashboardData(), 1000);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error updating order status:', error);
            this.showToast('error', 'Gagal mengupdate status: ' + error.message);
        }
    }

    updateOrderRowUI(orderId, orderData) {
        const orderRow = document.querySelector(`tr[data-order-id="${orderId}"]`);
        if (!orderRow) return;

        // Update status badge
        const statusBadge = orderRow.querySelector('.badge');
        if (statusBadge) {
            statusBadge.outerHTML = this.getStatusBadgeHTML(orderData.status);
        }

        // Update action buttons
        const actionCell = orderRow.querySelector('td:last-child');
        if (actionCell) {
            actionCell.innerHTML = this.getActionButtonsHTML(orderData);
        }

        // Remove highlight
        if (orderRow.classList.contains('new-order')) {
            orderRow.classList.remove('table-warning', 'new-order');
            orderRow.style.animation = 'none';
        }
    }

    confirmCompleteOrder(orderId) {
        if (confirm('Apakah Anda yakin pesanan ini sudah Selesai/Diambil?')) {
            this.updateOrderStatus(orderId, 'Completed');
        }
    }

    showToast(type, message) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now();
        const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        container.insertAdjacentHTML('beforeend', `
            <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="fas ${icon} me-2"></i>${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
    }

    async loadOrderDetails(orderId) {
        try {
            const response = await fetch(`/order/${orderId}`);
            const data = await response.json();
            
            if (data.error) throw new Error(data.error);
            
            this.updateModalContent(data);
            
            if (!this.isModalOpen) {
                new bootstrap.Modal(document.getElementById('orderDetailModal')).show();
            }
        } catch (error) {
            console.error('Error loading order details:', error);
            if (this.isModalOpen) {
                alert('Gagal memuat detail pesanan: ' + error.message);
            }
        }
    }

    updateModalContent(data) {
        const elements = {
            'modal-order-id': data.order_id,
            'modal-status': data.status_html,
            'modal-type': data.type,
            'modal-customer-name': data.customer_name || '-',
            'modal-created-at': data.created_at,
            'modal-total-amount': data.total_amount,
            'modal-items-description': data.items_description
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) element.innerHTML = value;
        });

        const deliveryCard = document.getElementById('delivery-details-card');
        const noDeliveryInfo = document.getElementById('no-delivery-info');
        
        if (data.delivery) {
            deliveryCard.style.display = 'block';
            noDeliveryInfo.style.display = 'none';
            document.getElementById('modal-address').innerHTML = data.delivery_address || '-';
            document.getElementById('modal-runner-option').textContent = data.runner_option || '-';
            document.getElementById('modal-runner-fee').textContent = data.runner_fee || '-';
        } else {
            deliveryCard.style.display = 'none';
            noDeliveryInfo.style.display = 'block';
        }
    }

    truncateText(text, length) {
        return text && text.length > length ? text.substring(0, length) + '...' : (text || '');
    }
}

// Initialize dashboard
const dashboard = new DashboardManager();
document.addEventListener('DOMContentLoaded', () => dashboard.init());
window.addEventListener('beforeunload', () => dashboard.stopRealTimeUpdates());