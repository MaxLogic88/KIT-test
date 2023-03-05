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
                    data.options.forEach(function(option) {
                        let selected = false;
                        if (option.id === data.DataObject.parent_id) selected = true;
                        addOption(select, option.title, option.id, selected);
                    });
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
            SendRequest('post', '/admin/form-handler.php', data, function (data) {
                if (data === 'success') {
                    let li = deleteObjects[i].parentNode.parentNode;
                    let ul = li.parentNode;
                    li.remove();
                    if (ul.querySelector('li') === null) ul.remove();
                } else {
                    console.log('error', data);
                }
            });
        });
    }
}