function MeGusta(id){
    let ruta= Routing.generate('likes');

    $.ajax({
        type: 'POST',
        url: ruta,
        data: ({id: id}),
        async: true,
        dataType: "json",
        success: function(data){
            window.location.reload();
        }
    });
}