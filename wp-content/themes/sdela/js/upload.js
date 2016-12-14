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
		
		defimg = $('#default_img').val();
		
		$(".image-wrapper > .col-md-3").last().after('<div class="col-md-3 col-sm-4 col-xs-6"><img data-src="' + defimg + '" src="' + defimg + '" width="126px" /><div><input type="hidden" name="ztumetabox_number[' + nextnum + ']" class="ztumetabox_number" value="' + nextnum + '" /><input type="hidden" name="ztumetabox_photo[' + nextnum + ']" id="ztumetabox_photo[' + nextnum + ']" value="" /><button type="submit" class="upload_image_button button btn btn-default image-buttons">Загрузить</button><button type="submit" class="remove_image_button button btn btn-default image-buttons">&times;</button></div></div>');
		

		return false;
	});
});

jQuery(document).ready(function($){
	'use strict';
	// настройки по умолчанию. Их можно добавить в имеющийся js файл, 
	// если datepicker будет использоваться повсеместно на проекте и предполагается запускать его с разными настройками
	$.datepicker.setDefaults({
		closeText: 'Закрыть',
		prevText: '<Пред',
		nextText: 'След>',
		currentText: 'Сегодня',
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		weekHeader: 'Нед',
		dateFormat: 'dd-mm-yy',
		firstDay: 1,
		showAnim: 'slideDown',
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	} );
		// Инициализация
	$('.datepicker').datepicker({ dateFormat: 'dd/mm/yy' });
	// можно подключить datepicker с доп. настройками так:
	/*
	$('input[name*="date"]').datepicker({ 
		dateFormat : 'yy-mm-dd',
		onSelect : function( dateText, inst ){
// функцию для поля где указываются еще и секунды: 000-00-00 00:00:00 - оставляет секунды
var secs = inst.lastVal.match(/^.*?\s([0-9]{2}:[0-9]{2}:[0-9]{2})$/);
secs = secs ? secs[1] : '00:00:00'; // только чч:мм:сс, оставим часы минуты и секунды как есть, если нет то будет 00:00:00
$(this).val( dateText +' '+ secs );
		}
	});
	*/          
});