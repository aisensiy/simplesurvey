window.TemplateEngine = {
	format : function(tmp, params) {
		return AceTemplate.format(tmp, params);
	}
};
//$(function() {

window.SurveyBase = Backbone.View.extend({
	initialize: function() {
	},
	tooltip_hook: function(options, element) {
		$elem = this.$(element);
		var default_val = {
			className: 'tip-twitter',
			content: function(callback) {
				return $(this).data('title');
			},
			timeOnScreen: 1500,
			alignX: 'left',
			alignY: 'center',
			alignTo: 'target',
			liveEvents: true,
			offsetX: 10,
			slide: false
		};
		$.extend(default_val, options);
		var options = default_val;
		$elem.poshytip(options).poshytip('enable');
		//console.log($elem);
		//希望有一种通用额方式可以关闭这个tooltip
		(function($el, $elem) {
			$el.one('off_tip', function() {
				console.log(['one', $elem[0]]);
				$elem.poshytip('disable');
			});
		})(this.$el, $elem);
	},
	add_edit_panel: function(elem, funcs) {
		var $target = $(elem);
		$target.append($(TemplateEngine.format('edit_panel_tmp', funcs)));
		$panel = $target.find('.edit-panel');
		var self = this;
		//刚刚添加到dom的元素
		(function($panel) {
			var handler = setInterval(function() {
				var height = $panel.height(), width = $panel.width();
				//console.log([height, width]);
				$panel.css({
					top: -height/2,
					right: -10 
				});
				if(width != 0) {
					clearInterval(handler);
					$panel.hide();
				}
			}, 50);
			setTimeout(function() {
				clearInterval(handler);
				$panel.hide();
			}, 1000);	
		})($panel);
	},
	show_off_edit_panel: function(view, selector) {
		var self = view;
		function bind(events, selector, callback) {
			if(selector == ' ')
				self.$el.bind(events, callback);
			else
				self.$el.on(events, selector, callback);
		}
		bind('mouseenter.edit_mode', selector, function() {
			$(this).children('.edit-panel').show();
		});
		bind('mouseleave.edit_mode', selector, function() {
			$(this).children('.edit-panel').hide();
		});
	},
	edit_mode: function(selectors) {
		var self = this;
		for(var selector in selectors) {
			var entity = selectors[selector];
			var $target = (selector == ' ' ? this.$el : this.$(selector)); 
			$target.each(function(index, elem) {
				if($(elem).css('position') == 'static' || $(elem).css('position') == '') {
					$(elem).css('position', 'relative');
				}
				if(entity.funcs && entity.funcs.length) {
					self.add_edit_panel(elem, entity.funcs);
				}
			});
			this.show_off_edit_panel(this, selector);
			if(entity.editable !== false) {(function(selector, options) {
					var namespace = options.namespace ? '.' + options.namespace : '';
					self.$el.on('dblclick.edit_mode'+namespace, selector, function() {
						$(this).editable(options);
					});
					
					var tips = self.$(selector).data('title');
					if(!tips) tips = '双击这里进行编辑';
					//console.log([selector, tips]);
					//$(selector).attr('title', tips);
					$(selector).attr('data-title', tips);
					//console.log(1);
					self.tooltip_hook(options.tooltip || {}, selector);
					//console.log(2);
					self.$el.on('mouseenter.edit_mode'+namespace, selector, function() {
						$(this).addClass('hover-highlight');
					});
					self.$el.on('mouseleave.edit_mode'+namespace, selector, function() {
						$(this).removeClass('hover-highlight');
					});
					
				})(selector, {removable: entity.removable, update: entity.update, remove: entity.remove, tooltip: entity.tooltip, namespace: entity.namespace});
			}
			if(entity.removable === true) {(function(selector, remove, namespace) {
					var namespace = namespace ? '.' + namespace : '';
					self.$el.on('click.edit_mode'+namespace, selector + ' .icon-remove', function() {
						if(remove) {
							//console.log(this);
							//console.log($(this).closest(selector));
							remove($(this).closest(selector));
						}
						else
							$(this).closest(selector).remove();
					});
				})(selector, entity.remove, entity.namespace);
			}
		}
	}
});
window.SurveyPanel = SurveyBase.extend({
	'events': {
		'submit form': 'get_result'
	},
	url : '/survey/save',
	el : $('#survey_panel'),
	error: function(msg) {
		var $error = this.$('form>.alert');
		$error.text(msg).show();
		
		setTimeout(function() {
			$error.slideUp();
		}, 3000);
	},
	render : function() {
		this.render_header();
		this.render_questions(this.options['editable']);
		this.$el.show('fast');
	},
	render_questions : function(editable) {
		var self = this;
		$.each(this.model.get('questions'), function(index, elem) {
			var question_view = QuestionFactory({
				model : new Question(elem),
				editable : editable,
				survey_view: self
			});
			self.questions.push(question_view);
			self.$('ul.questions').append(question_view.render().el);
		});
	},
	add_question: function(model) {
		var self = this, editable = this.options['editable'];
		var question_view = QuestionFactory({
			model : new Question(model),
			editable : editable,
			survey_view: self
		});
		self.questions.push(question_view);
		self.$('ul.questions').append(question_view.render().el);
		self.set_order();
	},
	render_header : function() {
		if(!$.trim($('head title').text())) $('head title').text(this.model.get('title'));
		this.$('header>h2').text(this.model.get('title'));
		this.$('header>div.description').text(this.model.get('description'));
	},
	initialize : function() {
		this.questions = [];
		this.$("ul.questions").empty();
		if(this.options['tag_panel']) this.tag_panel = this.options['tag_panel'];
		/*
		this.model.on('change:title', function(model, value) {
			console.log(value);
		});
		*/
		if(this.options['model'])
			this.render();
		if(this.options['editable'] == true) {
			this.frame_edit_mode();
		}
		this.id = this.model.get('id');
		this.set_order();
	},
	frame_edit_mode : function() {
		var self = this;
		SurveyBase.prototype.edit_mode.call(this, {
			'header>h2' : {
				update: function(new_text) {
					if(new_text.length > 50) {
						self.error('标题最长为50个字符');
					}
					new_text = new_text.substr(0, 50);
					self.model.set('title', new_text);
					return new_text;
				}
			},
			'header>div.description' : {
				update: function(new_text) {
					if(new_text.length > 500) {
						self.error('描述最长为500个字符');	
					}
					new_text = new_text.substr(0, 500);
					self.model.set('description', new_text);
					return new_text;
				}
			}
		});
		
		self.$("ul.questions").sortable({
			handle: 'legend',
			cursor: 'move',
			placeholder: "state-highlight",
			forcePlaceholderSize: true,
			axis: 'y',
			start: function(e, ui) {
				$(ui.placeholder).height($(ui.item).height() - 4);
			},
			update: _.bind(this.reset_order, this)
		})
		.sortable('enable')
		.find('legend').addClass('draggable');
	},
	edit_mode : function() {
		this.frame_edit_mode();
		$.each(this.questions, function(index, elem) {
			this.edit_mode && this.edit_mode();
		});
	},
	serialize : function() {
		var id = this.id;
		return {
			id : id,
			title : this.$('header>h2').text(),
			description : this.$('header>div.description').text(),
			questions: _.map(this.questions, function(elem) {return elem.serialize();})
		};
	},
	read_mode: function() {
		$.each(this.questions, function(index, elem) {
			this.read_mode && this.read_mode(); 
		});
		this.$el.trigger('off_tip');
		this.$('*').attr('title', '');
		this.$el.off('.edit_mode');
		this.$('.edit-panel').remove();
		$("ul.questions").sortable('disable')
		.find('legend').removeClass('draggable');

	},
	set_order: function() {
		this.$('ul.questions>li').each(function(i) {
			$(this).data('order', i);
		});
	},
	reset_order: function() {
		var self = this;
		var current_order = [];
		self.$('ul.questions>li').each(function(i) {
			current_order.push($(this).data('order'));
		});
		var new_questions = [];
		$.each(current_order, function(index, elem) {
			new_questions[index] = self.questions[elem];
		});
		self.questions = new_questions;
		self.set_order();
	},
	save: function(url) {
		url = url || '/index.php/survey/save';
		var o = this.serialize(), is_valid = true;

		if(!o.questions.length) is_valid = false;
		if(!is_valid) {
			alert('不能保存这样的问卷');
			return;
		}
		var obj = {};
		obj.json = JSON.stringify(this.serialize());

		if(this.tag_panel) 
			obj.tags = JSON.stringify(this.tag_panel.serialize());
		$.post(url, obj, function(data) {
			if(!data.success) {
				alert('保存失败了');
				return;
			}
			alert('保存成功');
			window.location = '/index.php/survey/edit/' + data.success;
		}, 'json');
	},
	get_value: function() {
		var vals = this.$('form').serializeArray();
		if(!vals.length) return false;
		var result = {};
		$.each(vals, function(index, elem) {
			if(elem.name && elem.value) {
				if(result[elem.name]) {
					if(result[elem.name] instanceof Array) result[elem.name].push(elem.value);
					else result[elem.name] = [result[elem.name], elem.value]; 
				} else {
					result[elem.name] = elem.value;
				}
			} 
		});
		return result;
	},
	get_result: function(e) {
		var self = this;
		e.preventDefault();
		var url = '/index.php/survey/answer/' + this.model.id;
		var result = this.get_value();
		if(result === false) {
			alert('不能提交空的问卷~');
			return;
		}
		$.getJSON(url, {'json': JSON.stringify(result)}, function(data) {
			if(data.success) {
				window.location = '/survey/message';
				//self.$(':submit').attr('disabled', true);
			} else {
				alert('提交失败...');
			}
		});	
	}
});

SurveyPanel.fetch = function(url, callback) {
	url = url || this.url;
	$.getJSON(url, function(data) {
		callback && callback(data);
	});
};
/**
 * 所有问题的view
 * 我没有最继承，感觉那样太空洞了，就用了switch
 */
window.QuestionView = SurveyBase.extend({
	tagName : 'li',
	/**
	 * 对于每个问题都有的统一的操作就用这种方式静态添加
	 */
	events : {
		'click.edit_mode fieldset + .edit-panel .icon-remove' : 'remove'
		/*
		'click fieldset>legend .icon-arrow-up' : 'to_top',
		'click fieldset>legend .icon-chevron-up' : 'to_prev',
		'click fieldset>legend .icon-chevron-down' : 'to_after',
		'click fieldset>legend .icon-arrow-down' : 'to_bottom'
		*/
	},
	initialize : function() {
		this.survey_view = this.options['survey_view'];
		this.get_template();
		this.edit_panel = 'fieldset+.edit-panel';
	},
	/**
	 * 加载模版时是依据model的type来加载的
	 * 所以model的type的命名一定要和template的id相对应，不可随便更改
	 */
	get_template : function() {
		var match = /^(\w+)-?(\w+)?$/i.exec(this.model.get('type'));
		if(!match[2])
			this.template = match[1] + '_tmp';
		else
			this.template = match[2] + '_tmp';
	},
	edit_mode : function() {
		SurveyBase.prototype.edit_mode.call(this, {
			' ': {funcs: ['remove'], editable: false},
			'fieldset>legend': {editable: true}
		});
		this.$('fieldset>legend').addClass('draggable');
		//this.$el.on('click.edit_mode', 'fieldset>.edit-panel .icon-remove', $.proxy(this.remove, this));
		this.delegateEvents(this.events());
	},
	read_mode: function() {
		this.$el.trigger('off_tip');
		this.$el.off('.edit_mode');
		this.$('.edit-panel').remove();
	},
	render : function() {
		this.$el.html(TemplateEngine.format(this.template, this.model.toJSON()));
		var self = this;
		if(this.options['editable'] == true) {
			setTimeout(function() {
				self.edit_mode();
			}, 100);
		}
		return this;
	},
	error: function(msg) {
		var $error = this.$el.children('.alert');
		$error.text(msg).show();
		
		setTimeout(function() {
			$error.slideUp();
		}, 3000);
	},
	//===================================================================
	//以下的操作比较简单，就不详细说明了
	remove : function(e) {
		var result = confirm('你确定要删除这个问题么？');
		if(result) {
			for(var i=0, n=this.survey_view.questions.length; i<n; i++)
				if(this.survey_view.questions[i] === this)
					this.survey_view.questions.splice(i, 1);
			this.$el.remove();
		}
	},
	//===================================================================
	//Discarded
	to_prev : function() {
		this.$el.insertBefore(this.$el.prev(this.tagName));
		this.$(this.edit_panel).hide();
	},
	to_after : function() {
		this.$el.insertAfter(this.$el.next(this.tagName));
		this.$(this.edit_panel).hide();
	},
	to_top : function() {
		this.$el.prependTo(this.$el.parent());
		this.$(this.edit_panel).hide();
	},
	to_bottom : function() {
		this.$el.appendTo(this.$el.parent());
		this.$(this.edit_panel).hide();
	},
	//=====================================================================
	serialize: function() {
		return {
			id: this.model.id,
			type: this.model.get('type'),
			content: this.$('legend').text()
		}
	}
});

window.QuestionView.getType = function(type) {
	var match = /^(\w+)-?(\w+)?$/i.exec(type);
	if(!match[2])
		return match[1];
	else
		return match[2];
};
window.QuestionFactory = function(opt) {
	String.prototype.capitalize = function() {
		return this.charAt(0).toUpperCase() + this.slice(1).toLowerCase();
	}
	if(!opt.model) return;
	var model = opt.model;
	var type = QuestionView.getType(model.get('type'));
	return new QuestionViews[type.capitalize() + 'View'](opt);
};
window.QuestionViews = window.QuestionViews || {};
window.QuestionViews.MatrixView = QuestionView.extend({
	events: function() {
		return _.extend({
			'click.edit_mode table>thead td:not(:empty) .icon-remove': 'del_col',
			'click.edit_mode table>thead td:not(:empty) .icon-plus': 'add_col',
			'click.edit_mode table>tbody>tr th .icon-remove': 'del_row',
			'click.edit_mode table>tbody>tr th .icon-plus': 'add_row'
		}, QuestionView.prototype.events);
	},
	edit_mode: function() {
		QuestionView.prototype.edit_mode.call(this);
		SurveyBase.prototype.edit_mode.call(this, {
			'table>thead td:not(:empty)>div' : {
				funcs : ['plus', 'remove'],
				tooltip: {alignX: 'center', alignY: 'top', 'offsetY': 0}
			},
			'table>tbody>tr th>div' : {
				funcs : ['plus', 'remove']
			},
		});
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
				$(this).css('border-width', '1px');
			});
			return ui;
		};

		this.$('table tbody').sortable({
			helper : fixHelper,
			placeholder : "state-highlight",
			forcePlaceholderSize : true,
			start: function(e, ui) {
				$(ui.placeholder).height($(ui.item).height());
			},
			stop: function(e, ui) {
				ui.item.children().each(function() {
					console.log(this);
					$(this).width('auto');
				});
			}
		}).sortable('enable')
		.find('th>div').addClass('draggable');
	},
	read_mode: function() {
		QuestionView.prototype.read_mode.call(this);
		this.$('table tbody').sortable('disable')
		.find('th>div').removeClass('draggable');;
	},
	del_col: function(e) {
		if($(e.target).closest('tr').children('td').size() <= 3) {
			this.error('至少应当有两个选项存在');
			return;
		}
		var td = $(e.target).closest('td')[0];
		var index = 0;
		$(td).closest('tr').children('td').each(function(ind, elem) {
			if(elem === td) index = ind;
		});
		selector = $.format('table tr>td:nth-child(#{col})', {col: index + 1});
		this.$(selector).remove();
	},
	del_row: function(e) {
		if($(e.target).closest('tbody').find('th').size() <= 2) {
			this.error('至少应当有两个问题存在');
			return;
		}
		var th = $(e.target).closest('th')[0];
		this.$(th).closest('tr').remove();
	},
	add_col: function(e) {
		var td = $(e.target).closest('td')[0];
		var index = 0;
		$(td).closest('tr').children('td').each(function(ind, elem) {
			if(elem === td) index = ind;
		});
		selector = $.format('table tr>td:nth-child(#{col})', {col: index + 1});
		this.$(selector).each(function(index, elem) {
			$(elem).clone().insertAfter(elem);
		});
	},
	add_row: function(e) {
		var th = $(e.target).closest('th')[0];
		var $tr = this.$(th).closest('tr'); 
		$tr.clone().insertAfter($tr);
	},
	serialize: function() {
		var entity = QuestionView.prototype.serialize.call(this);
		_.extend(entity, {
			vals: _.map(this.$('table>thead td:not(:empty)'), function(elem) {return $.trim($(elem).text());}),
			questions: _.map(this.$('table>tbody>tr th'), function(elem) {return [$.trim($(elem).text()), $(elem).next('td').find('input').attr('name').replace(/^field/, '')];})
		});
		return entity;
	}
});

window.QuestionViews.ChoiceView = QuestionView.extend({
	events: function() {
		return _.extend({
			'click li:not(.other) .icon-plus': 'add_choice'
		}, QuestionView.prototype.events); 
	},
	edit_mode: function() {
		var self = this;
		QuestionView.prototype.edit_mode.call(this);
		SurveyBase.prototype.edit_mode.call(this, {
			'fieldset>ul.choice>li:not(.other)' : {
				funcs : ['plus', 'remove'],
				removable : true,
				remove: function(elem) {
					if(self.validate()) $(elem).remove();
					else {
						self.error('至少应当有两个选项存在');
					}
				}
			},
			'fieldset>ul.choice>li.other' : {
				funcs : ['remove'],
				removable : true,
				remove: function(elem) {
					if(self.validate()) {
						$(elem).remove();
						self.model.set('other', false);
					}
					else {
						self.error('至少应当有两个选项存在');
					}
				}
			}
		});
		this.$("ul.choice").sortable({
			helper : function(e, ui) {
				$(this).children().each(function() {
					$(this).height($(this).height(true));
				});
				$(this).css('border-width', '1px');
				return ui;
			},
			items: 'li:not(.other)',
			handle: 'label',
			placeholder : "state-highlight",
			forcePlaceholderSize : true,
			axis: 'y',
			start: function(e, ui) {
				$(ui.placeholder).height($(ui.item).height() - 4);
			}
		}).sortable('enable')
		.children(':not(.other)').find('label').addClass('draggable');
	},
	read_mode: function() {
		QuestionView.prototype.read_mode.call(this);
		this.$("ul.choice").sortable('disable')
		.children().find('label').removeClass('draggable');;
	},
	add_choice: function(e) {
		$li = $(e.target).closest('li');
		$li.clone().insertAfter($li);
	},
	validate: function() {
		return this.serialize().choices.length > 2;
	},
	serialize: function() {
		var entity = QuestionView.prototype.serialize.call(this);
		_.extend(entity, {
			choices: _.map(this.$('fieldset>ul.choice>li'), function(elem) {
				return {
					'content': $.trim($('label', elem).text()),
					'other': $(elem).hasClass('other') && $(elem).has('input[type=text]').size() > 0
				}}
			)
		});
		entity.other = this.model.get('other');
		return entity;
	}
});

window.QuestionViews.DropdownView = QuestionView.extend({
	events: function() {
		return _.extend({
			'click li .icon-plus': 'add_choice'
		}, QuestionView.prototype.events); 
	},
	add_choice: function(e) {
		$li = $(e.target).closest('li');
		$li.clone().insertAfter($li);
	},
	bind_event: function(to_edit, to_normal) {
		var self = this;
		//绑定双击事件
		
		this.$el.on('click.edit_mode', 'div.choice:has(select)', function() {
			var last = $.data(this, 'lastclick');
			if(last && (+new Date() - last) < 500) {
		        to_edit(self);
		        $.data(this, 'lastclick', '');
		    }
		    else if(!last || (last && (+new Date() - last) >= 500))
		        $.data(this, 'lastclick', (+new Date()));
		});
		/*
		this.$el.on('dblclick.edit_mode', 'div.choice:has(select)', function() {
			to_edit(self);
		});
		*/
		this.$el.on('click.edit_mode', '.choice .icon-ok', function() {
			to_normal(self);
		});
		//this.$('select').attr('title', "双击这里进行编辑");
		if(/Safari/i.test(navigator.userAgent) && !/chrome/i.test(navigator.userAgent) && /single/i.test(this.model.get('type'))) {
			this.$('select').attr('data-title', "双点击下拉选框右侧进行编辑");		
		}
		else this.$('select').attr('data-title', "双击这里进行编辑");
		this.tooltip_hook({}, 'select');
		this.show_off_edit_panel(this, 'div:has(select)');
	},
	to_edit: function(view) {
		view.$('select').tooltip('disable');
		var wrapper = view.$('select').parent();
		var newhtml = wrapper.html().replace(/<(\/)?([a-z]+)[^>]*>/gi, function(all, slash, tag) {
		    tag = tag.toLowerCase();
		    switch(tag) {
		        case "select": 
		            return all.replace('select', 'ul');
		            break;
		        case "option": 
		            return all.replace('option', 'li');
		            break;
		        default:
		        	return all;
		    }
		});
		wrapper.html(newhtml).children('ul').addClass('unstyled');
		$('li', wrapper).addClass("edit-mode");
		SurveyBase.prototype.edit_mode.call(view, {
			'li' : {
				funcs : ['plus', 'remove'],
				removable : true,
				remove: function(elem) {
					if($('li', wrapper).size() > 2) $(elem).remove();
					else {
						console.log(1);
						view.error('至少应当有两个选项存在');
					}
				},
				namespace: 'select'
			}
		});
		view.$('ul').sortable({
			placeholder : "state-highlight",
			forcePlaceholderSize : true,
			start: function(e, ui) {
				$(ui.placeholder).height($(ui.item).height());
			}
		}).sortable('enable')
		.children().addClass('draggable');;
		
		wrapper.css('position', 'relative').append($(TemplateEngine.format('edit_panel_tmp', ['ok'])));
		var pos = {
			right: wrapper.width() - view.$('ul').width() - wrapper.children('.edit-panel').outerWidth() - 10,
			top: -10
		};
		wrapper.children('.edit-panel').css(pos);
	},
	to_normal: function(view) {
		var wrapper = view.$('ul').parent();
		wrapper.find('.edit-panel').remove();
		//console.log(view.$el);
		view.$el.off('.select');
		//console.log(wrapper);
		var newhtml = wrapper.html().replace(/<(\/)?([a-z]+)[^>]*>/gi, 
		function(all, slash, tag) {
		    tag = tag.toLowerCase();
		    switch(tag) {
		        case "ul": 
		            return all.replace('ul', 'select');
		            break;
		        case "li": 
		            return all.replace('li', 'option');
		            break;
		        default:
		        	return all;
		    }
		});
		wrapper.html(newhtml).children('select').removeClass('unstyled');
		$('option', wrapper).removeClass("edit-mode");
		//QuestionView.prototype.edit_mode.call(view);
		//view.bind_event(view.to_edit, view.to_normal);
	},
	edit_mode: function() {
		var self = this;
		QuestionView.prototype.edit_mode.call(this);
		
		this.bind_event(this.to_edit, this.to_normal);
		/*
		function to_edit(view) {
			view.$el.trigger('off_tip');
			var wrapper = view.$('select').parent();
			var newhtml = wrapper.html().replace(/<(\/)?([a-z]+)[^>]*>/gi, function(all, slash, tag) {
			    tag = tag.toLowerCase();
			    switch(tag) {
			        case "select": 
			            return all.replace('select', 'ul');
			            break;
			        case "option": 
			            return all.replace('option', 'li');
			            break;
			        default:
			        	return all;
			    }
			});
			wrapper.html(newhtml).children('ul').addClass('unstyled');
			$('li', wrapper).addClass("edit-mode");
			SurveyBase.prototype.edit_mode.call(view, {
				'li' : {
					funcs : ['plus', 'remove'],
					removable : true,
					remove: function(elem) {
						if($('li', wrapper).size() > 2) $(elem).remove();
						else {
							console.log(1);
							self.error('至少应当有两个选项存在');
						}
					}
				}
			});
			view.$('ul').sortable({
				placeholder : "state-highlight",
				forcePlaceholderSize : true,
				start: function(e, ui) {
					$(ui.placeholder).height($(ui.item).height());
				}
			}).sortable('enable')
			.children().addClass('draggable');;
			
			wrapper.css('position', 'relative').append($(TemplateEngine.format('edit_panel_tmp', ['ok'])));
			var pos = {
				right: wrapper.width() - view.$('ul').width() - wrapper.children('.edit-panel').outerWidth() - 10,
				top: -10
			};
			wrapper.children('.edit-panel').css(pos);
		}
		function to_normal(view) {
			var wrapper = view.$('ul').parent();
			wrapper.find('.edit-panel').remove();
			view.$el.off('.edit_mode');
			console.log(wrapper);
			var newhtml = wrapper.html().replace(/<(\/)?([a-z]+)[^>]*>/gi, 
			function(all, slash, tag) {
			    tag = tag.toLowerCase();
			    switch(tag) {
			        case "ul": 
			            return all.replace('ul', 'select');
			            break;
			        case "li": 
			            return all.replace('li', 'option');
			            break;
			        default:
			        	return all;
			    }
			});
			wrapper.html(newhtml).children('select').removeClass('unstyled');
			$('option', wrapper).removeClass("edit-mode");
			view.bind_event();
			
			SurveyBase.prototype.edit_mode.call(view, {
				'li' : {
					funcs : ['remove'],
					removable : true
				}
			});
			
		}
		*/
	},
	read_mode: function() {
		this.$('.icon-ok').triggerNative('click');
		QuestionView.prototype.read_mode.call(this);
	},
	serialize: function() {
		this.$('.icon-ok').triggerNative('click');
		this.$el.trigger('off_tip');
		var entity = QuestionView.prototype.serialize.call(this);
		_.extend(entity, {
			choices: _.map(this.$('fieldset select>option'), function(elem) {return $.trim($(elem).text());})
		});
		return entity;
	}
});

window.QuestionViews.TextView = QuestionView.extend({
	events: function() {
		return QuestionView.prototype.events;
	},
	edit_mode: function() {
		QuestionView.prototype.edit_mode.call(this);
	}
});

window.ControlPanel = Backbone.View.extend({
	el : $('#control_panel'),

	events : {
		'click a.create-btn' : 'add_question',
		'click a.mode' : 'mode_change',
		'click a.save' : 'save'
	},
	
	initialize : function() {
		this.survey_panel = this.options['survey_panel'];
	},
	
	add_question : function(e) {
		e.preventDefault();
		var type = $(e.target).attr('data-type');
		console.log(type);
		this.survey_panel.add_question(window.question_sample[type]);
	},
	
	mode_change: function(e) {
		e.preventDefault();
		var $this = $(e.target);
		var type = $this.data('mode');
		switch(type) {
			case 'edit-mode':
				this.survey_panel.read_mode();
				this.survey_panel.options['editable'] = false;
				$this.data('mode', 'read-mode');
				$this.text('编辑模式');
				break;
			case 'read-mode':
				this.survey_panel.edit_mode();
				this.survey_panel.options['editable'] = true;
				$this.data('mode', 'edit-mode');
				$this.text('预览模式');
				break;
		}
	},
	save: function(e) {
		e.preventDefault();
		this.survey_panel.save();
	}
});

window.ResultView = Backbone.View.extend({
	el: $('#result_container'),
	template: 'result_tmp',
	text_template: 'result_text_tmp',
	events: {
		'click :checkbox': 'fetch'
	},
	initialize: function() {
		if(!this.model) this.fetch();
		else this.render();
	},

	render: function() {
		$('head title').text(this.model.get('title'));
		this.$('h2.title').text(this.model.get('title'));
		this.render_sections(this.model.get('result'));
	},

	render_sections: function(result) {
		console.log(result);
        this.$('section').remove();
		var self = this;
		$.each(result, function(key, elem) {
			elem.name = key;
			if(/text|other/i.test(elem.type)) 
				$(TemplateEngine.format(self.text_template, elem)).appendTo(self.$el);	
			else
				$(TemplateEngine.format(self.template, elem)).appendTo(self.$el);
		});
	},

	filter: function() {
		var result = {};
		this.$(':checkbox:checked').each(function() {
			var k = $(this).attr('name'), v = $(this).val();
			if(result[k]) result[k].push(v);
		    else result[k] = [v];
		});
		this._filter = result;
		return result;
	},

	set_filter: function() {
		var self = this;
		if(!this._filter) return;
		var $checkboxes = this.$(':checkbox');
		$checkboxes.closest('td').removeClass('checked');
		for(var field in this._filter) {
			if(this._filter[field] instanceof Array) {
				$.each(this._filter[field], function(index, elem) {
					var selector = '[name=#{name}][value=#{value}]'.replace(/#{(\w+)}/g, function(match, index) {
						if(index == 'name') return field;
						else if(index == 'value') return elem;
					});
					$checkboxes.filter(selector).attr('checked', true).closest('td').addClass('checked');
				});
			} else {
				var selector = '[name=#{name}][value=#{value}]'.replace(/#{(\w+)}/g, function(match, index) {
					if(index == 'name') return field;
					else if(index == 'value') return self._filter[field];
				});
				$checkboxes.filter(selector).attr('checked', true).closest('td').addClass('checked');
			}
			
		}
	},

	fetch: function() {
		var self = this;
		var id = /\d+$/.exec(location.pathname)[0];
		var url = '/index.php/result/index/' + id;
		console.log(id);
		$.getJSON(url, {'filter': JSON.stringify(this.filter())}, function(data) {
			self.model = new Result(data);
			self.render();
			self.set_filter();
		});
	}

});

window.LoginView = Backbone.View.extend({
	el: $('.login-panel'),
	error: function(msg) {
		var self = this;
		self.$('#error').find('.alert').text(msg).end().show();
		
		setTimeout(function() {
			self.$('#error').slideUp();
		}, 3000);
	},
	initialize: function() {
		var self = this;
		this.$('form').validate({
			errorPlacement: function() {},
			rules: {
				email: {
					required: true,
					email: true
				},
				password: 'required'
			},
			submitHandler: function(form) {
				$.post(form.action, $(form).serialize(), function(data) {
					data = JSON.parse(data);
					if(data.success) {
						location = '/';
					} else {
						self.error('账号和密码不匹配');
					}
				});
			}
		});
	}
});

window.SignupView = Backbone.View.extend({
	el: $('.signup-panel'),
	initialize: function() {
		var self = this;
		this.$msg = this.$('#error');
		this.$('form').validate({
			//debug: true,
			onkeyup: false,
			rules: {
				email: {
					required: true,
					email: true,
					//remote: {
					//	
					//}
				},
				password: 'required',
				repassword: {
					required: true,
					equalTo: '[name=password]'
				},
				nickname: 'required'
			},
			messages: {
				email: {
					required: '',
					email: '请在这里填写一个邮箱地址'
				},
				password: '',
				repassword: {
					required: '',
					equalTo: '两次密码输入不一致'
				},
				nickname: ''
			},
			submitHandler: function(form) {
				$.post(form.action, $(form).serialize(), function(data) {
					data = JSON.parse(data);
					if(data.success) {
						location = '/';
					} else {
						self.error(data.msg);
					}
				});
			}
		});
	},
	error: function(msg) {
		var self = this;
		var $error = self.$msg.find('.alert');
		$error.text(msg);
		self.$msg.show();
		
		setTimeout(function() {
			self.$msg.slideUp();
		}, 3000);
	}
});

window.NaviBarView = Backbone.View.extend({
	el: $('body>.navbar'),
	url: '/index.php/auth/is_login',
	profile_template: 'profile_tmp',
	initialize: function() {
		this.$navs = this.$('ul.nav>li');
		var self = this;
		$.getJSON(this.url, {rand: +new Date()}, function(data) {
			if(data.success == false) {
				self.$navs.filter('.login').hide();
			} else {
				self.$navs.filter('.logout').hide();
				self.render_profile(data.data);
			}
			self.active_link();
			self.$el.show('normal');
		});
	},
	render_profile: function(data) {
		$profile = this.$navs.filter('.profile');
		$profile.html(TemplateEngine.format(this.profile_template, data));
	},
	active_link: function() {
		var path = location.pathname;
		this.$navs.find('a').each(function(index, elem) {
			if($(elem).attr('href') == path) $(elem).closest('li').addClass('active');
			else $(elem).closest('li').removeClass('active');
		});
	}
});
	

window.SurveyListView = Backbone.View.extend({
	el: $('ul.survey-list'),
	events: {
		'click .update-status': 'update_status',
		'click .delete': 'delete'
	},
	update_status: function(e) {
		e.preventDefault();
		var result = confirm('你确定要这么做么？');
		if(!result) return;
		var url = $(e.target).attr('href'),
			status = $(e.target).data('status');
		$.post(url, {val: status}, function(data) {
			data = JSON.parse(data);
			if(data.success) location.reload();
			else alert('failed');
		});
	},
	delete: function(e) {
		e.preventDefault();
		var result = confirm('这个操作将不可逆的删除问卷以及其全部结果，\n你真的要这么做么？');
		if(!result) return;
		$.post(e.target.href, function(data) {
			if(data.success) {
				$(e.target).closest('.survey-list>li').remove();
			} else {
				alert('删除失败了');
			}
		}, 'json');
	}
});

window.SurveyTagPanel = Backbone.View.extend({
	el: $('#tag-panel'),
	template: "<span><span class='del'><a href='javascript: void(0);'>&times;</a></span><span class='value'>#{this}</span></span>",
	_read_tmp: "<span><span class='value'>#{this}</span></span>",
	initialize: function() {
		this.tags = this.$('.tags');
		this.input = this.$('#add-tag');
		this.btn = this.$('#add-tag-btn');
		if(this.options['mode'] == 'read') {
			this.read_mode();
		}
	},
	read_mode: function() {
		this.template = this._read_tmp;
		this.input.parent().remove();
		this.$('h3').remove();
	},
	events: {
		'click #add-tag-btn': 'add_tag',
		'click .tags .del': 'del_tag',
		'keypress #add-tag': 'enter_add_tag'
	},
	add_tag: function(e) {
		var tag = $.trim(this.input.val());
		if(!tag) return;
		this.tags.append(TemplateEngine.format(this.template, tag));
		this.input.val('');
	},
	enter_add_tag: function(e) {
		console.log(e);
		if(e.keyCode == 13) {
			this.btn.click();
		}
	},
	del_tag: function(e) {
		$(e.target).parent().parent().remove();
	},
	fetch: function($tags) {
		var self = this;
		this.tags.empty();
		if($tags.length)
			$.each($tags, function() {
				self.tags.append(TemplateEngine.format(self.template, this));
			});
	},
	serialize: function() {
		var result = [];
		this.tags.find('.value').each(function() {
			result.push($.trim($(this).text()));
		});
		return result;
	}
});
//});