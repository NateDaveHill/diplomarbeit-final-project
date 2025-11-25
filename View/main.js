
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
