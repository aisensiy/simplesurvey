(function($) {
	$.fn.triggerNative = function(event) {
      this.each(function() {
        if (document.createEventObject) {
          this.fireEvent("on" + event);
        } else {
          var e;
          var isMouseEvent = "click mousedown mouseup mouseover mousemove mouseout".indexOf(event) !== -1;
          if (isMouseEvent) {
            e = document.createEvent("MouseEvents");
            e.initMouseEvent(event, true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
          } else {
            e = document.createEvent("HTMLEvents");
            e.initEvent(event, true, true);
          }
          this.dispatchEvent(e);
        }
      });
      return this;
    };
    
	$.format = function(str, param) {
	    return str.replace(/#{(\w+)}/gi, function(all, one) {
	        return param[one.toLowerCase()];
	    });
	};
	/**
	 * 计算额外的宽度
	 */
	$.computeExtraWidth = function(elem, boundary, side) {
		var extra = 0;
		$.each(boundary, function(i, e) {
			switch(side) {
				case 'both':
					extra += parseInt($(elem).css(e + '-left')) || 0;
					extra += parseInt($(elem).css(e + '-right')) || 0;
					break;
				case 'left':
					extra += parseInt($(elem).css(e + '-left')) || 0;
					break;
				case 'right':
					extra += parseInt($(elem).css(e + '-right')) || 0;
					break;
				default: 
					break;
			}
		});
		return extra;
	};
	/**
	 * 统一元素的宽度
	 * @param cols{int} 元素的列数
	 */
	$.fn.sameWidth = function(cols) {
		this.css('float', 'left');
		if(cols) {
			var parent_width = this.parent().width();
			var extra = ['margin', 'border', 'padding'],
				self  = this, extra_width = 0;
			extra_width = $.computeExtraWidth(self, extra, 'both');
			var width = parent_width / cols - extra_width - cols;
			return this.width(width);
		}
		var maxWidth = 0;
		this.each(function(ind, ele) {
			if($(ele).width() > maxWidth)
				maxWidth = $(ele).width();
		});
		return this.width(maxWidth);
	};
	
	/**
	 * 让该元素充满容器
	 */
	$.fn.autoWidth = function() {
		var width = this.parent().width();
		var extra = ['margin', 'border', 'padding'];
		var self  = this;
		width -= $.computeExtraWidth(self, extra, 'both');
		this.width(width);
		return this;
	};
	
	/**
	 * 为survey建立统一的编辑模式
	 */
	$.fn.editable = function(options) {
		var self = self || this,
			offset = self.offset(),
			id = 'edit-mode',
			tag = Math.ceil($(self).height()) > Math.ceil(parseFloat($(self).css('line-height'))) * 1.5 ? 'textarea' : 'input';
		//clear last input	
		$('#' + id).blur().remove();
		
		$("<" + tag + ">", {
			"type" : "text",
			"id" : id
		}).appendTo("body")
		.val($.trim($(self).text()))
		.blur(function() {
			var new_text = $('<div/>').text($(this).val()).html(),
				old_text = $.trim($('<div/>').text($(self).text()).html());
			if(new_text !== "" && new_text !== $(self).text()) {
				//hook
				if(options.update) new_text = options.update(new_text);
				$(self).html($(self).html().replace(old_text, new_text));
				
			}
			else if(options.removable === true && new_text === "") {
				//hook
				if(options.remove)
					options.remove(self);
				else
					$(self).remove();
			}
			//options.callback && options.callback();
			$(this).remove();
		});
		var extra_width = $.computeExtraWidth($('#' + id), ['border', 'padding'], 'both');
		//console.log(['padding', $(self).css('padding')]);
		//console.log(self);
		$('#' + id).css({
			"position" : "absolute",
			"padding-top" : ($(self).css('padding-top') || 0),
			"padding-bottom" : ($(self).css('padding-bottom') || 0),
			"padding-left" : ($(self).css('padding-left') || 0),
			"padding-right" : ($(self).css('padding-right') || 0),
			"line-height": $(self).css('line-height'),
			"left" : offset.left,
			"top" : offset.top,
			"font-family" : $(self).css("font-family"),
			"font-size" : $(self).css("font-size"),
			"height" : $(self).height(),
			"width" : $(self).width(),
			"text-align": $(self).css('text-align')
		})
		.focus()
		.select();
		
		return this;
	};
	
	/**
	 * 建立像bootstrap那样的在滚动下来之后，导航贴在顶部的工具条
	 */
	$.fn.autoFix = function(top) {
		var offset = this.offset(), self = this;
		var self = this;
		$(document).scroll(function() {
			$.data(self, 'left', self.css('left'));
			$.data(self, 'top', self.css('top'));
			if($(document).height() - $(window).height() > $(self).outerHeight(true)  
			   &&
			   $(window).scrollTop() + top > offset.top) {
				self.addClass('fixed');
				self.css({
					left: offset.left - self.css('margin-left') - self.css('border-left'),
					top: top
				});
			} else {
				self.removeClass('fixed');
				self.css({
					position: $.data(self, 'position'),
					left: $.data(self, 'left'),
					top: $.data(self, 'top')
				});
			}
		});
		return this;
	};

	$.fn.setfooter = function(classname) {
		var $this= $(this);
		function make_bottom() {
			if($('body').height() < $(window).height()) {
				if(classname) $this.addClass(classname);
				else {
					var style = {};
					style.position = $this.css('position');
					style.left = $this.css('left');
					style.bottom = $this.css('bottom');
					$this.data('oldstyle', style);
					$this.css({
						'position': 'absolute',
						'left': 'auto',
						'bottom': '0'
					});
				};
			} else {
				if(classname) $this.removeClass(classname);
				else if($this.data('oldstyle')) {
					$this.css($this.data('oldstyle'));
				}
			}
		}
		make_bottom();
		$(window).scroll(function() {
			make_bottom();
		});
		return this;
	}
	
})(jQuery);
