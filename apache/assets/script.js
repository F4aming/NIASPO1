// Функция для отправки заказа через AJAX
document.querySelector("form").addEventListener("submit", function (event) {
    event.preventDefault(); // Отключаем стандартную отправку формы

    const formData = new FormData(event.target); // Получаем данные формы
    const data = {
        dish_name: formData.get("dish_name"),
        quantity: formData.get("quantity")
    };

    // Отправка данных на сервер
    fetch("/", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
        .then(response => response.text())
        .then(responseText => {
            alert(responseText); // Показываем сообщение об успехе/ошибке
            loadOrders(); // Обновляем список заказов
        })
        .catch(error => console.error("Ошибка:", error));
});

// Функция для загрузки списка заказов через AJAX
function loadOrders() {
    fetch("/?action=get_orders")
        .then(response => response.json())
        .then(orders => {
            const ordersList = document.querySelector("ul");
            ordersList.innerHTML = ""; // Очищаем текущий список

            if (orders.length > 0) {
                orders.forEach(order => {
                    const listItem = document.createElement("li");
                    listItem.innerHTML = `
                        <strong>${order.dish_name}</strong><br>
                        Количество: ${order.quantity}<br>
                        <small>Добавлено: ${order.created_at}</small>
                    `;
                    ordersList.appendChild(listItem);
                });
            } else {
                ordersList.innerHTML = "<li>Нет заказов.</li>";
            }
        })
        .catch(error => console.error("Ошибка при загрузке заказов:", error));
}

function addToOrder(image) {
        // Получаем данные из атрибутов изображения
    const orderName = image.getAttribute('data-name');
    const quantity = image.getAttribute('data-quantity');

        // Заполняем скрытую форму
    document.getElementById('order_item').value = orderName;
    document.getElementById('quantity').value = quantity;

        // Автоматически отправляем форму
    document.getElementById('addOrderForm').submit();
}

// Загружаем заказы при загрузке страницы
document.addEventListener("DOMContentLoaded", loadOrders);
