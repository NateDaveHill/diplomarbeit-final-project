
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
