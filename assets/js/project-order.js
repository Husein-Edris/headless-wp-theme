/**
 * Drag-and-drop ordering for the Projects admin list.
 *
 * Makes the list-table rows sortable; on drop it sends the new order of post
 * IDs to admin-ajax, which writes each project's menu_order. The headless
 * frontend then reads that order via WPGraphQL.
 *
 * @package HeadlessPro
 */
(function ($) {
	'use strict';

	var cfg = window.headlessProProjectOrder || {};
	var $list = $('#the-list');

	if (!$list.length || !cfg.ajaxUrl) {
		return;
	}

	function collectOrder() {
		var ids = [];
		$list.children('tr').each(function () {
			// WP list-table rows use id="post-123".
			var match = /post-(\d+)/.exec(this.id || '');
			if (match) {
				ids.push(match[1]);
			}
		});
		return ids;
	}

	function persistOrder() {
		var ids = collectOrder();
		if (!ids.length) {
			return;
		}

		$list.addClass('hp-reordering');

		$.post(cfg.ajaxUrl, {
			action: cfg.action,
			nonce: cfg.nonce,
			order: ids
		})
			.always(function () {
				$list.removeClass('hp-reordering');
			})
			.fail(function () {
				window.alert('Could not save the new project order. Please reload and try again.');
			});
	}

	$list.sortable({
		items: '> tr',
		axis: 'y',
		cursor: 'move',
		opacity: 0.7,
		placeholder: 'hp-sort-placeholder',
		helper: function (event, ui) {
			// Keep cell widths while dragging so the row doesn't collapse.
			ui.children().each(function () {
				$(this).width($(this).width());
			});
			return ui;
		},
		update: persistOrder
	});
})(jQuery);
