(function ($){
	
	"use strict";
	var config 			= searchliveConfig;
	
	var SearchlivePhp 	= {
		ajaxurl:			config.ajaxUrl,
		selectAll: 			'Select All',
		deselectAll: 		'Deselect All',
		selectSome: 		'Please, select some',
	};
	
	var SearchliveFilter = {
		page: 				1,
		text:				'',
		basicIDS:			[],
		posttypesIDS:		[],
		catsIDS:			[],
	};
	
    var SearchliveCore 	= {
		
		cacheElements: function(){
			this.lol = {
				mainWindow: 				$('.searchlive_content_wrap'),
				mainInput: 					$('.searchlive_form_elements input.searchlive_search'),
				resultWrap: 				$('.searchlive_result_wrap_in'),
				resultList: 				$('.searchlive_result_list'),
				pagination:					$('.searchlive_pagination'),
				filterWindow: 				$('.searchlive_filter_wrap'),
				filterTrigger:  			$('.slive_form_icon.icon_filter'),
				filterCloser:  				$('.searchlive_filter_closer'),
				iconLoader:  				$('.slive_form_icons .icon_loader'),
				iconSearch:  				$('.slive_form_icons .icon_search'),
				ddListBasic:  				$('.slive_filter_basic .slive_filter_dropdown_list'),
				ddListPosttypes:  			$('.slive_filter_posttypes .slive_filter_dropdown_list'),
				ddListCats:  				$('.slive_filter_cats .slive_filter_dropdown_list'),
				triggerDropdown:			$('.slive_filter_item_content_header'),
				selectAllBtnBasic:			$('.slive_filter_basic .slive_all_select span'),
				selectAllBtnPosttypes:		$('.slive_filter_posttypes .slive_all_select span'),
				selectAllBtnCats:			$('.slive_filter_cats .slive_all_select span'),
			};
		},
		
		
        init: function () 
		{
			var self = this;
			self.cacheElements();
			self.openSearchWindow();
			self.closeSearchWindow();
			self.fillFilter(); 		// we need to fill filter before start any action
			self.triggerFilterDropdown();
			self.changeFilterPreview('basic');
			self.changeFilterPreview('posttypes');
			self.changeFilterPreview('cats');
			self.svgConverter();
			self.startSearch();
			self.triggerFilterWindow();
			self.checkUncheckDropdownListItem();
			self.checkUncheckDropdownListAll();
        },

		
		openSearchWindow: function()
		{
			var self 		= this;
			var windowH 	= $(window).height();
			
			if(config.trigger != '')
			   {
			   		var button		= $(config.trigger);
			
					button.off().on('click', function(e){

						e.preventDefault();
						e.stopPropagation();

						if(!($('body').hasClass('searchlive_body')))
						{
							$('body').addClass('searchlive_body');
							self.lol.mainWindow.css({minHeight:windowH});
							setTimeout(function(){self.lol.mainInput.focus();}, 200);
						}

						return false;

					});
			   }
			
		},
		
		closeSearchWindow: function()
		{
			var self 		= this;
			var closer		= $('.searchlive_window_closer');
			
			closer.off().on('click', function(e){
				
				e.preventDefault();
				
				if(($('body').hasClass('searchlive_body')))
				{
					$('body').removeClass('searchlive_body');
					self.lol.mainWindow.css({minHeight:0});
					self.lol.mainInput.val('');
				}
				
				return false;
				
			});
		},
		
		
		fillFilter: function()
		{
			var self 		= this;
			var ID 			= 0;
			var basics 		= self.lol.ddListBasic.find('span.slive_selected');
			var posttypes 	= self.lol.ddListPosttypes.find('span.slive_selected');
			var cats 		= self.lol.ddListCats.find('span.slive_selected');
			
			// push all basic ids to array
			basics.each(function(){
				ID = $(this).data('id');
				SearchliveFilter.basicIDS.push(ID);
			});
			
			// push all posttypes ids to array
			posttypes.each(function(){
				ID = $(this).data('id');
				SearchliveFilter.posttypesIDS.push(ID);
			});
			
			// push all cats ids to array
			cats.each(function(){
				ID = $(this).data('id');
				if(isNaN(ID)){return;}else{SearchliveFilter.catsIDS.push(ID);}
			});
		},
		
		
		triggerFilterDropdown: function()
		{
			var self = this;
			
			// close by window click
			$(window).on('click',function(){
				if(self.lol.filterWindow.find('.slive_filter_item').hasClass('isOpen'))
					{
						self.lol.filterWindow.find('.slive_filter_item').removeClass('isOpen');
					}
			});
			
			self.lol.filterWindow.find('.slive_filter_item_content_footer').on('click', function(e){
				e.preventDefault();
				e.stopPropagation();
			});
			
			self.lol.triggerDropdown.on('click', function(e){
				e.preventDefault();
				e.stopPropagation();
				var current 	= $(this);
				var parent		= current.parents('.slive_filter_item');
				
				if(parent.hasClass('isOpen')){parent.removeClass('isOpen');}else{self.lol.filterWindow.find('.slive_filter_item').removeClass('isOpen'); parent.addClass('isOpen');}
			});
		},
		
		
		changeFilterPreview: function(type)
		{
			var self 		= this;
			var html		= '', ID, Count, Total, Name;
			
			if(type === 'basic')
				{
					ID 		= SearchliveFilter.basicIDS[0];
					Count 	= self.lol.ddListBasic.find('span.slive_selected').length;
					Total 	= self.lol.ddListBasic.find('span').length;
					Name	= self.lol.ddListBasic.find('span[data-id="'+ID+'"]').text();

					html = '<span class="slive_title">'+Name+'</span> <span class="slive_counter">'+Count+' / '+Total+'</span>';

					if(SearchliveFilter.basicIDS.length === 0)
						{
							html = SearchlivePhp.selectSome;
						}

					self.lol.ddListBasic.closest('.slive_filter_item_content_footer').siblings().find('span').html(html);
					
				}
			
			if(type === 'posttypes')
				{
					ID 		= SearchliveFilter.posttypesIDS[0];
					Count 	= self.lol.ddListPosttypes.find('span.slive_selected').length;
					Total 	= self.lol.ddListPosttypes.find('span').length;
					Name	= self.lol.ddListPosttypes.find('span[data-id="'+ID+'"]').text();

					html = '<span class="slive_title">'+Name+'</span> <span class="slive_counter">'+Count+' / '+Total+'</span>';

					if(SearchliveFilter.posttypesIDS.length === 0)
						{
							html = SearchlivePhp.selectSome;
						}

					self.lol.ddListPosttypes.closest('.slive_filter_item_content_footer').siblings().find('span').html(html);
					
					
					// enable/disable categories
					var post	= self.lol.ddListPosttypes.find('span.slive_selected[data-id="post"]');
					var others	= self.lol.ddListPosttypes.find('span.slive_selected');
					if(post.length && others.length === 1)
						{
							self.lol.ddListCats.parents('.slive_filter_cats').css({display:'block'});
						}
					else
						{
							self.lol.ddListCats.parents('.slive_filter_cats').css({display:'none'});
						}
					
				}
			else if(type === 'cats')
				{
					var catID 		= SearchliveFilter.catsIDS[0];
					var catCount 	= SearchliveFilter.catsIDS.length;
					var catTotal 	= self.lol.ddListCats.find('span').length;
					var catName		= self.lol.ddListCats.find('span[data-id="'+catID+'"]').text();

					html = '<span class="slive_title">'+catName+'</span> <span class="slive_counter">'+catCount+' / '+catTotal+'</span>';

					if(SearchliveFilter.catsIDS.length === 0)
						{
							html = SearchlivePhp.selectSome;
						}

					self.lol.ddListCats.closest('.slive_filter_item_content_footer').siblings().find('span').html(html);
				}
			
		},
		
		
		svgConverter: function()
		{
			$('img.searchlive_svg_converter').each(function(){
				var $img 		= $(this);
				var imgClass	= $img.attr('class');
				var imgURL		= $img.attr('src');
				$.get(imgURL, function(data){
					var $svg = $(data).find('svg');
					if(typeof imgClass !== 'undefined') {$svg = $svg.attr('class', imgClass+' replaced-svg');}
					$svg = $svg.removeAttr('xmlns:a');
					$img.replaceWith($svg);
				}, 'xml');
			});
		},
		
		
		triggerFilterWindow: function()
		{
			var self = this;
			
			// open/close filter window by filter trigger
			self.lol.filterTrigger.on('click', function(e)
			{
				e.preventDefault();
				e.stopPropagation();
				if(self.lol.filterWindow.hasClass('isOpen')){self.lol.filterWindow.removeClass('isOpen');}else{self.lol.filterWindow.addClass('isOpen');}
			});
			
			// close filter window by closer
			self.lol.filterCloser.on('click', function(e)
			{
				e.preventDefault();
				if(self.lol.filterWindow.hasClass('isOpen')){self.lol.filterWindow.removeClass('isOpen');}else{self.lol.filterWindow.addClass('isOpen');}
			});
			
			// close filter window by click on main window
			self.lol.mainWindow.on('click', function(){if(self.lol.filterWindow.hasClass('isOpen')){self.lol.filterWindow.removeClass('isOpen');}});

		},
		
		
		checkUncheckDropdownListItem: function()
		{
			var self = this;
			
			self.lol.ddListBasic.find('span').on('click', function()
			{
				var item 		= $(this);
				var itemID 		= item.data('id');
				
				if(item.hasClass('slive_selected'))
					{
						item.removeClass('slive_selected');
						SearchliveFilter.basicIDS = $.grep(SearchliveFilter.basicIDS, function(value) {return value !== itemID;	}); // remove from array
						self.lol.selectAllBtnBasic.removeClass('all_selected').html(SearchlivePhp.selectAll); // need to change select all button
					}
				else
					{
						item.addClass('slive_selected');
						SearchliveFilter.basicIDS.push(itemID); // add to array
						if(self.lol.selectAllBtnBasic.find('li').length === SearchliveFilter.basicIDS.length)
							{
								self.lol.selectAllBtnBasic.addClass('all_selected').html(SearchlivePhp.deselectAll); // need to change select all button
							}
					}
				
				SearchliveFilter.page = 1;
				self.changeFilterPreview('basic');
				self.ajaxRequest(); // call to ajax request
				
			});
			
			self.lol.ddListPosttypes.find('span').on('click', function()
			{
				var item 		= $(this);
				var itemID 		= item.data('id');
				
				if(item.hasClass('slive_selected'))
					{
						item.removeClass('slive_selected');
						SearchliveFilter.posttypesIDS = $.grep(SearchliveFilter.posttypesIDS, function(value) {return value !== itemID;	});
						self.lol.selectAllBtnPosttypes.removeClass('all_selected').html(SearchlivePhp.selectAll); // need to change select all button
					}
				else
					{
						item.addClass('slive_selected');
						SearchliveFilter.posttypesIDS.push(itemID); // add to array
						if(self.lol.selectAllBtnPosttypes.find('li').length === SearchliveFilter.posttypesIDS.length)
							{
								self.lol.selectAllBtnPosttypes.addClass('all_selected').html(SearchlivePhp.deselectAll); // need to change select all button
							}
					}
				
				SearchliveFilter.page = 1;
				self.changeFilterPreview('posttypes');
				self.ajaxRequest(); // call to ajax request
				
			});
			
			self.lol.ddListCats.find('span').on('click', function()
			{
				var cat 		= $(this);
				var catID 		= cat.data('id');
				
				if(cat.hasClass('slive_selected'))
					{
						cat.removeClass('slive_selected');
						SearchliveFilter.catsIDS = $.grep(SearchliveFilter.catsIDS, function(value) {return value !== catID;	}); // remove from array
						self.lol.selectAllBtnCats.removeClass('all_selected').html(SearchlivePhp.selectAll); // need to change select all button
					}
				else
					{
						cat.addClass('slive_selected');
						SearchliveFilter.catsIDS.push(catID); // add to array
						if(self.lol.ddListCats.find('li').length === SearchliveFilter.catsIDS.length)
							{
								self.lol.selectAllBtnCats.addClass('all_selected').html(SearchlivePhp.deselectAll); // need to change select all button
							}
					}
				
				
				SearchliveFilter.page = 1;
				self.changeFilterPreview('cats');
				self.ajaxRequest(); // call to ajax request
				
			});
		},
		
		
		checkUncheckDropdownListAll: function()
		{
			var self 		= this, ID;
			var items 		= self.lol.ddListBasic.find('span');
			var posttypes 	= self.lol.ddListPosttypes.find('span');
			var cats 		= self.lol.ddListCats.find('span');
			
			self.lol.selectAllBtnBasic.on('click', function()
			{
				var selector 		= $(this);
				
				if(selector.hasClass('all_selected'))
				{
					selector.removeClass('all_selected').html(SearchlivePhp.selectAll);
					items.removeClass('slive_selected');
					SearchliveFilter.basicIDS = []; // empty array
				}else
				{
					selector.addClass('all_selected').html(SearchlivePhp.deselectAll);
					items.addClass('slive_selected');
					
					// push all deselected basic ids to array
					items.each(function(){
						ID = $(this).data('id');
						// items those are not in the array
						if(SearchliveFilter.basicIDS.indexOf(ID) < 0){SearchliveFilter.basicIDS.push(ID);}else{return;}
					});
				}
				SearchliveFilter.page = 1;
				self.changeFilterPreview('basic');
				self.ajaxRequest(); // call to ajax request
			});
			
			
			self.lol.selectAllBtnPosttypes.on('click', function()
			{
				var selector 		= $(this);
				
				if(selector.hasClass('all_selected'))
				{
					selector.removeClass('all_selected').html(SearchlivePhp.selectAll);
					posttypes.removeClass('slive_selected');
					SearchliveFilter.posttypesIDS = []; // empty array
				}else
				{
					selector.addClass('all_selected').html(SearchlivePhp.deselectAll);
					posttypes.addClass('slive_selected');
					
					// push all deselected basic ids to array
					posttypes.each(function(){
						ID = $(this).data('id');
						// post types those are not in the array
						if(SearchliveFilter.posttypesIDS.indexOf(ID) < 0){SearchliveFilter.posttypesIDS.push(ID);}else{return;}
					});
				}
				SearchliveFilter.page = 1;
				self.changeFilterPreview('posttypes');
				self.ajaxRequest(); // call to ajax request
			});
			
			
			self.lol.selectAllBtnCats.on('click', function()
			{
				var selector 		= $(this);
				
				if(selector.hasClass('all_selected'))
				{
					selector.removeClass('all_selected').html(SearchlivePhp.selectAll);
					cats.removeClass('slive_selected');
					SearchliveFilter.catsIDS = []; // empty array
				}else
				{
					selector.addClass('all_selected').html(SearchlivePhp.deselectAll);
					cats.addClass('slive_selected');
					
					// push all deselected cats ids to array
					cats.each(function(){
						ID = $(this).data('id');
						// make sure id is number and cats those are not in the array
						if(!isNaN(ID) && SearchliveFilter.catsIDS.indexOf(ID) < 0){SearchliveFilter.catsIDS.push(ID);}else{return;}
					});
				}
				SearchliveFilter.page = 1;
				self.changeFilterPreview('cats');
				self.ajaxRequest(); // call to ajax request
			});
		},
		
		
		triggerLoader: function(open)
		{
			var self = this;
			
			if(open === 'open')
			{
				self.lol.iconLoader.addClass('isOpen');
				self.lol.iconSearch.addClass('isClose');
			}else
			{
				self.lol.iconLoader.removeClass('isOpen');
				self.lol.iconSearch.removeClass('isClose');
			}
		},
		
		
		startSearch: function()
		{
			var self = this;
			
			self.lol.mainInput.off().on('keyup', function()
			{
				SearchliveFilter.text = $(this).val();
				SearchliveFilter.page = 1;
				self.ajaxRequest(); // call to ajax request
			});
		},
		
		
		ajaxRequest: function()
		{
			var self = this;
			
			if(SearchliveFilter.text === ''){return;} // if input is empty stop ajax request
			
			self.triggerLoader('open');
			
			var requestData = {
				action: 		'searchliveAjaxLiveSearch',
				page: 			SearchliveFilter.page,
				text: 			SearchliveFilter.text,
				basicIDS: 		SearchliveFilter.basicIDS,
				posttypesIDS: 	SearchliveFilter.posttypesIDS,
				catsIDS: 		SearchliveFilter.catsIDS,
			};

			$.ajax({
				type: 'POST',
				url: SearchlivePhp.ajaxurl,
				cache:true,
				data: requestData,
				success: function(data)
				{
					self.triggerLoader('close');
					self.displayResults(data);
				},
				error: function(xhr, textStatus, errorThrown)
				{
					console.log(errorThrown);
					console.log(textStatus);
					console.log(xhr);
				}
			});
		},
		
		
		displayResults: function(data)
		{
			var self 	= this;
			var obj 	= $.parseJSON(data);
			
			//console.log(obj.console);
			
			if(obj.result.length)
			{
				self.lol.resultWrap.html(obj.result);
			}
			
			self.cacheElements();
			self.pagination();
		},
		
		pagination: function()
		{
			var self = this;
			
			// filter pagination
			self.lol.pagination.find('a').off().on('click', function(){
				
				
				var currentPage = $(this),
					li 			= currentPage.parent(),
					page 		= currentPage.data('page'),
					prevnext	= 0;
				
				if(li.hasClass('prev')){
					page 		= currentPage.parent().parent().find('li.active a').data('page') - 1;
					prevnext 	= 1;
				}else if(li.hasClass('next')){
					page 		= currentPage.parent().parent().find('li.active a').data('page') + 1;
					prevnext 	= 1;
				}
				
				// if it isn't current page
				if(!li.hasClass('active')){
					SearchliveFilter.page = page;
					self.ajaxRequest();
				}
				// this for prevand next buttons
				if(prevnext === 1){
					SearchliveFilter.page = page;
					self.ajaxRequest();
				}
				
				return false;
			});
		},

		
    };
	
	$(document).ready(function(){SearchliveCore.init();});

})(jQuery);