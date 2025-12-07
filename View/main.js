
// JS from index.php
    function showLoginModal() {
    document.getElementById('loginModal').classList.add('active');
}
    function closeLoginModal() {
    document.getElementById('loginModal').classList.remove('active');
}
    function showRegisterModal() {
    document.getElementById('registerModal').classList.add('active');
}
    function closeRegisterModal() {
    document.getElementById('registerModal').classList.remove('active');
}
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}


// JS from cart.php
function showLoginModal() {
    document.getElementById('loginModal').classList.add('active');
}
function closeLoginModal() {
    document.getElementById('loginModal').classList.remove('active');
}
function showRegisterModal() {
    document.getElementById('registerModal').classList.add('active');
}
function closeRegisterModal() {
    document.getElementById('registerModal').classList.remove('active');
}
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}

// JS from profile.php
    function showOrderDetails(orderId) {
    const modal = document.getElementById('orderModal');
    modal.classList.add('active');

    fetch(`get_order_details.php?id=${orderId}`)
    .then(response => response.text())
    .then(html => {
    document.getElementById('orderDetailsContent').innerHTML = html;
})
    .catch(error => {
    document.getElementById('orderDetailsContent').innerHTML = '<p class="text-danger">Fehler beim Laden.</p>';
});
}

    function closeOrderModal() {
    document.getElementById('orderModal').classList.remove('active');
}

    window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
    event.target.classList.remove('active');
}
}


//JS from product.php
    function showLoginModal() {
    document.getElementById('loginModal').classList.add('active');
}
    function closeLoginModal() {
    document.getElementById('loginModal').classList.remove('active');
}
    function showRegisterModal() {
    document.getElementById('registerModal').classList.add('active');
}
    function closeRegisterModal() {
    document.getElementById('registerModal').classList.remove('active');
}
    window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
    event.target.classList.remove('active');
}
}


// JS from admin.php

    function showAddProductModal() {
    document.getElementById('productModalTitle').textContent = 'Produkt hinzufügen';
    document.getElementById('productForm').reset();
    document.getElementById('product-id').value = '';
    document.getElementById('productSubmitBtn').name = 'add_product';
    document.getElementById('productSubmitBtn').textContent = 'Hinzufügen';
    document.getElementById('productModal').classList.add('active');
}

    function editProduct(product) {
    document.getElementById('productModalTitle').textContent = 'Produkt bearbeiten';
    document.getElementById('product-id').value = product.id;
    document.getElementById('product-name').value = product.name;
    document.getElementById('product-description').value = product.description;
    document.getElementById('product-price').value = product.price;
    document.getElementById('product-stock').value = product.stock;
    document.getElementById('product-image').value = product.image || '';
    document.getElementById('productSubmitBtn').name = 'edit_product';
    document.getElementById('productSubmitBtn').textContent = 'Aktualisieren';
    document.getElementById('productModal').classList.add('active');
}

    function closeProductModal() {
    document.getElementById('productModal').classList.remove('active');
}

    function updateOrderStatus(orderId, currentStatus) {
    document.getElementById('status-order-id').value = orderId;
    document.getElementById('order-status').value = currentStatus;
    document.getElementById('statusModal').classList.add('active');
}

    function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('active');
}

    function updateUserRole(userId, currentRole, username) {
    document.getElementById('role-user-id').value = userId;
    document.getElementById('role-username').textContent = username;
    document.getElementById('user-role').value = currentRole;
    document.getElementById('roleModal').classList.add('active');
}

    function closeRoleModal() {
    document.getElementById('roleModal').classList.remove('active');
}

    function deleteProduct(productId) {
    if (confirm('Wirklich löschen?')) {
    document.getElementById('delete-product-id').value = productId;
    document.getElementById('deleteForm').submit();
}
}

    function showOrderDetails(orderId) {
    const modal = document.getElementById('orderModal');
    modal.classList.add('active');

    fetch(`get_order_details.php?id=${orderId}`)
    .then(response => response.text())
    .then(html => {
    document.getElementById('orderDetailsContent').innerHTML = html;
})
    .catch(error => {
    document.getElementById('orderDetailsContent').innerHTML = '<p class="text-danger">Fehler beim Laden.</p>';
});
}

    function closeOrderModal() {
    document.getElementById('orderModal').classList.remove('active');
}

    window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
    event.target.classList.remove('active');
}
}
