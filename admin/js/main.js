$(document).ready(function () {
	 
 	$(".ts-sidebar-menu li a").each(function () {
 		if ($(this).next().length > 0) {
 			$(this).addClass("parent");
 		};
 	})
 	var menux = $('.ts-sidebar-menu li a.parent');
 	$('<div class="more"><i class="fa fa-angle-down"></i></div>').insertBefore(menux);
 	$('.more').click(function () {
 		$(this).parent('li').toggleClass('open');
 	});
	$('.parent').click(function (e) {
		e.preventDefault();
 		$(this).parent('li').toggleClass('open');
 	});
 	$('.menu-btn').click(function () {
 		$('nav.ts-sidebar').toggleClass('menu-open');
 	});

	if ($.fn.DataTable) {
		$.extend( true, $.fn.dataTable.defaults, {
			"language": {
				"sEmptyTable": "Nenhum registo encontrado",
				"sInfo": "A mostrar de _START_ até _END_ de _TOTAL_ registos",
				"sInfoEmpty": "A mostrar 0 até 0 de 0 registos",
				"sInfoFiltered": "(Filtrado de _MAX_ registos no total)",
				"sInfoPostFix": "",
				"sInfoThousands": ".",
				"sLengthMenu": "Mostrar _MENU_ registos",
				"sLoadingRecords": "A carregar...",
				"sProcessing": "A processar...",
				"sZeroRecords": "Nenhum registo encontrado",
				"sSearch": "Pesquisa:",
				"oPaginate": {
					"sNext": "Próximo",
					"sPrevious": "Anterior",
					"sFirst": "Primeiro",
					"sLast": "Último"
				},
				"oAria": {
					"sSortAscending": ": Ordenar colunas de forma ascendente",
					"sSortDescending": ": Ordenar colunas de forma descendente"
				}
			}
		});
	}

	if ($('#zctb').length) {
		$('#zctb').DataTable();
	}

	$("#input-43").fileinput({
		showPreview: false,
		allowedFileExtensions: ["zip", "rar", "gz", "tgz"],
		elErrorContainer: "#errorBlock43"
	});

 });
