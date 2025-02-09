document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('/AsoEC/php/mypage.php'); // PHPファイルへのパス
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const user = await response.json();

        if (user.success) {
            // ユーザー情報を表示
            document.getElementById('username').textContent = user.data.username;
            document.getElementById('email').textContent = user.data.email;
            document.getElementById('created-at').textContent = user.data.created_at;

            // ゲストの場合、新規登録リンクを表示
            if (user.data.username === 'ゲスト') {
                document.getElementById('guest-register-link').style.display = 'block';
            }
        } else {
            alert(user.message);
            window.location.href = 'login.html'; // ログインページへのリダイレクト
        }
    } catch (error) {
        console.error('Error:', error);
        alert('サーバーエラーが発生しました。');
        window.location.href = 'login.html';
    }
});

// 更新フォームの処理
document.getElementById('update-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const newUsername = document.getElementById('new-username').value;
    const newEmail = document.getElementById('new-email').value;

    try {
        const response = await fetch('/AsoEC/php/update_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ newUsername, newEmail })
        });

        const result = await response.json();
        alert(result.message);

        if (result.success) {
            // 更新後の情報を反映
            if (newUsername) document.getElementById('username').textContent = newUsername;
            if (newEmail) document.getElementById('email').textContent = newEmail;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('更新処理に失敗しました。');
    }
});

// ログアウトボタンの処理
document.getElementById('logout').addEventListener('click', async () => {
    try {
        const response = await fetch('/AsoEC/php/logout.php');
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const result = await response.json();
        alert(result.message);
        if (result.success) {
            window.location.href = 'login.html'; // ログインページへのリダイレクト
        }
    } catch (error) {
        console.error('Error:', error);
        alert('ログアウト処理に失敗しました。');
    }
});
