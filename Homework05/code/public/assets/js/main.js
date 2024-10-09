document.querySelectorAll('.delete_user').forEach((delete_btn) => {
    delete_btn.addEventListener('click', function (e) {
        e.preventDefault();
        
        $.ajax({
            method: "POST",
            url: "/user/delete/",
            data: { id: e.target.dataset.id },
        }).done(function (response) {
            $(e.target).parent().parent().remove();
        });
    });
});

const userTable = $("#userTable");
if (userTable) {
    let maxId = $(".table-responsive tbody tr:last-child td:first-child").html();
    setInterval(function () {
        $.ajax({
            method: "POST",
            url: "/user/indexRefresh/",
            data: { maxId: maxId },
        }).done(function (response) {
            let users = $.parseJSON(response);
            
            if (users.length != 0) {
                for (var k in users) {
                    let row = "<tr>";

                    row += "<td>" + users[k].id + "</td>";
                    maxId = users[k].id;

                    row += "<td>" + users[k].username + "</td>";
                    row += "<td>" + users[k].userlastname + "</td>";
                    row += "<td>" + users[k].userbirthday + "</td>";
                    row += `<td><a href="/user/edit/?id=${users[k].id}" class="btn btn-primary">Редактировать</a>
                            <a href="#" class="btn btn-danger delete_user" data-id="${users[k].id}">Удалить</a></td>`;

                    row += "</tr>";

                    $("#userTable tbody").append(row);
                }
            }
        });

    }, 1000);
}
