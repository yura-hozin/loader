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
	if (window.FormData === undefined) {
		alert('В вашем браузере FormData не поддерживается')
	} else {
		var formData = new FormData();
		var alias = $("#js-file").data('alias');
		formData.append('file', $("#js-file")[0].files[0]);

		$.ajax({
			type: "POST",
			url: '/admin/loader/upload/one',
			cache: false,
			contentType: false,
			processData: false,
			data: formData,
			dataType : 'json',
			success: function(msg){
				if (msg.error == '') {
					$("#js-file").hide();
					$('#result').html(msg.success);
				} else {
					$('#result').html(msg.error);
				}
			}
		});
	}
});
JS
);
?>


<h1>Шаблон загрузки файла</h1>

<input type="file" id="js-file" data-alias="<?=$alias;?>"/>

<div id="result">
    <!-- Результат из upload.php -->
</div>