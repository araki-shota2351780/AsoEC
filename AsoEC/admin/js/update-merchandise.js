document.addEventListener("DOMContentLoaded", () => {
    const adminId = document.getElementById("adminCheck").placeholder;
    const adminInput = document.getElementById("adminCheck");
    const updateButton = document.getElementById("updateButton");
    const merchSelect = document.getElementById("merch_id");

    // 商品選択時に情報取得
    merchSelect.addEventListener("change", () => {
        const merchId = merchSelect.value;
        if (merchId) {
            fetch(`/AsoEC/admin/php/get-merchandise.php?merch_id=${merchId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("name").value = data.name || '';
                    document.getElementById("description").value = data.description || '';
                    document.getElementById("price").value = data.price || '';
                    document.getElementById("stock").value = data.stock_quantity || '';
                    document.getElementById("current_image_url").value = data.image_url || '';
                    document.getElementById("currentImage").innerHTML = data.image_url
                        ? `<img src="${data.image_url}" alt="現在の商品画像" style="max-width: 100px;">`
                        : '<p>画像は登録されていません。</p>';
                });
        }
    });

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
