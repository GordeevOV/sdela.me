jQuery(function($){
	/*
	 * действие при нажатии на кнопку загрузки изображения
	 * вы также можете привязать это действие к клику по самому изображению
	 */
	$('body').on('click', '.upload_image_button', function(){
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $(this);
		wp.media.editor.send.attachment = function(props, attachment) {
			$(button).parent().prev().attr('src', attachment.url);
			$(button).prev().val(attachment.id);
			wp.media.editor.send.attachment = send_attachment_bkp;
		}
		wp.media.editor.open(button);
		return false;    
	});
	/*
	 * удаляем значение произвольного поля
	 * если быть точным, то мы просто удаляем value у input type="hidden"
	 */
	$('body').on('click', '.remove_image_button', function(){
		var r = confirm("Уверены?");
		if (r == true) {
			var src = $(this).parent().prev().attr('data-src');
			$(this).parent().prev().attr('src', src);
			$(this).prev().prev().val('');
		}
		return false;
	});
	
	$('.add_image_button').click(function(){
		lastnum = $('.ztumetabox_number').last().val();
		//alert (lastnum);
		nextnum = parseInt(lastnum) + 1;
		//alert (nextnum);
		
		$(this).parent().parent().before('<tr><th style="width:300px;">Изображения:</th>				<td><div style="float: left;"><img data-src="" src="" width="115px" /><div><input type="hidden" name="ztumetabox_number[0]" class="ztumetabox_number" value="' + nextnum + '" /><input type="hidden" name="ztumetabox_photo[' + nextnum + ']" id="ztumetabox_photo[' + nextnum + ']" value="" /><button type="submit" class="upload_image_button button">Загрузить</button><button type="submit" class="remove_image_button button">&times;</button></div></div></td></tr>');

		return false;
	});
});