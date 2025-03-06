function openModal() {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

    //запись онлайн
function submitBooking() {
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const phone = document.getElementById('phone').value;

    if (date && time && phone) {
        alert(`Запись на ${date} в ${time} успешно оформлена! Мы свяжемся с вами по номеру ${phone}.`);
        closeModal();
    } else {
        alert('Пожалуйста, заполните все поля.');
    }
}

function toggleMenu() {
    const navMenu = document.getElementById('nav-menu');
    navMenu.classList.toggle('active');
}
