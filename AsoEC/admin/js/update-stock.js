document.addEventListener("DOMContentLoaded", () => {
    const adminId = document.getElementById("adminCheck").placeholder;
    const adminInput = document.getElementById("adminCheck");
    const updateButton = document.getElementById("updateButton");

    // 管理者ID確認
    adminInput.addEventListener("input", () => {
        if (adminInput.value === adminId) {
            document.getElementById("adminStatus").textContent = "管理者IDが確認されました。";
            document.getElementById("adminStatus").style.color = "green";
            updateButton.disabled = false;
        } else {
            document.getElementById("adminStatus").textContent = "管理者IDが一致しません。";
            document.getElementById("adminStatus").style.color = "red";
            updateButton.disabled = true;
        }
    });
});
