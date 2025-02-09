document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelectorAll('.banner-slide');
    let currentIndex = 0;
    const slideInterval = 5000; // 切り替え間隔を5秒に設定

    function showNextSlide() {
        // 現在のスライドを左に移動
        slides[currentIndex].style.transform = 'translateX(-100%)';
        slides[currentIndex].style.opacity = '0';

        // 次のスライドを画面中央に
        currentIndex = (currentIndex + 1) % slides.length;
        slides[currentIndex].style.transform = 'translateX(0)';
        slides[currentIndex].style.opacity = '1';

        // 前のスライドを右に戻す準備
        const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
        slides[prevIndex].style.transform = 'translateX(100%)';
    }

    // 初期スライド位置を設定
    slides.forEach((slide, index) => {
        if (index === 0) {
            slide.style.transform = 'translateX(0)';
            slide.style.opacity = '1';
        } else {
            slide.style.transform = 'translateX(100%)';
            slide.style.opacity = '0';
        }
    });

    // スライド切り替えを開始
    setInterval(showNextSlide, slideInterval);
});
