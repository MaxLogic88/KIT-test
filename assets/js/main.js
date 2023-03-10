window.onload = function () {
    //Получаем описание объекта
    let domObjects = document.querySelectorAll('.title');
    for (let i = 0; i < domObjects.length; i++) {
        domObjects[i].addEventListener('click', function () {
            let id = this.parentNode.getAttribute('id').slice(7);
            let data = new FormData();
            data.append('id', id);
            data.append('task', 'getObject');
            SendRequest('post', '/admin/form-handler.php', data, function (data) {
                if (data.DataObject.id === id) {
                    let textBlock = document.querySelector('.description .text');
                    textBlock.innerText = data.DataObject.description;
                } else {
                    console.log('error', data);
                }
            });
        });
    }

    //Показываем дочерние ветки
    let showChilds = document.querySelectorAll('.showChilds');
    for (let i = 0; i < showChilds.length; i++) {
        showChilds[i].addEventListener('click', function () {
            let parentId = this.parentNode.getAttribute('id').slice(7);
            let childs = document.querySelectorAll('[data-parent="' + parentId +'"]');
            for (let j = 0; j < childs.length; j++) {
                childs[j].style.display = 'block';
                document.querySelector('#object-' + parentId + ' .showChilds').style.display = 'none';
            }
        });
    }
}