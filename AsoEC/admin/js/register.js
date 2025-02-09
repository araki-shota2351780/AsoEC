// Basic form validation for registration
document.querySelector("form").addEventListener("submit", (e) => {
    const adminName = document.getElementById("admin_name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!adminName || !email || !password) {
        e.preventDefault();
        alert("Please fill in all fields.");
    }
});
