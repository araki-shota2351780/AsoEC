document.addEventListener("DOMContentLoaded", () => {
    const adminId = document.getElementById("adminCheck").placeholder;
    const adminInput = document.getElementById("adminCheck");
    const addAdminButton = document.getElementById("addAdminButton");

    // 管理者ID確認
    adminInput.addEventListener("input", () => {
        if (adminInput.value === adminId) {
            document.getElementById("adminStatus").textContent = "管理者IDが確認されました。";
            document.getElementById("adminStatus").style.color = "green";
            addAdminButton.disabled = false;
        } else {
            document.getElementById("adminStatus").textContent = "管理者IDが一致しません。";
            document.getElementById("adminStatus").style.color = "red";
            addAdminButton.disabled = true;
        }
    });
});
