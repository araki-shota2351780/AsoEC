document.getElementById('merchForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const category = document.getElementById('category').value;
    const size = document.getElementById('size').value;

    const randomString = Math.random().toString(36).substring(2, 7);
    const merchId = `${category}${size}${randomString}`;

    document.getElementById('merch_id').value = merchId;

    this.submit();
});

// 管理者ID確認処理
const adminId = document.getElementById('adminCheck').placeholder; // プレースホルダーから取得
const adminInput = document.getElementById('adminCheck');
const registerButton = document.getElementById('registerButton');
const adminStatus = document.getElementById('adminStatus');

// 管理者ID入力チェック
adminInput.addEventListener('input', () => {
    if (adminInput.value === adminId) {
        adminStatus.textContent = "管理者IDが確認されました。";
        adminStatus.style.color = "green";
        registerButton.disabled = false;
    } else {
        adminStatus.textContent = "管理者IDが一致しません。";
        adminStatus.style.color = "red";
        registerButton.disabled = true;
    }
});
