// Basic form validation for login
document.querySelector("form").addEventListener("submit", (e) => {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!email || !password) {
        e.preventDefault();
        alert("Please fill in all fields.");
    }
});
