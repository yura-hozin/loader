<?php
/**
 * Created by PhpStorm.
 * User: Юрий&Елена
 * Date: 08.03.23
 * Time: 9:05
 */
?>

<?php
$this->registerJs(<<<JS
$("#js-file").change(function(){
	if (window.FormData === undefined){
		alert('В вашем браузере FormData не поддерживается')
	} else {
		var formData = new FormData();
		var alias = $("#js-file").data('alias');
		$.each($("#js-file")[0].files,function(key, input){
			formData.append('file[]', input);
		});

		$.ajax({
			type: "POST",
			url: '/admin/loader/upload',
			cache: false,
			contentType: false,
			processData: false,
			data: formData,
			dataType : 'json',
			success: function(data){
			    var links = [];
				data.forEach(function(msg) {
					$('#result').append(msg.success);
					if (msg.error != '')
					    $('#result').append(msg.error);
                    else
                    {
                        $('#result').append(msg.path);
                        links.push(msg.path);
                    }
				});
				// Перемещаем файлы
				sendMoveFiles(links, alias);
			}
		});
	}
});

function sendMoveFiles(files, alias)
{
    //console.log("00000000000000000000");
    var arr = {
         "files": files,
         "alias": alias
    }
    $.ajax({
        type: "POST",
        url: '/admin/loader/move',
        data: arr,
        dataType : 'json',
        success: function(data){
            //console.log("888888888888888888888888");
        },
        error: function (jqXHR, exception) {
            if (jqXHR.status === 0) {
                alert('Not connect. Verify Network.');
            } else if (jqXHR.status == 404) {
                alert('Requested page not found (404).');
            } else if (jqXHR.status == 500) {
                alert('Internal Server Error (500).');
            } else if (exception === 'parsererror') {
                alert('Requested JSON parse failed.');
            } else if (exception === 'timeout') {
                alert('Time out error.');
            } else if (exception === 'abort') {
                alert('Ajax request aborted.');
            } else {
                alert('Uncaught Error. ' + jqXHR.responseText);
            }
        }
    });
}

JS
);
?>


<h1>Шаблон загрузки файлов</h1>

<input type="file" multiple id="js-file" data-alias="<?=$alias;?>"/>

<div id="result">
    <!-- Результат из upload.php -->
</div>