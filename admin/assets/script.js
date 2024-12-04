document.addEventListener('DOMContentLoaded', () => {
    // Подтверждение удаления заказа
    const deleteButtons = document.querySelectorAll('.delete-button');

    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const confirmDelete = confirm("Вы уверены, что хотите удалить этот заказ?");
            if (!confirmDelete) {
                event.preventDefault();
            }
        });
    });

    // Уведомления об успешных действиях (пример для удаления)
    const notification = document.querySelector('.notification');
    if (notification) {
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000); // Скрыть уведомление через 3 секунды
    }
});
