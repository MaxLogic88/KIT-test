window.onload = function () {
    //Показываем форму редактирования узла
    let editObjects = document.querySelectorAll('.edit');
    for (let i = 0; i < editObjects.length; i++) {
        editObjects[i].addEventListener('click', function () {
            let id = this.parentNode.getAttribute('id').slice(7);
            let data = new FormData();
            data.append('id', id);
            data.append('task', 'getObject');
            SendRequest('post', '/admin/form-handler.php', data, function (data) {
                if (data.DataObject.id === id) {
                    let editBlock = document.querySelector('.edit-object');
                    editBlock.querySelector('[name="title"]').value = data.DataObject.title;
                    editBlock.querySelector('[name="id"]').value = data.DataObject.id;
                    editBlock.querySelector('[name="description"]').value = data.DataObject.description;
                    let select = editBlock.querySelector('[name="parent_id"]');
                    select.options.length = 0;
                    addOption(select, "Не выбрано", 0);
                    if (data.options) {
                        data.options.forEach(function (option) {
                            let selected = false;
                            if (option.id === data.DataObject.parent_id) selected = true;
                            for (let i = 0; i < option.deep; i++) {
                                option.title = '-' + option.title;
                            }
                            addOption(select, option.title, option.id, selected);
                        });
                    }
                    editBlock.style.display = 'block';
                } else {
                    console.log('error', data);
                }
            });
        });
    }

    //Удаляем узел
    let deleteObjects = document.querySelectorAll('.delete');
    for (let i = 0; i < deleteObjects.length; i++) {
        deleteObjects[i].addEventListener('click', function () {
            let id = this.parentNode.getAttribute('id').slice(7);
            let data = new FormData();
            data.append('id', id);
            data.append('task', 'deleteObject');
            if(confirm('Вы действительно хотите удалить этот узел?')) {
                SendRequest('post', '/admin/form-handler.php', data, function (data) {
                    if (data) {
                        data.forEach(function (id) {
                            document.querySelector('#object-' + id).remove();
                        });
                    } else {
                        console.log('error', data);
                    }
                });
            }
        });
    }
}