// Custom JS for canteen management system

// Cart array to hold selected items
// Centralized cart object for all pages
// Stores cart items as {itemId: {itemId, name, price, image, quantity}}
let cart = {};

// Add item to cart
function addToCart(itemId, itemName, price, image, quantity) {
    // Add item to cart or update quantity
    if (!cart[itemId]) {
        cart[itemId] = {
            itemId,
            name: itemName,
            price,
            image,
            quantity
        };
    } else {
        cart[itemId].quantity += quantity;
    }
    renderCart();
}

// Remove item from cart
function removeFromCart(itemId) {
    // Remove item from cart
    delete cart[itemId];
    renderCart();
}

// Render cart table in UI
function renderCart() {
    // Render cart table in UI
    const tbody = document.querySelector('#cart-table tbody');
    tbody.innerHTML = '';
    let total = 0;
    Object.values(cart).forEach(item => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><img src="../assets/uploads/${item.image}" style="height:40px;width:40px;object-fit:cover;"> ${item.name}</td>
            <td><input type="number" min="1" value="${item.quantity}" class="form-control cart-qty" data-id="${item.itemId}"></td>
            <td>₹${item.price}</td>
            <td>₹${subtotal.toFixed(2)}</td>
            <td><button class="btn btn-danger btn-sm remove-cart-btn" data-id="${item.itemId}">Remove</button></td>
        `;
        tbody.appendChild(row);
    });
    document.getElementById('cart-total').textContent = total.toFixed(2);
}

// Event listeners for cart actions
window.addEventListener('DOMContentLoaded', () => {
    // Add to Cart button
    document.body.addEventListener('click', function(e) {
        // Add item to cart
        if (e.target.classList.contains('add-to-cart')) {
            const card = e.target.closest('.card-body');
            const itemId = parseInt(e.target.dataset.id);
            const itemName = e.target.dataset.name;
            const price = parseFloat(e.target.dataset.price);
            const image = e.target.dataset.image || '';
            addToCart(itemId, itemName, price, image, 1);
        }
        // Remove from cart
        if (e.target.classList.contains('remove-cart-btn')) {
            const itemId = parseInt(e.target.dataset.id);
            removeFromCart(itemId);
        }
    });
    // Update quantity in cart
    document.body.addEventListener('change', function(e) {
        if (e.target.classList.contains('cart-qty')) {
            const itemId = parseInt(e.target.dataset.id);
            const qty = parseInt(e.target.value);
            if (cart[itemId] && qty > 0) {
                cart[itemId].quantity = qty;
                renderCart();
            }
        }
    });
    // Place order button
    document.getElementById('place-order-btn').addEventListener('click', function() {
        if (Object.keys(cart).length === 0) {
            document.getElementById('order-message').innerHTML = '<div class="alert alert-warning">Cart is empty.</div>';
            return;
        }
        // AJAX order placement
        const cartData = Object.values(cart);
        fetch('../backend/place_order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({cart: cartData})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                cart = {};
                renderCart();
                alert('Order placed successfully!');
                location.reload();
            } else {
                alert('Order failed: ' + (data.message || 'Unknown error'));
            }
        });
    });
});

// Toast notification logic
// Toast notification logic
function showToast(message, type = 'info') {
    // Show toast notification
    let toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.role = 'alert';
    toast.innerHTML = `<div class='d-flex'><div class='toast-body'>${message}</div><button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast'></button></div>`;
    document.body.appendChild(toast);
    let bsToast = new bootstrap.Toast(toast, {delay: 2500});
    bsToast.show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

// AJAX error handler
function handleAjaxError(error) {
    // Handle AJAX errors with toast
    showToast('Error: ' + error, 'danger');
}
